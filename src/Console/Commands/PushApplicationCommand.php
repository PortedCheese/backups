<?php

namespace PortedCheese\Backups\Console\Commands;

use Aws\Sdk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PushApplicationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:push {period=daily} {--from-current} {--folder=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull app zip by period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $period = $this->argument("period");
        $fileName = "{$period}.zip";
        $folder = BackupApplicationCommand::FOLDER;
        $currentPath = "{$folder}/{$fileName}";
        $s3Folder = $this->option("folder");
        if (empty($s3Folder)) {
            $s3Folder = "";
        }
        else {
            $s3Folder .= "/";
        }
        // Если из текущих бэкапов, пробуем скопировать файл.
        if (
            $this->option("from-current") &&
            Storage::disk("backups")->exists($currentPath)
        ) {
            Storage::disk("backups")->copy($currentPath, $fileName);
        }
        // Если файла нет, то ошибка.
        if (! Storage::disk("backups")->exists($fileName)) {
            $this->error("File not found");
            return;
        }
        // Пробуем отправить файл.
        try {
            Storage::disk("yandex")->put(
                $s3Folder . $fileName,
                Storage::disk("backups")->get($fileName)
            );
            // Если файл отправился, удаляем с сервера.
            if (
                $this->option("from-current") &&
                Storage::disk("backups")->exists($currentPath)
            ) {
                Storage::disk("backups")->delete($currentPath);
            }
            Storage::disk("backups")->delete($fileName);
        }
        catch (\Exception $exception) {
            $this->line($exception->getMessage());
        }
    }
}
