¿<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Process;

class DeployController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $deployResult = null;
        if (session('deploy_output')) {
            $deployResult = [
                'status' => session('deploy_status'),
                'message' => session('deploy_message'),
                'output' => session('deploy_output'),
            ];
        }

        return view('admin.deploy.index', compact('deployResult'));
    }

    public function run()
    {
        $baseDir = base_path();
        $commands = [
            "cd $baseDir && git pull origin main 2>&1",
            "cd $baseDir && php artisan migrate --force 2>&1",
        ];

        $output = '';
        $failed = false;

        foreach ($commands as $cmd) {
            $result = Process::run($cmd);
            $output .= "> $cmd\n".$result->output().$result->errorOutput()."\n";
            if (! $result->successful()) {
                $failed = true;
            }
        }

        if (PHP_OS_FAMILY !== 'Windows') {
            $chown = Process::run("cd $baseDir && chown -R www-data:www-data storage bootstrap/cache 2>&1");
            $output .= "> chown storage bootstrap/cache\n".$chown->output().$chown->errorOutput()."\n";
            if (! $chown->successful()) {
                $failed = true;
            }
        }

        $status = $failed ? 'error' : 'success';
        $message = $failed ? 'Deploy completed with errors.' : 'Deploy completed successfully.';

        return redirect()->route('admin.deploy.index')
            ->with('deploy_status', $status)
            ->with('deploy_message', $message)
            ->with('deploy_output', $output);
    }
}
