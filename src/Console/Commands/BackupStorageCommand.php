<?php

namespace PortedCheese\Backups\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class BackupStorageCommand extends Command
{
    const FILE_NAME = "public.zip";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup public folder in storage';

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

        if (Storage::disk("backups")->exists(self::FILE_NAME)) {
            Storage::disk("backups")->delete(self::FILE_NAME);
        }

        try {
            $this->zip = Zip::create(backup_path(self::FILE_NAME));
        }
        catch (\Exception $exception) {
            $this->zip = null;
        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->zip) {
            $this->error("Fail init archive");
        }
        try {
            $this->zip->add(backup_storage_path(), true);
            $this->zip->close();

            $this->info("Archive generated successfully");
        }
        catch (\Exception $exception) {
            $this->error("Error while generated archive");
        }
    }
}
