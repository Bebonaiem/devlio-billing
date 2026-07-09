<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Service;
use Carbon\Carbon;

class ServiceService
{
    public function __construct(
        private readonly ProvisioningService $provisioning,
    ) {}

    public function activateService(Service $service): void
    {
        $service->update(['status' => 'active']);

        if (!$service->server) {
            $this->provisioning->provision($service);
        } else {
            $this->provisioning->unsuspend($service->server);
        }
    }

    public function suspendService(Service $service): void
    {
        $service->update(['status' => 'suspended']);

        if ($service->server) {
            $this->provisioning->suspend($service->server);
        }
    }

    public function unsuspendService(Service $service): void
    {
        $service->update(['status' => 'active']);

        if ($service->server) {
            $this->provisioning->unsuspend($service->server);
        }
    }

    public function terminateService(Service $service): void
    {
        $service->update(['status' => 'cancelled']);

        if ($service->server) {
            $this->provisioning->terminate($service->server);
        }
    }

    public function renewService(Service $service, float $price): void
    {
        $plan = $service->plan;

        if (!$plan) {
            return;
        }

        $currentExpiry = $service->expires_at ? Carbon::parse($service->expires_at) : now();
        $newExpiry = $this->calculateNewExpiry($currentExpiry, $plan);

        $service->update([
            'expires_at' => $newExpiry,
            'status' => 'active',
            'price' => $price,
        ]);
    }

    public function getExpiryDate(Plan $plan): Carbon
    {
        return $this->calculateNewExpiry(now(), $plan);
    }

    public function calculateNewExpiry(Carbon $currentExpiry, Plan $plan): Carbon
    {
        $startDate = $currentExpiry->isFuture() ? $currentExpiry : now();

        if ($plan->type === 'free') {
            return $startDate->copy()->addCentury();
        }

        if ($plan->type === 'one-time') {
            return $startDate->copy()->addCentury();
        }

        $period = $plan->billing_period ?? 1;
        $unit = $plan->billing_unit ?? 'month';

        return match ($unit) {
            'day' => $startDate->copy()->addDays($period),
            'week' => $startDate->copy()->addWeeks($period),
            'month' => $startDate->copy()->addMonths($period),
            'year' => $startDate->copy()->addYears($period),
            default => $startDate->copy()->addMonth(),
        };
    }

    public function getUserActiveServices($userId): \Illuminate\Database\Eloquent\Collection
    {
        return Service::where('user_id', $userId)
            ->where('status', 'active')
            ->with(['product', 'plan', 'order'])
            ->get();
    }

    public function getExpiringServices(int $daysAhead = 3): \Illuminate\Database\Eloquent\Collection
    {
        return Service::where('status', 'active')
            ->where('expires_at', '<=', now()->addDays($daysAhead))
            ->where('expires_at', '>', now())
            ->with(['user', 'product', 'plan'])
            ->get();
    }
}
