<?php

namespace App\Helpers;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use App\Classes\Extension\Gateway;
use App\Models\Extension as ExtensionModel;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\Service;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class ExtensionHelper
{
    public static function getExtension(string $type, string $extension, array $config = []): Extension
    {
        $namespace = '\\App\\Extensions\\'.ucfirst($type).'s\\'.$extension.'\\'.$extension;

        if (! class_exists($namespace)) {
            throw new \RuntimeException("Extension class not found: {$namespace}");
        }

        return new $namespace($config);
    }

    public static function call(ExtensionModel $extension, string $function, array $args = [], bool $mayFail = false): mixed
    {
        try {
            $instance = self::getExtension(
                $extension->type,
                $extension->extension,
                $extension->settings->pluck('value', 'key')->toArray()
            );

            return $instance->{$function}(...$args);
        } catch (\Throwable $e) {
            if ($mayFail) {
                Log::warning("Extension call failed: {$extension->extension}->{$function}", [
                    'error' => $e->getMessage(),
                ]);

                return null;
            }

            throw $e;
        }
    }

    public static function createServer(Service $service): ?array
    {
        $server = $service->product->server;

        if (! $server) {
            return null;
        }

        return self::call($server, 'createServer', [
            $service,
            $server->settings->pluck('value', 'key')->toArray(),
            $service->properties->pluck('value', 'key')->toArray(),
        ]);
    }

    public static function suspendServer(Service $service): void
    {
        $server = $service->product->server;

        if ($server) {
            self::call($server, 'suspendServer', [
                $service,
                $server->settings->pluck('value', 'key')->toArray(),
                $service->properties->pluck('value', 'key')->toArray(),
            ]);
        }
    }

    public static function unsuspendServer(Service $service): void
    {
        $server = $service->product->server;

        if ($server) {
            self::call($server, 'unsuspendServer', [
                $service,
                $server->settings->pluck('value', 'key')->toArray(),
                $service->properties->pluck('value', 'key')->toArray(),
            ]);
        }
    }

    public static function terminateServer(Service $service): void
    {
        $server = $service->product->server;

        if ($server) {
            self::call($server, 'terminateServer', [
                $service,
                $server->settings->pluck('value', 'key')->toArray(),
                $service->properties->pluck('value', 'key')->toArray(),
            ]);
        }
    }

    public static function upgradeServer(Service $service): void
    {
        $server = $service->product->server;

        if ($server) {
            self::call($server, 'upgradeServer', [
                $service,
                $server->settings->pluck('value', 'key')->toArray(),
                $service->properties->pluck('value', 'key')->toArray(),
            ]);
        }
    }

    public static function pay(Gateway $gateway, Invoice $invoice, float $total): void
    {
        $gateway->pay($invoice, $total);
    }

    public static function addPayment(Invoice $invoice, ExtensionModel $gateway, float $amount, float $fee, string $transactionId, string $status = 'succeeded'): InvoiceTransaction
    {
        return $invoice->transactions()->create([
            'gateway_id' => $gateway->id,
            'amount' => $amount,
            'fee' => $fee,
            'transaction_id' => $transactionId,
            'status' => $status,
        ]);
    }

    public static function addProcessingPayment(Invoice $invoice, ExtensionModel $gateway, float $amount, float $fee, string $transactionId): InvoiceTransaction
    {
        return self::addPayment($invoice, $gateway, $amount, $fee, $transactionId, 'processing');
    }

    public static function addFailedPayment(Invoice $invoice, ExtensionModel $gateway, float $amount, float $fee, string $transactionId): InvoiceTransaction
    {
        return self::addPayment($invoice, $gateway, $amount, $fee, $transactionId, 'failed');
    }

    public static function getExtensions(?string $type = null): Collection
    {
        $query = ExtensionModel::query();

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public static function getAvailableExtensions(): array
    {
        $extensions = [];

        $paths = [
            'gateways' => base_path('extensions/Gateways'),
            'servers' => base_path('extensions/Servers'),
            'others' => base_path('extensions/Others'),
        ];

        foreach ($paths as $type => $path) {
            if (! is_dir($path)) {
                continue;
            }

            foreach (glob($path.'/*') as $extensionPath) {
                $name = basename($extensionPath);
                $classPath = $extensionPath.'/'.$name.'.php';

                if (! file_exists($classPath)) {
                    continue;
                }

                $className = '\\App\\Extensions\\'.ucfirst($type).'\\'.$name.'\\'.$name;

                if (! class_exists($className)) {
                    continue;
                }

                $reflection = new ReflectionClass($className);
                $attributes = $reflection->getAttributes(ExtensionMeta::class);

                $meta = null;
                if (! empty($attributes)) {
                    $meta = $attributes[0]->newInstance();
                }

                $installed = ExtensionModel::where('extension', $name)->where('type', rtrim($type, 's'))->exists();

                $extensions[] = [
                    'name' => $name,
                    'type' => rtrim($type, 's'),
                    'class' => $className,
                    'meta' => $meta,
                    'installed' => $installed,
                ];
            }
        }

        return $extensions;
    }

    public static function getInstallableExtensions(): array
    {
        return array_filter(self::getAvailableExtensions(), fn ($ext) => ! $ext['installed']);
    }

    public static function getConfig(string $type, ?string $name, array $config = []): array
    {
        if (! $name) {
            return [];
        }

        try {
            $extension = self::getExtension($type, $name, $config);

            return $extension->getConfig($config);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function runMigrations(string $path): void
    {
        $migrator = app(Migrator::class);
        $migrator->run(base_path($path));
    }

    public static function rollbackMigrations(string $path): void
    {
        $migrationFiles = array_reverse(glob(base_path($path.'/*.php')));

        foreach ($migrationFiles as $file) {
            $migration = require_once $file;
            $migrationName = pathinfo($file, PATHINFO_FILENAME);

            if (method_exists($migration, 'down') && DB::table('migrations')->where('migration', $migrationName)->exists()) {
                $migration->down();
                DB::table('migrations')->where('migration', $migrationName)->delete();
            }
        }
    }

    public static function hasFunction(ExtensionModel $extension, string $function): bool
    {
        try {
            $instance = self::getExtension(
                $extension->type,
                $extension->extension,
                $extension->settings->pluck('value', 'key')->toArray()
            );

            return method_exists($instance, $function);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function getActions(Service $service): array
    {
        $server = $service->product->server;

        if (! $server || ! self::hasFunction($server, 'getActions')) {
            return [];
        }

        return self::call($server, 'getActions', [
            $service,
            $server->settings->pluck('value', 'key')->toArray(),
            $service->properties->pluck('value', 'key')->toArray(),
        ]) ?? [];
    }
}
