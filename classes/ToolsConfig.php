<?php

namespace OranFry\Jars\Admin;

class ToolsConfig extends \OranFry\Tools\Config
{
    public function custom(object $config, ?string $httpMountPoint, ?string $cliMountPoint, array $options): void
    {
        define('JARS_ADMIN_BASEPATH', $httpMountPoint);
        define('JARS_ADMIN_HOMEPATH', $httpMountPoint);

        $config->ledger = ($config->ledger ?? []) + [
            'report' => ReportLedger::class,
        ];
    }

    public function includePath(): ?string
    {
        return 'vendor/oranfry/jars-admin';
    }

    public function requires(): array
    {
        return ['vendor/oranfry/ledger'];
    }

    public function router(): string
    {
        return AdminRouter::class;
    }

    public function title(): string
    {
        return 'Jars Admin';
    }
}
