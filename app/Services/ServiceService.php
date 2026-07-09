<?php
namespace App\Services;

use App\Jobs\ProvisionServer;
use App\Jobs\UnsuspendServer;
use App\Models\Plan;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function __construct(
        private readonly ProvisioningService $provisioning,
    ) {}

    public function activateService(Service $service): void
    {
        $service->update(['status' => Service::STATUS_ACTIVE]);

        if (! $service->server) {
            $this->provisioning->provision($service);
        } else {
            $this->provisioning->unsuspend($service);
        }
    }

    public function suspendService(Service $service): void
    {
        $service->update(['status' => Service::STATUS_SUSPENDED]);

        if ($service->server) {
            $this->provisioning->suspend($service);
        }
    }

    public function unsuspendService(Service $service): void
    {
        $service->update(['status' => Service::STATUS_ACTIVE]);

        if ($service->server) {
            $this->provisioning->unsuspend($service);
        }
    }

    public function terminateService(Service $service): void
    {
        $service->update(['status' => Service::STATUS_CANCELLED]);

        if ($service->server) {
            $this->provisioning->terminate($service);
        }

        $service->invoices()
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
    }

    public function renewService(Service $service): void
    {
        if ($service->product && $service->product->server) {
            if ($service->status === Service::STATUS_SUSPENDED) {
                UnsuspendServer::dispatch($service);
            } elseif ($service->status === Service::STATUS_PENDING) {
                ProvisionServer::dispatch($service);
            }
        }

        $service->expires_at = $service->calculateNextDueDate();
        $service->status = Service::STATUS_ACTIVE;
        $service->save();
    }

    public function getExpiryDate(Plan $plan): Carbon
    {
        return $this->calculateNewExpiry(now(), $plan);
    }

    public function calculateNewExpiry(Carbon $currentExpiry, Plan $plan): Carbon
    {
        $startDate = $currentExpiry->isFuture() ? $currentExpiry : now();

        if ($plan->isFree() || $plan->isOneTime()) {
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

    public function getUserActiveServices(int $userId): Collection
    {
        return Service::where('user_id', $userId)
            ->where('status', Service::STATUS_ACTIVE)
            ->with(['product', 'plan', 'order'])
            ->get();
    }

    public function getExpiringServices(int $daysAhead = 3): Collection
    {
        return Service::where('status', Service::STATUS_ACTIVE)
            ->where('expires_at', '<=', now()->addDays($daysAhead))
            ->where('expires_at', '>', now())
            ->with(['user', 'product', 'plan'])
            ->get();
    }
}
