<?php

namespace OranFry\Jars\Admin;

use OranFry\Jars\Contract\Client as JarsClient;

class ToolsConfig extends \OranFry\Tools\Config
{
    public function boot(JarsClient $jars): array
    {
        $data = [];

        if (isset($_GET['version']) && preg_match('/^[1-9][0-9]*$/', $_GET['version'])) {
            $data['version'] = intval($_GET['version']);
        }

        return $data;
    }

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
