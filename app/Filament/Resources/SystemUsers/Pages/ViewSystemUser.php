<?php

namespace App\Filament\Resources\SystemUsers\Pages;

use App\Filament\Resources\SystemUsers\SystemUserResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewSystemUser extends ViewRecord
{
    protected static string $resource = SystemUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('System user personal details and contact info')
                    ->schema([
                        TextEntry::make('firstName')
                            ->label('First Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('middleName')
                            ->label('Middle Name')
                            ->placeholder('N/A'),
                        TextEntry::make('lastName')
                            ->label('Last Name')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                        TextEntry::make('address')
                            ->label('Physical Address')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('System Information')
                    ->description('Account creation details and audit timestamps')
                    ->schema([
                        TextEntry::make('creator.username')
                            ->label('Created By')
                            ->formatStateUsing(fn ($record) => $record->creator?->getFilamentName() ?? $record->creator?->username ?? 'System')
                            ->placeholder('System'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(3),
            ]);
    }
}
