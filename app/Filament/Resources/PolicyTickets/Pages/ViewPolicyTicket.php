<?php

namespace App\Filament\Resources\PolicyTickets\Pages;

use App\Filament\Resources\PolicyTickets\PolicyTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPolicyTicket extends ViewRecord
{
    protected static string $resource = PolicyTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('แก้ไข')
                ->icon('heroicon-o-pencil'),
            Actions\DeleteAction::make()
                ->label('ลบ')
                ->icon('heroicon-o-trash'),
                
            // เพิ่ม Action สำหรับดูไฟล์จากเจ้าหน้าที่ทิพย์
            Actions\Action::make('view_staff_file')
                ->label('ไฟล์จากทิพย์')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(function ($record) {
                    if ($record->staff_file_path) {
                        return asset('storage/' . $record->staff_file_path);
                    }
                    return null;
                })
                ->openUrlInNewTab()
                ->visible(fn ($record) => !empty($record->staff_file_path)),
                
            // เพิ่ม Action สำหรับดูไฟล์จากลูกค้า  
            Actions\Action::make('view_request_file')
                ->label('ไฟล์จากลูกค้า')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(function ($record) {
                    if ($record->request_file_path) {
                        return asset('storage/' . $record->request_file_path);
                    }
                    return null;
                })
                ->openUrlInNewTab()
                ->visible(fn ($record) => !empty($record->request_file_path)),
        ];
    }
}
