<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExtensionOfficerFarmInviteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('extensionOfficer.full_name')
                    ->label('Extension Officer')
                    ->formatStateUsing(fn ($record) => 
                        trim($record->extensionOfficer->firstName . ' ' . 
                             ($record->extensionOfficer->middleName ?? '') . ' ' . 
                             $record->extensionOfficer->lastName)
                    )
                    ->weight('bold'),
                TextEntry::make('farmer.full_name')
                    ->label('Farmer / Owner')
                    ->formatStateUsing(fn ($record) => 
                        trim($record->farmer->firstName . ' ' . 
                             ($record->farmer->middleName ?? '') . ' ' . 
                             $record->farmer->surname)
                    ),
                TextEntry::make('farmer.email')
                    ->label('Owner Email')
                    ->icon('heroicon-o-envelope'),
                TextEntry::make('access_code')
                    ->label('Access Code')
                    ->badge()
                    ->color('success')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Access code copied!'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
