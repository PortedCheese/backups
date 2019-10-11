<?php

namespace PortedCheese\Backups\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupDataBaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:db {table?} {--file=backup.sql}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup application database';

    protected $username;
    protected $password;
    protected $database;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->username = config("database.connections.mysql.username");
        $this->password = config("database.connections.mysql.password");
        $this->database = config("database.connections.mysql.database");
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Command data.
        $password = $this->password;
        $db = $this->database;
        if ($table = $this->argument("table")) {
            $password = "";
            $db .= " $table";
        }
        // File.
        $file = $this->option("file");
        if (Storage::disk("backups")->exists($file)) {
            Storage::disk("backups")->delete($file);
        }
        // Make command.
        $process = Process::fromShellCommandline(sprintf(
            "mysqldump -u%s -p%s --default-character-set=utf8 --result-file=%s %s",
            $this->username,
            $password,
            storage_path("app/backups/" . $this->option("file")),
            $db
        ));

        try {
            // Run command.
            $process->mustRun();

            $this->info("The backup has been processed successfully");
        }
        catch (ProcessFailedException $exception) {
            $this->error("The backup process has been failed");
            $this->info($exception->getMessage());
        }
    }
}