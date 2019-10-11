<?php

namespace PortedCheese\Backups;

use PortedCheese\Backups\Console\Commands\BackupDataBaseCommand;
use PortedCheese\Backups\Console\Commands\RestoreDataBaseCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	public function boot()
	{
        // Console.
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupDataBaseCommand::class,
                RestoreDataBaseCommand::class,
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