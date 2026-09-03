<?php

use App\Models\Boyong;
use App\Services\PembayaranService;

test('scope summary mengikuti cakupan boyong', function () {
    $boyong = new Boyong([
        'boyong_scope' => ['asrama' => true, 'madin' => true, 'formal' => false],
    ]);

    expect($boyong->getScopeLabelsAttribute())->toBe(['Asrama', 'Madin'])
        ->and($boyong->scope_summary)->toBe('Asrama + Madin');
});

test('scope default hanya asrama bila kosong', function () {
    $boyong = new Boyong;

    expect($boyong->getScopeLabelsAttribute())->toBe([])
        ->and($boyong->scope_summary)->toBe('Asrama');
});

test('mapping unit kategori boyong', function () {
    expect(PembayaranService::UNIT_ASRAMA)->toBe(['07'])
        ->and(PembayaranService::UNIT_MADIN)->toBe(['01'])
        ->and(PembayaranService::UNIT_FORMAL)->toBe(['02', '03', '04', '05', '06', '08'])
        ->and(PembayaranService::UNIT_KANTIN)->toBe(['KANTIN']);
});
