<?php
namespace App\Classes\Extension;

use App\Models\Extension as ExtensionModel;
use App\Models\Setting;

class Extension
{
    public function __construct(public array $config = [])
    {
        $this->config = $config;
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $extension = $this->resolveExtensionModel();

        if (! $extension) {
            return $default;
        }

        $setting = Setting::where('key', $key)
            ->where('settingable_type', ExtensionModel::class)
            ->where('settingable_id', $extension->id)
            ->first();

        return $setting?->value ?? $default;
    }

    public function getConfig(array $values = []): array
    {
        return [];
    }

    public function installed(): void {}

    public function uninstalled(): void {}

    public function upgraded(?string $oldVersion = null): void {}

    public function boot(): void {}

    public function enabled(): void {}

    public function disabled(): void {}

    protected function resolveExtensionModel(): ?ExtensionModel
    {
        $class = class_basename(static::class);

        return ExtensionModel::where('extension', $class)->first();
    }
}
