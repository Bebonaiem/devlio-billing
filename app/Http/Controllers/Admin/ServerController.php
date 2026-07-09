<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;

class ServerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $servers = Server::with(['user', 'order.plan.product'])
            ->latest()
            ->paginate(20);

        return view('admin.servers.index', compact('servers'));
    }

    public function show(Server $server)
    {
        $server->load(['user', 'order.plan.product']);

        return view('admin.servers.show', compact('server'));
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', 'Server deleted successfully.');
    }
}
