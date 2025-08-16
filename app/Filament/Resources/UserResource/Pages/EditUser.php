<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('ดู')
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label('ลบ')
                ->icon('heroicon-o-trash'),
        ];
    }
}
