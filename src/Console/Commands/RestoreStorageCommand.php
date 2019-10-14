<?php

namespace PortedCheese\Backups\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class RestoreStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore public folder in storage';

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
        if (! Storage::disk("backups")->exists(BackupStorageCommand::FILE_NAME)) {
            $this->error("File not found");
            return;
        }

        try {
            $this->zip = Zip::open(backup_path(BackupStorageCommand::FILE_NAME));
        }
        catch (\Exception $exception) {
            $this->zip = null;
        }

        if (! $this->zip) {
            $this->error("Fail open archive");
            return;
        }

        $directories = Storage::disk("public")->directories();
        foreach ($directories as $directory) {
            Storage::disk("public")->deleteDirectory($directory);
        }

        try {
            $this->zip->extract(backup_storage_path());

            $this->info("Files successfully restored");
        }
        catch (\Exception $exception) {
            $this->error("Fail extract archive. Need manually extract");
        }
    }
}
