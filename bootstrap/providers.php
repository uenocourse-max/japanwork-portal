<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\ListPanelProvider;
use App\Providers\Filament\RecruiterPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    ListPanelProvider::class,
    RecruiterPanelProvider::class,
];
