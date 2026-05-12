<?php

namespace OranFry\Jars\Admin;

class ToolsConfig extends \Tools\Config
{
    public function custom(object $config, ?string $httpMountPoint, ?string $cliMountPoint, array $options): void
    {
        define('JARS_ADMIN_BASEPATH', $httpMountPoint);
        define('JARS_ADMIN_HOMEPATH', $httpMountPoint);

        $config->ledger ??= [];

        $config->ledger += [
            'accounts' => \OranFry\Ledgers\Accounts::class,
            'balance' => \OranFry\Ledgers\Balance::class,
            'bank' => \OranFry\Ledgers\Bank::class,
            'gst' => \OranFry\Ledgers\Gst::class,
            'income' => \OranFry\Ledgers\Income::class,
        ];
    }

    public function includePath(): ?string
    {
        return 'vendor/oranfry/jars-admin';
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
