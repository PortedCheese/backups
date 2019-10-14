<?php

namespace PortedCheese\Backups\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Zip;

class RestoreApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:app {period=daily} {--from-current}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore app files by period';

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
        $period = $this->argument("period");
        $fileName = "{$period}.zip";

        if ($this->option("from-current")) {
            $folder = BackupApplicationCommand::FOLDER;
            if (Storage::disk("backups")->exists("{$folder}/{$fileName}")) {
                Storage::disk("backups")->copy("{$folder}/{$fileName}", $fileName);
            }
        }

        if (! Storage::disk("backups")->exists($fileName)) {
            $this->error("File not found");
            return;
        }

        try {
            $this->zip = Zip::open(backup_path($fileName));
        }
        catch (\Exception $exception) {
            $this->zip = null;
        }

        if (! $this->zip) {
            $this->error("Fail open archive");
            Log::error("Fail open application archive");
            return;
        }

        try {
            $this->zip->extract(backup_path());
            $this->zip->close();
            Storage::disk("backups")->delete($fileName);

            $this->callSilent("restore:db");
            $this->callSilent("restore:storage");

            $this->callSilent("cache:clear");

            $this->info("Application successfully restored");
        }
        catch (\Exception $exception) {
            $this->error("Fail extract archive. Need manually extract");
            Log::error("Fail extract application archive. Need manually extract");
        }
    }
}
