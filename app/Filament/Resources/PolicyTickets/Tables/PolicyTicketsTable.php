<?php

namespace App\Filament\Resources\PolicyTickets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class PolicyTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => route('filament.admin.resources.policy-tickets.view', ['record' => $record]))
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('หมายเลขตั๋ว')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('customer.name')
                    ->label('ลูกค้า')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('insurance_type')
                    ->label('ประเภท')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'MOU' => 'primary',
                        'มติ24' => 'success',
                        default => 'secondary'
                    }),

                TextColumn::make('duration')
                    ->label('ระยะเวลา')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        '3_months' => '3 เดือน',
                        '6_months' => '6 เดือน', 
                        '12_months' => '12 เดือน',
                        '15_months' => '15 เดือน',
                        default => $state
                    }),

                TextColumn::make('person_count')
                    ->label('จำนวนคน')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('ราคารวม')
                    ->money('THB')
                    ->sortable()
                    ->alignRight(),

                TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'draft' => 'gray',
                        'submitted' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'draft' => '📝 ร่าง',
                        'submitted' => '📤 ส่งแล้ว',
                        'processing' => '⏳ กำลังดำเนินการ',
                        'completed' => '✅ เสร็จแล้ว',
                        'rejected' => '❌ ถูกปฏิเสธ',
                        default => $state
                    }),

                TextColumn::make('request_file_path')
                    ->label('ไฟล์ส่ง')
                    ->formatStateUsing(fn (?string $state): string => $state ? '📄' : '—')
                    ->alignCenter(),

                TextColumn::make('policy_file_path')
                    ->label('กรมธรรม์')
                    ->formatStateUsing(fn (?string $state): string => $state ? '📄' : '—')
                    ->alignCenter(),

                TextColumn::make('createdBy.name')
                    ->label('สร้างโดย')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('วันที่สร้าง')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('staff_name')
                    ->label('เจ้าหน้าที่')
                    ->placeholder('ยังไม่ได้ระบุ')
                    ->icon('heroicon-o-user')
                    ->iconColor('success')
                    ->searchable()
                    ->toggleable(),

                IconColumn::make('staff_file_path')
                    ->label('ไฟล์แนบ')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('')
                    ->trueColor('info')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'draft' => '📝 ร่าง',
                        'submitted' => '📤 ส่งแล้ว',
                        'processing' => '⏳ กำลังดำเนินการ',
                        'completed' => '✅ เสร็จแล้ว',
                        'rejected' => '❌ ถูกปฏิเสธ',
                    ]),

                SelectFilter::make('insurance_type')
                    ->label('ประเภทประกัน')
                    ->options([
                        'MOU' => 'MOU',
                        'มติ24' => 'มติ24',
                    ]),

                SelectFilter::make('duration')
                    ->label('ระยะเวลา')
                    ->options([
                        '3_months' => '3 เดือน',
                        '6_months' => '6 เดือน',
                        '12_months' => '12 เดือน',
                        '15_months' => '15 เดือน',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label('ดู')
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->label('แก้ไข')
                    ->icon('heroicon-o-pencil'),
                Action::make('public_link')
                    ->label('Public Link')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->action(function ($record) {
                        $url = route('public.ticket.check', ['ticket_number' => $record->ticket_number]);
                        return redirect($url);
                    })
                    ->tooltip('เปิด Public Link สำหรับลูกค้า'),
                Action::make('copy_access_info')
                    ->label('คัดลอกข้อมูล')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->action(function ($record) {
                        $url = route('public.ticket.check', ['ticket_number' => $record->ticket_number]);
                        $message = "ข้อมูลคำขอประกันภัย\nหมายเลขคำขอ: {$record->ticket_number}\nลิงค์ตรวจสอบ: {$url}";
                        
                        return response()->json([
                            'message' => 'คัดลอกข้อมูลแล้ว',
                            'data' => $message
                        ]);
                    })
                    ->tooltip('คัดลอกหมายเลขและลิงค์สำหรับส่งให้ลูกค้า'),
                Action::make('staff_link')
                    ->label('ฟอร์มเจ้าหน้าที่')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->url(fn ($record) => route('public.ticket.staff-form', $record->ticket_number))
                    ->openUrlInNewTab()
                    ->tooltip('เปิดฟอร์มสำหรับเจ้าหน้าที่กรอกข้อมูล'),
                Action::make('download_request')
                    ->label('ดาวน์โหลดไฟล์ส่ง')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->request_file_path ? asset('storage/' . $record->request_file_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->request_file_path)),
                Action::make('download_policy')
                    ->label('ดาวน์โหลดกรมธรรม์')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->policy_file_path ? asset('storage/' . $record->policy_file_path) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->policy_file_path)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('ลบที่เลือก'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s') // Refresh ทุก 30 วินาที
            ->emptyStateHeading('ยังไม่มีตั๋วประกัน')
            ->emptyStateDescription('สร้างตั๋วประกันใหม่เพื่อเริ่มต้น')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
