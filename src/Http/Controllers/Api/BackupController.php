<?php

namespace PortedCheese\Backups\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Список файлов.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $files = [];
        $folder = $request->get("folder", config("backups.folder"));
        foreach (Storage::disk("yandex")->files($folder) as $fileName) {
            $ts = Storage::disk("yandex")->lastModified($fileName);
            $carbon = Carbon::createFromTimestamp($ts);
            $carbon->timezone = "Europe/Moscow";
            $files[] = [
                "name" => $fileName,
                "time" => $carbon->format("d.m.Y H:i:s"),
                "download" => route("admin.backups.download", ['file' => $fileName]),
            ];
        }
        return response()
            ->json($files);
    }

    /**
     * Создание нового бэкапа.
     *
     * @param Request $request
     * @param string $period
     * @return \Illuminate\Http\JsonResponse
     */
    public function make(Request $request, string $period)
    {
        $data = [
            'period' => $period,
        ];
        if ($folder = $request->get("folder", false)) {
            $data['--folder'] = $folder;
        }
        Artisan::queue("backup:app", $data);

        return response()
            ->json("Added to queue");
    }

    /**
     * Восстановить бэкап.
     * @param Request $request
     * @param string $period
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, string $period)
    {
        $data = [
            'period' => $period,
        ];
        if ($folder = $request->get("folder", false)) {
            $data["--folder"] = $folder;
        }
        Artisan::queue("restore:app", $data);

        return response()
            ->json("Added to queue");
    }

    /**
     * Скачать файл.
     *
     * @param Request $request
     * @return mixed
     */
    public function download(Request $request)
    {
        if (
            ! empty(config("backups.keyId")) &&
            ! empty(config("backups.keySecret")) &&
            ! empty(config("backups.bucket")) &&
            ! empty(config("backups.folder"))
        ) {
            $file = $request->get("file");
            if (Storage::disk("yandex")->exists($file)) {
                return Storage::disk("yandex")->download($file);
            }
        }
        abort(404);
    }
}
