<?php

namespace PortedCheese\Backups;

use Aws\Sdk;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use PortedCheese\Backups\Console\Commands\BackupApplicationCommand;
use PortedCheese\Backups\Console\Commands\BackupDataBaseCommand;
use PortedCheese\Backups\Console\Commands\BackupStorageCommand;
use PortedCheese\Backups\Console\Commands\PullApplicationCommand;
use PortedCheese\Backups\Console\Commands\PushApplicationCommand;
use PortedCheese\Backups\Console\Commands\RestoreApplicationCommand;
use PortedCheese\Backups\Console\Commands\RestoreDataBaseCommand;
use PortedCheese\Backups\Console\Commands\RestoreStorageCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function boot()
	{
        // Console.
        $this->commands([
            BackupDataBaseCommand::class,
            RestoreDataBaseCommand::class,
            BackupStorageCommand::class,
            RestoreStorageCommand::class,
            BackupApplicationCommand::class,
            RestoreApplicationCommand::class,

            PushApplicationCommand::class,
            PullApplicationCommand::class,
        ]);

        // Подключение роутов.
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/admin.php');

        // Экспорт конфигурации.
        $this->publishes([
            __DIR__ . '/config/backups.php' => config_path('backups.php'),
        ], 'config');

        // Добавить конфигурацию для файловой системы.
        app()->config['filesystems.disks.backups'] = [
            'driver' => 'local',
            'root' => backup_path(),
        ];
        app()->config['filesystems.disks.yandex'] = [
            'driver' => "yaS3Backups",
            "key" => config("backups.keyId"),
            'secret' => config("backups.keySecret"),
            'region' => config("backups.region"),
            'bucket' => config("backups.bucket"),
        ];

        // Yandex cloud storage.
        Storage::extend("yaS3Backups", function ($app, $config) {
            $configS3 = [
                "endpoint" => "https://storage.yandexcloud.net",
                "region" => $config['region'],
                "version" => "latest",
                "credentials" => [
                    "key" => $config["key"],
                    "secret" => $config["secret"],
                ],
            ];
            if (config("app.debug")) {
                $configS3['http'] = [
                    'verify' => false
                ];
            }
            $sdk = new Sdk($configS3);
            $s3 = $sdk->createS3();

            return new Filesystem(new AwsS3V3Adapter($s3, $config['bucket']));
        });
	}

	public function register()
	{
	
	}
}