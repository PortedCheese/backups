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
            $files[] = [
                "name" => $fileName,
                "time" => Carbon::createFromTimestamp($ts)->format("d.m.Y H:i:s"),
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
}
