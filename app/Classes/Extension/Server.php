<?php
namespace App\Classes\Extension;

use App\Models\Service;

class Server extends Extension
{
    public function createServer(Service $service, array $settings, array $properties): array
    {
        return [];
    }

    public function suspendServer(Service $service, array $settings, array $properties): void {}

    public function unsuspendServer(Service $service, array $settings, array $properties): void {}

    public function terminateServer(Service $service, array $settings, array $properties): void {}

    public function upgradeServer(Service $service, array $settings, array $properties): void {}

    public function getProductConfig(array $values = []): array
    {
        return [];
    }

    public function getCheckoutConfig(object $product, array $values = []): array
    {
        return [];
    }

    public function getActions(Service $service): array
    {
        return [];
    }

    public function getView(Service $service, string $view): ?string
    {
        return null;
    }

    public function testConfig(): bool
    {
        return true;
    }
}
