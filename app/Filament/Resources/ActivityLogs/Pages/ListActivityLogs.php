<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - Activity logs are auto-generated
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // No widgets for now - ActivityStatsWidget was deleted
        ];
    }
}
