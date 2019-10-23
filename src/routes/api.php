<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', "role:admin"],
    "prefix" => "api/backups",
    "as" => "api.backups.",
    'namespace' => 'PortedCheese\Backups\Http\Controllers\Api',
], function () {
    Route::get("/", "BackupController@index");

    Route::post("/{period}", "BackupController@make");

    Route::put("/{period}", "BackupController@restore");
});