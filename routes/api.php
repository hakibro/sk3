<?php

use App\Http\Controllers\BoyongController;
use Illuminate\Support\Facades\Route;

Route::get('/boyong/tagihan-terakhir/{idperson}', [BoyongController::class, 'apiTagihanTerakhir'])
    ->name('api.boyong.tagihan-terakhir');
