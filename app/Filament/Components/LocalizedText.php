<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;

final class LocalizedText
{
    public static function make(string $path, string $label, bool $required = true, bool $multiline = false): Fieldset
    {
        $component = $multiline ? Textarea::class : TextInput::class;

        return Fieldset::make($label)->columns(2)->columnSpanFull()->schema([
            $component::make($path.'.en')->label('English')->required($required)->maxLength($multiline ? 2000 : 255)
                ->helperText($required ? 'Required default language.' : 'Optional.'),
            $component::make($path.'.sw')->label('Kiswahili')->maxLength($multiline ? 2000 : 255)
                ->helperText('If empty, the published English text is shown.'),
        ]);
    }
}
