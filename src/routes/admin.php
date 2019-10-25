<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', "role:admin"],
    "prefix" => "admin/backups",
    "as" => "admin.backups.",
    'namespace' => 'PortedCheese\Backups\Http\Controllers\Api',
], function () {
    Route::get("/", "BackupController@download")
        ->name("download");
});