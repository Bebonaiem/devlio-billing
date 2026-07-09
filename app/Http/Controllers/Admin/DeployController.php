<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class DeployController extends Controller
{
    public function index()
    {
        return view('admin.deploy.index');
    }

    public function run()
    {
        $baseDir = base_path();
        $commands = [
            "cd $baseDir && git pull origin main 2>&1",
            "cd $baseDir && php artisan migrate --force 2>&1",
            "cd $baseDir && chown -R www-data:www-data storage bootstrap/cache 2>&1",
        ];

        $output = '';
        $failed = false;

        foreach ($commands as $cmd) {
            if (str_starts_with($cmd, 'chown')) {
                if (PHP_OS_FAMILY !== 'Windows') {
                    $result = Process::run($cmd);
                    $output .= "> $cmd\n" . $result->output() . ($result->exitCode() ? $result->errorOutput() : '') . "\n";
                    if (!$result->successful()) $failed = true;
                } else {
                    $output .= "> $cmd\n(skipped on Windows)\n";
                }
            } else {
                $result = Process::run($cmd);
                $output .= "> $cmd\n" . $result->output() . ($result->exitCode() ? $result->errorOutput() : '') . "\n";
                if (!$result->successful()) $failed = true;
            }
        }

        $status = $failed ? 'error' : 'success';
        $message = $failed ? 'Deploy completed with errors.' : 'Deploy completed successfully.';

        return response()->json([
            'status' => $status,
            'message' => $message,
            'output' => $output,
        ]);
    }
}