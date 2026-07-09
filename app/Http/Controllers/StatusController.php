<?php
namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\PterodactylService;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(PterodactylService $pterodactyl)
    {
        $user = Auth::user();

        $services = Service::where('user_id', $user->id)
            ->with(['server', 'product', 'plan'])
            ->latest()
            ->get();

        $servers = collect();

        foreach ($services as $service) {
            $serverData = [
                'service' => $service,
                'server' => $service->server,
                'status' => 'unknown',
                'resources' => null,
                'ip' => null,
                'port' => null,
            ];

            if ($service->server) {
                $serverData['ip'] = $service->server->ip;

                if ($service->server->pterodactyl_server_identifier) {
                    $resources = $pterodactyl->getServerResources(
                        $service->server->pterodactyl_server_identifier,
                        $user
                    );

                    if ($resources) {
                        $serverData['status'] = $resources['current_state'] ?? 'unknown';
                        $serverData['resources'] = [
                            'cpu_absolute' => $resources['cpu_absolute'] ?? null,
                            'memory_bytes' => $resources['memory_bytes'] ?? null,
                            'memory_limit_bytes' => $resources['memory_limit_bytes'] ?? null,
                            'disk_bytes' => $resources['disk_bytes'] ?? null,
                            'disk_limit_bytes' => $resources['disk_limit_bytes'] ?? null,
                            'uptime' => $resources['uptime'] ?? null,
                        ];
                        $serverData['port'] = $resources['allocations'] ?? null;
                    } else {
                        $serverData['status'] = $service->server->status ?? 'unknown';
                    }
                } else {
                    $serverData['status'] = $service->server->status ?? 'unknown';
                }
            } else {
                $serverData['status'] = $service->status;
            }

            $servers->push($serverData);
        }

        return view('dashboard.servers', compact('servers'));
    }
}
