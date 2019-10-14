<?php

namespace PortedCheese\Backups;

use PortedCheese\Backups\Console\Commands\BackupApplicationCommand;
use PortedCheese\Backups\Console\Commands\BackupDataBaseCommand;
use PortedCheese\Backups\Console\Commands\BackupStorageCommand;
use PortedCheese\Backups\Console\Commands\RestoreDataBaseCommand;
use PortedCheese\Backups\Console\Commands\RestoreStorageCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function boot()
	{
        // Console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupDataBaseCommand::class,
                RestoreDataBaseCommand::class,
                BackupStorageCommand::class,
                RestoreStorageCommand::class,
                BackupApplicationCommand::class,
            ]);
        }
        // Добавить конфигурацию.
        app()->config['filesystems.disks.backups'] = [
            'driver' => 'local',
            'root' => backup_path(),
        ];
	}

	public function register()
	{
	
	}
}