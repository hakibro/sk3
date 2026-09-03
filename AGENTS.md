# AGENTS.md

Laravel 13 app for **mutasi santri** ("boyong" / SK3) at Pondok Pesantren Ngalah. UI and domain terminology are Indonesian.

## Environment & tooling

- Managed by **Lerd** (`.lerd.yaml`): Laravel 13, PHP 8.5, Node 22, MySQL + Redis, workers `queue`/`schedule`/`tunnel`. App URL is `https://sk3.test` (secured).
- `.env` is NOT sqlite — it uses MySQL at host `lerd-mysql`, db `sk3` (root/lerd). `DB_CONNECTION=mysql` also enforced in `phpunit.xml` (`sk3_testing`).
- Use Lerd tooling (`lerd_exec artisan`, `lerd_exec composer`, `lerd_exec vendor_run`) rather than raw `php`/`composer` on the host.
- Dev server: `composer run dev` (starts `artisan serve`, `queue:listen`, `pail`, and `vite` together). Build assets with `npm run build`.

## Commands

- Test: `composer test` (clears config cache first, then `php artisan test`). Tests use **Pest** with `RefreshDatabase` (see `tests/Pest.php`).
- Format: Laravel **Pint** (`vendor/bin/pint`).
- Migrations/seeders via `php artisan migrate` / `php artisan db:seed`.

## Critical: external database coupling (daruttaqwa_*)

This app is an overlay on a separate SIS/DTA database, NOT self-contained. Do not assume data lives in the local `sk3` DB.

- `v_siswa` and `v_asrama` are **MySQL views** created by migrations (`database/migrations/*make_v_siswa*`, `*make_v_asrama*`) that `SELECT` across other schemas: `daruttaqwa_person.tbl_person`, `daruttaqwa_sisda.tbl_*`, `daruttaqwa_referensi.tbl_departemen`.
- `PembayaranService` (app/Services) runs raw `DB::select`/`DB::statement` queries against `daruttaqwa_trans.ips_siswa` (tagihan/payments). The hardcoded period list `PERIODES` (`20212022`..`20252026`) and `tgl_jurnal < NOW()` filters must be kept in sync with the source data.
- The views filter to `idunit = '07'` and `idperiode = '20252026'` — a new school year requires updating migrations and `PembayaranService::PERIODES`.
- `Siswa`/`Asrama` models map to these views: non-incrementing string PKs (`idperson`, `idkelas`), `$timestamps = false`. Don't treat them like regular Eloquent tables.

## Roles & authorization

Three roles on `users.role` (string column, default `pengurus_asrama`): `admin`, `pengurus_pusat` (pusat), `pengurus_asrama` (asrama).

- `users.lembaga` stores the asrama name and is the data-scoping key: pengurus_asrama is hard-filtered to `siswa.asrama == user->lembaga` (see `SiswaController`, `BoyongController`).
- Helper methods live on `User`: `isAdmin()`, `isPusat()`, `isAsrama()`.
- Admin access is a Gate `access-admin` defined in `AppServiceProvider::boot()` (route middleware `can:access-admin`, prefix `/admin`).
- When creating a `pengurus_asrama`, `lembaga` **must** match the `asrama` value in `v_siswa`, or the user sees no data.

## Boyong (SK3) domain flow

- `BoyongService` (app/Services) is the core business logic: `ajukanBoyong`, `setujui`, `tolak`, `cekKelayakanBoyong`, surat-number/token generation. Route handlers in `BoyongController` are thin; put mutations in the service, not the controller.
- Status enum on `boyongs.status`: `pending` / `approved` / `rejected`.
- When a `pengurus_pusat` submits, the boyong is created directly as `approved` (auto `nomor_surat` + `public_token`); when asrama submits it starts `pending`.
- A student may have at most one active (`pending`/`approved`) boyong; `cekKelayakanBoyong` enforces this.
- Payment-cut rules are encoded in `BoyongService`: `spp_bulan_berjalan_full = tanggal_boyong->day > 6`, and `status_cut_pembayaran` is `menunggu_cut` vs `tidak_perlu`.
- `cetakSurat` blocks printing (SK3) while the student still has outstanding tagihan (`sisaTagihan > 0`); the "surat keterangan pengajuan" path is for the still-owing case.
- Public verification: `GET /verifikasi-boyong/{token}` resolves by `boyongs.public_token` (no auth).

## API

- `routes/api.php`: `GET /api/boyong/tagihan-terakhir/{idperson}` (`apiTagihanTerakhir`). Bearer-token guarded via `config('services.boyong_payment_api.token')` (env `BOYONG_PAYMENT_API_TOKEN`) — only enforced if the token is set.

## Conventions / gotchas

- App settings (kop surat) are key/value rows in `app_settings`, read via `AppSetting::kopSurat()` / `getValue` / `setValue`. Don't hardcode kop-surat text in views.
- `AlasanBoyong` (`alasan_boyongs`) is a seed-driven lookup for boyong reasons; managed in the admin panel.
- Seeder accounts (from `RoleUserSeeder`): `admin@test.com` / `pusat@test.com` / `asrama@test.com`, all password `password`.
- Real app layout is `resources/views/layouts/app.blade.php` (emerald sidebar + mobile bottom nav). `layouts/navigation.blade.php` is leftover Breeze default — don't mistake it for the active nav.
- No CI workflows (`/.github` absent) and no `pint.json`; lint/style is via Pint defaults.
