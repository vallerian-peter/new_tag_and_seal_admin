<?php

namespace App\Filament\Resources\FarmUsers\Pages;

use App\Filament\Resources\FarmUsers\FarmUserResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewFarmUser extends ViewRecord
{
    protected static string $resource = FarmUserResource::class;

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
                    ->description('Farm user basic details and contact info')
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
                        TextEntry::make('gender')
                            ->label('Gender')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'male' => 'info',
                                'female' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->placeholder('N/A'),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon('heroicon-o-phone')
                            ->copyable(),
                    ])
                    ->columns(3),

                Section::make('Farm & Role Assignment')
                    ->description('Associated farm and assigned operational role')
                    ->schema([
                        TextEntry::make('farm.name')
                            ->label('Assigned Farm')
                            ->icon('heroicon-o-building-storefront')
                            ->weight('bold')
                            ->placeholder('N/A'),
                        TextEntry::make('roleTitle')
                            ->label('Assigned Role')
                            ->badge()
                            ->formatStateUsing(fn (string $state) => str_replace('-', ' ', ucfirst($state)))
                            ->color('primary'),
                    ])
                    ->columns(2),

                Section::make('System Information')
                    ->description('Record creation and modification audit timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated At')
                            ->dateTime('M d, Y h:i A')
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(2),
            ]);
    }
}
