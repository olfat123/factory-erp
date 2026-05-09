<?php

namespace App\Providers;

use App\Contracts\AccountingServiceInterface;
use App\Events\GoodsReceived;
use App\Events\ProductionCompleted;
use App\Events\ProductionStarted;
use App\Events\StockAdjusted;
use App\Listeners\RecordGoodsReceivedAccounting;
use App\Listeners\RecordProductionCompletedAccounting;
use App\Listeners\RecordProductionStartedAccounting;
use App\Services\AccountingService;
use App\Services\BatchService;
use App\Services\InventoryService;
use App\Services\ProductionService;
use App\Services\PurchaseService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(InventoryService::class);
        $this->app->singleton(PurchaseService::class);
        $this->app->singleton(ProductionService::class);
        $this->app->singleton(BatchService::class);
        $this->app->bind(AccountingServiceInterface::class, AccountingService::class);
    }

    public function boot(): void
    {
        Event::listen(GoodsReceived::class, RecordGoodsReceivedAccounting::class);
        Event::listen(ProductionStarted::class, RecordProductionStartedAccounting::class);
        Event::listen(ProductionCompleted::class, RecordProductionCompletedAccounting::class);
    }
}
