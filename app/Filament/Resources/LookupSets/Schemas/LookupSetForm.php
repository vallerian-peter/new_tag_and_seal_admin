<?php

namespace App\Filament\Resources\LookupSets\Schemas;

use App\Services\DynamicConfigurationService;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LookupSetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lookup set details')->columns(2)->columnSpanFull()->schema([
                TextInput::make('name')->required()->helperText('Stored identifier for this list in forms.'),
                TextInput::make('code')->required()->regex('/^[a-z][a-z0-9_.]*$/')->helperText('Internal code, usually the same as the name.'),
                TextInput::make('version')->numeric()->integer()->minValue(1)->default(1)->required(),
                Select::make('parent_lookup_set_uuid')->label('Filter using earlier lookup')
                    ->options(fn ($record) => \App\Models\LookupSet::where('status', 'published')
                        ->when($record, fn ($q) => $q->where('uuid', '!=', $record->uuid))
                        ->orderBy('name')->pluck('name', 'uuid'))
                    ->searchable()->nullable()
                    ->helperText('Optional: for dependent choices such as District depending on Region. Leave empty for standalone lists.'),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired'])->default('draft')->disabled()->dehydrated(false),
            ]),
            Section::make('Choice options')->columnSpanFull()->schema([
                Repeater::make('options')->label('Choices in order')->relationship('options')
                    ->defaultItems(0)->addActionLabel('Add choice')->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['code'] ?? null)
                    ->orderColumn('sort_order')
                    ->schema([
                        TextInput::make('code')->required()->regex('/^[a-z0-9_.-]+$/'),
                        TextInput::make('label')->required(),
                        KeyValue::make('translations_json')->label('Choice labels per language')->default(['en' => '', 'sw' => ''])->nullable(),
                        Select::make('parent_option_code')->label('Only available when parent choice is')
                            ->visible(fn (callable $get) => filled($get('../../parent_lookup_uuid')))
                            ->options(function (callable $get): array {
                                $parentUuid = $get('../../parent_lookup_uuid');
                                if (! $parentUuid) {
                                    return [];
                                }
                                return \App\Models\LookupOption::where('lookup_set_uuid', $parentUuid)->orderBy('code')->pluck('code', 'code')->all();
                            })
                            ->searchable()->nullable(),
                    ])
                    ->columns(2)->columnSpanFull(),
            ]),
        ]);
    }
}
