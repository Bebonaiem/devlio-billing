<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\ServiceService;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService,
    ) {
        $this->middleware('auth');
    }

    public function index()
    {
        $services = Service::with(['user', 'product', 'plan', 'currency'])
            ->latest()
            ->paginate(20);

        return view('admin.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        $service->load([
            'user',
            'product',
            'plan.prices',
            'configs.configOption',
            'configs.configValue',
            'currency',
            'cancellations',
            'upgrades',
            'invoices' => function ($q) {
                $q->with('items')->latest();
            },
        ]);

        return view('admin.services.show', compact('service'));
    }

    public function suspend(Service $service)
    {
        $this->serviceService->suspendService($service);

        return back()->with('success', 'Service suspended successfully.');
    }

    public function unsuspend(Service $service)
    {
        $this->serviceService->unsuspendService($service);

        return back()->with('success', 'Service unsuspended successfully.');
    }

    public function terminate(Service $service)
    {
        $this->serviceService->terminateService($service);

        return back()->with('success', 'Service terminated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->configs()->delete();
        $service->cancellations()->delete();
        $service->upgrades()->delete();
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
