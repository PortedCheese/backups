<?php

namespace PortedCheese\Backups\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class BackupApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:app {type=daily}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup app files by type';

    /**
     * @var Zip
     */
    protected $zip;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->zip = null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Backup database.
        $this->callSilent("backup:db");
        if (! Storage::disk("backups")->exists(BackupDataBaseCommand::FILE_NAME)) {
            $this->error("Backup database failed");
            return;
        }
        // Backup storage.
        $this->callSilent("backup:storage");
        if (! Storage::disk("backups")->exists(BackupStorageCommand::FILE_NAME)) {
            $this->error("Backup storage failed");
            return;
        }
        // Make archive.
        $type = $this->argument("type");

        if (Storage::disk("backups")->exists("{$type}.zip")) {
            Storage::disk("backups")->delete("{$type}.zip");
        }

        try {
            $this->zip = Zip::create(backup_path("{$type}.zip"));
        }
        catch (\Exception $exception) {
            $this->zip = null;
        }

        if (! $this->zip) {
            $this->error("Fail init archive");
            return;
        }

        try {
            $this->zip->add([
                backup_path(BackupDataBaseCommand::FILE_NAME),
                backup_path(BackupStorageCommand::FILE_NAME),
            ]);
            $this->zip->close();

            Storage::disk("backups")->delete([
                BackupDataBaseCommand::FILE_NAME,
                BackupStorageCommand::FILE_NAME,
            ]);

            $this->info("Backup {$type} create successfully");
        }
        catch (\Exception $exception) {
            $this->error("Error while generated archive");
        }
    }
}
