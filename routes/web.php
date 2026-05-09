<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/portal');
});

Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['en', 'ar'])) {
        session(['locale' => $lang]);
    }
    return redirect('/portal');
})->name('locale.switch')->middleware('throttle:20,1');

// Report exports (protected by Filament auth middleware)
Route::middleware(['web', 'auth', 'throttle:60,1'])->prefix('reports')->name('reports.')->group(function () {
    Route::get('/inventory/excel',          [ReportController::class, 'inventoryExcel'])->name('inventory.excel');
    Route::get('/inventory/pdf',            [ReportController::class, 'inventoryPdf'])->name('inventory.pdf');
    Route::get('/stock-movements/excel',    [ReportController::class, 'stockMovementsExcel'])->name('stock-movements.excel');
    Route::get('/production/excel',         [ReportController::class, 'productionExcel'])->name('production.excel');
    Route::get('/production/pdf',           [ReportController::class, 'productionPdf'])->name('production.pdf');
    Route::get('/financial/excel',          [ReportController::class, 'financialExcel'])->name('financial.excel');
    Route::get('/financial/pdf',            [ReportController::class, 'financialPdf'])->name('financial.pdf');
});

