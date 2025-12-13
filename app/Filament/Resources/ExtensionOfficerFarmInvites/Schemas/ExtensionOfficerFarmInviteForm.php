<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Schemas;

use App\Models\Farm;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExtensionOfficerFarmInviteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Extension Officer')
                    ->schema([
                        Select::make('extensionOfficerId')
                            ->label('Extension Officer')
                            ->relationship('extensionOfficer', 'firstName', modifyQueryUsing: function ($query) {
                                return $query->selectRaw("id, firstName, middleName, lastName")
                                    ->orderBy('firstName');
                            })
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return trim($record->firstName . ' ' . ($record->middleName ?? '') . ' ' . $record->lastName);
                            })
                            ->searchable(['firstName', 'middleName', 'lastName', 'email'])
                            ->preload()
                            ->required()
                            ->helperText('Select the extension officer who will receive access'),
                    ])
                    ->columns(1),

                Section::make('Farm & Owner Selection')
                    ->description('Search and select a farm to grant access. The owner (farmer) will be automatically linked.')
                    ->schema([
                        Select::make('farmId')
                            ->label('Search Farm by Name or Owner')
                            ->options(function (?string $search = null) {
                                $query = Farm::with('farmer');

                                if (!empty($search) && strlen($search) >= 1) {
                                    // Filter when searching
                                    $query->where(function ($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%")
                                            ->orWhere('referenceNo', 'like', "%{$search}%");
                                    })
                                    ->orWhereHas('farmer', function ($q) use ($search) {
                                        $q->where('firstName', 'like', "%{$search}%")
                                            ->orWhere('middleName', 'like', "%{$search}%")
                                            ->orWhere('surname', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('phone1', 'like', "%{$search}%");
                                    });
                                    $limit = 50;
                                } else {
                                    // Show top 5 farms when empty
                                    $limit = 5;
                                }

                                $farms = $query->orderBy('created_at', 'desc')
                                    ->limit($limit)
                                    ->get();

                                return $farms->mapWithKeys(function ($farm) {
                                    $farmer = $farm->farmer;
                                    if (!$farmer) {
                                        return [$farm->id => "{$farm->name} - {$farm->referenceNo} (No Owner)"];
                                    }
                                    $farmerName = trim(
                                        ($farmer->firstName ?? '') . ' ' .
                                        ($farmer->middleName ?? '') . ' ' .
                                        ($farmer->surname ?? '')
                                    );
                                    $label = "{$farm->name} (Owner: {$farmerName}) - {$farm->referenceNo}";
                                    return [$farm->id => $label];
                                });
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $farm = Farm::with('farmer')->find($value);
                                if (!$farm) {
                                    return '';
                                }
                                $farmer = $farm->farmer;
                                if (!$farmer) {
                                    return "{$farm->name} - {$farm->referenceNo} (No Owner)";
                                }
                                $farmerName = trim(
                                    ($farmer->firstName ?? '') . ' ' .
                                    ($farmer->middleName ?? '') . ' ' .
                                    ($farmer->surname ?? '')
                                );
                                return "{$farm->name} (Owner: {$farmerName}) - {$farm->referenceNo}";
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $farm = Farm::find($state);
                                    if ($farm && $farm->farmerId) {
                                        $set('farmerId', $farm->farmerId);
                                    }
                                }
                            })
                            ->helperText('Shows top 5 farms by default. Type to search by farm name, owner name, email, phone, or farm reference number.'),
                    ])
                    ->columns(1),

                Section::make('Access Code')
                    ->description('A unique access code will be automatically generated in the format: ACODE-XXXXXABC=7-XXXX')
                    ->schema([
                        TextInput::make('access_code')
                            ->label('Access Code')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn ($livewire) => 
                                $livewire instanceof \App\Filament\Resources\ExtensionOfficerFarmInvites\Pages\CreateExtensionOfficerFarmInvite
                                    ? \App\Models\ExtensionOfficerFarmInvite::generateUniqueAccessCode()
                                    : null
                            )
                            ->helperText('This code allows the extension officer to access the selected farm'),
                    ])
                    ->columns(1)
                    ->visible(fn ($get) => !empty($get('extensionOfficerId')) && !empty($get('farmId'))),

                // Hidden field to store farmerId
                TextInput::make('farmerId')
                    ->hidden()
                    ->dehydrated()
                    ->required()
                    ->default(null),
            ]);
    }
}
