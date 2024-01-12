<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Admin\GudangController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\Admin\MutasiController;
use App\Http\Controllers\Admin\ScanController;

// guest
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'show'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.perform');
});



// auth
Route::middleware('auth')->group(function () {
    // get
    Route::get('/', function () {
        return redirect(url('dashboard'));
    })->name('home');


    // pages all
    Route::get('/dashboard', App\Http\Controllers\DashboardController::class)->middleware('checkAbsen');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class,'index']);
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class,'edit']);
    Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
    Route::get('/absen/now', \App\Http\Controllers\AbsenController::class);

    // pages karyawan
    Route::prefix('/karyawan')->group(function () {
        Route::get('/absen', \App\Http\Controllers\Karyawan\AbsenController::class)->middleware('checkAbsen');
    });

    // pages admin
    Route::middleware('checkRole:1')->prefix('/admin')->group(function () {
        Route::get('/absen', [\App\Http\Controllers\Admin\AbsenController::class, 'index']);
        Route::get('/absen/bulanan', [\App\Http\Controllers\Admin\AbsenController::class, 'bulanan']);

        Route::get('/karyawan', \App\Http\Controllers\Admin\KaryawanController::class);

        Route::get('/settings',[\App\Http\Controllers\Admin\SettingController::class,'index']);

        Route::get('/gudang',[GudangController::class,'index']);
        Route::post('/gudang/simpan-gudang',[GudangController::class,'simpan'])->name('simpanGudang');
        Route::post('/gudang/update-gudang/{id}',[GudangController::class,'update'])->name('updateGudang');
        Route::post('/gudang/hapus-gudang/{id}',[GudangController::class,'hapus'])->name('hapusGudang');

        Route::get('/barang',[BarangController::class,'index'])->name('indexBarang');
        Route::get('/barang/scan',[ScanController::class,'index'])->name('indexScan');
        Route::post('/barang/simpan-barang',[BarangController::class,'simpan'])->name('simpanBarang');
        Route::post('/barang/update-barang/{id}',[BarangController::class,'update'])->name('updateBarang');
        Route::post('/barang/hapus-barang/{id}',[BarangController::class,'hapus'])->name('hapusBarang');

        Route::get('/kategori',[KategoriController::class,'index'])->name('indexKategori');
        Route::post('/kategori/simpan-kategori',[KategoriController::class,'simpan'])->name('simpanKategori');
        Route::post('/kategori/update-kategori/{id}',[KategoriController::class,'update'])->name('updateKategori');
        Route::post('/kategori/delete-kategori/{id}',[KategoriController::class,'delete'])->name('hapusKategori');

        Route::get('/satuan',[SatuanController::class,'index'])->name('indexSatuan');
        Route::post('/satuan/simpan-satuan',[SatuanController::class,'simpan'])->name('simpanSatuan');
        Route::post('/satuan/update-satuan/{id}',[SatuanController::class,'update'])->name('updateSatuan');
        Route::post('/satuan/delete-satuan/{id}',[SatuanController::class,'delete'])->name('hapusSatuan');

        Route::get('/mutasi',[MutasiController::class,'index'])->name('indexMutasi');

        Route::post('/mutasi/masuk/simpan',[MutasiController::class,'simpanMasuk'])->name('simpanMasuk');
        Route::post('/mutasi/masuk/hapus-masuk/{id}',[MutasiController::class,'hapusMasuk'])->name('hapusMasuk');

        Route::post('/mutasi/keluar/simpan',[MutasiController::class, 'simpanKeluar'])->name('simpanKeluar');
        Route::post('/mutasi/keluar/hapus-keluar/{id}',[MutasiController::class, 'hapusKeluar'])->name('hapusKeluar');

        Route::post('/mutasi/kembali/simpan',[MutasiController::class, 'simpanKembali'])->name('simpanKembali');
        Route::post('/mutasi/kembali/hapus-kembali/{id}',[MutasiController::class, 'hapusKembali'])->name('hapusKembali');
    });
});






// api
Route::middleware('auth.api')->prefix('/api')->group(function () {

    // api all
    Route::post('/absen', \App\Http\Apis\AbsenApi::class);

    Route::get('/dashboard/aktivitas', [\App\Http\Apis\DashboardApi::class,'index']);
    Route::get('/dashboard/recap', [\App\Http\Apis\DashboardApi::class,'recap']);

    Route::patch('profile/change-password', [\App\Http\Apis\ProfileApi::class, 'changePassword']);
    Route::post('profile/change-picture', [\App\Http\Apis\ProfileApi::class, 'changePicture']);
    Route::patch('profile', [\App\Http\Apis\ProfileApi::class, 'update']);
    Route::get('profile', [\App\Http\Apis\ProfileApi::class, 'index']);

    // api karyawan
    Route::prefix('/karyawan')->group(function () {
        Route::get('/absen/today', [\App\Http\Apis\Karyawan\AbsenApi::class, 'today']);
        Route::get('/absen/monthly', [\App\Http\Apis\Karyawan\AbsenApi::class, 'monthly']);
        Route::get('/absen/{id}', [\App\Http\Apis\Karyawan\AbsenApi::class, 'show']);
        Route::get('/absen', [\App\Http\Apis\Karyawan\AbsenApi::class, 'index']);
    });

    // api admin
    Route::middleware('checkRoleApi:1')->prefix('/admin')->group(function () {
        Route::get('/absen/bulanan/{id}', [\App\Http\Apis\Admin\AbsenApi::class, 'bulanan_show']);
        Route::get('/absen/bulanan', [\App\Http\Apis\Admin\AbsenApi::class, 'bulanan']);
        Route::get('/absen/export-daily', [\App\Http\Apis\Admin\AbsenApi::class, 'export_daily']);
        Route::get('/absen/{id}', [\App\Http\Apis\Admin\AbsenApi::class, 'show']);
        Route::get('/absen', [\App\Http\Apis\Admin\AbsenApi::class, 'index']);
        Route::post('/absen/batch', [\App\Http\Apis\Admin\AbsenApi::class, 'batch']);
        Route::post('/absen', [\App\Http\Apis\Admin\AbsenApi::class, 'store']);
        Route::delete('/absen/{id}', [\App\Http\Apis\Admin\AbsenApi::class, 'destroy']);

        Route::put('/aktivitas/{id}', [\App\Http\Apis\Admin\AktivitasApi::class, 'update']);
        Route::post('/aktivitas', [\App\Http\Apis\Admin\AktivitasApi::class, 'store']);
        Route::delete('/aktivitas/{id}', [\App\Http\Apis\Admin\AktivitasApi::class, 'destroy']);

        Route::get('/karyawan',[\App\Http\Apis\Admin\KaryawanApi::class,'index']);
        Route::post('/karyawan',[\App\Http\Apis\Admin\KaryawanApi::class,'store']);
        Route::patch('/karyawan/{id}',[\App\Http\Apis\Admin\KaryawanApi::class,'update']);
        Route::delete('/karyawan/{id}',[\App\Http\Apis\Admin\KaryawanApi::class,'delete']);


    });
});


// storage
// Route::middleware(['auth.storage'])->group(function () {
//     Route::get('/storage/private/{path}', \App\Http\Controllers\StorageController::class)
//         ->where('path', '.*')
//         ->name('storage.private');
// });

Route::get('run', function () {
    return date('H:i');
});
Route::get('test', function () {
    return view('pages.test',compact('user'));
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
