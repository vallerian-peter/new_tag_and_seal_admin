# Filament v4 Namespace Reference Guide

## Overview
Filament v4 has a specific namespace structure that must be followed. This guide documents the correct namespaces for different components.

---

## 1. Schema/Form Components (for Resource Forms)

### Layout Components
**Use:** `Filament\Schemas\Components\*`

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Wizard;
```

### Form Input Components
**Use:** `Filament\Forms\Components\*`

```php
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\FileUpload;
```

### Utilities
**Use:** `Filament\Schemas\Components\Utilities\*`

```php
use Filament\Schemas\Components\Utilities\Get;
```

---

## 2. Infolist Components (for View Modals/Pages)

### Layout Components
**Use:** `Filament\Schemas\Components\*`

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
```

### Display Components
**Use:** `Filament\Infolists\Components\*`

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
```

---

## 3. Table Components

### Table Structure
**Use:** `Filament\Tables\*`

```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
```

---

## 4. Actions

### Action Components
**Use:** `Filament\Actions\*`

```php
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
```

---

## 5. Common Mistakes & Fixes

### ❌ Wrong: Section from Infolists (for view modals)
```php
use Filament\Infolists\Components\Section; // WRONG!
```

### ✅ Correct: Section from Schemas
```php
use Filament\Schemas\Components\Section; // CORRECT
```

### ❌ Wrong: Grid from Infolists
```php
use Filament\Infolists\Components\Grid; // WRONG!
```

### ✅ Correct: Grid from Schemas
```php
use Filament\Schemas\Components\Grid; // CORRECT
```

### ❌ Wrong: Get from Forms
```php
use Filament\Forms\Get; // WRONG!
```

### ✅ Correct: Get from Schemas Utilities
```php
use Filament\Schemas\Components\Utilities\Get; // CORRECT
```

---

## 6. File-by-File Examples

### Resource Form Schema File
**File:** `app/Filament/Resources/*/Schemas/*Form.php`

```php
<?php

namespace App\Filament\Resources\BirthEvents\Schemas;

// Schema components
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

// Form components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

class BirthEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')  // ← From Schemas
                    ->schema([
                        TextInput::make('name'),    // ← From Forms
                        Select::make('type'),       // ← From Forms
                    ])
                    ->columns(2),
            ]);
    }
}
```

### Resource Table File
**File:** `app/Filament/Resources/*/Tables/*Table.php`

```php
<?php

namespace App\Filament\Resources\BirthEvents\Tables;

// Table components
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

// Actions
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

// Infolist components (for view modal)
use Filament\Infolists\Components\TextEntry;  // ← Entry components
use Filament\Schemas\Components\Grid;         // ← Layout from Schemas
use Filament\Schemas\Components\Section;      // ← Layout from Schemas

class BirthEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordActions([
                Action::make('view')
                    ->infolist([
                        Section::make('Details')    // ← From Schemas
                            ->schema([
                                Grid::make(2)       // ← From Schemas
                                    ->schema([
                                        TextEntry::make('name'),  // ← From Infolists
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
```

---

## 7. Quick Reference Chart

| Component Type | Namespace | Use Case |
|----------------|-----------|----------|
| `Section` | `Filament\Schemas\Components\Section` | Forms & View Modals |
| `Grid` | `Filament\Schemas\Components\Grid` | Forms & View Modals |
| `Get` | `Filament\Schemas\Components\Utilities\Get` | Dynamic form logic |
| `TextInput` | `Filament\Forms\Components\TextInput` | Form inputs |
| `Select` | `Filament\Forms\Components\Select` | Form dropdowns |
| `DatePicker` | `Filament\Forms\Components\DatePicker` | Form date fields |
| `Textarea` | `Filament\Forms\Components\Textarea` | Form text areas |
| `TextEntry` | `Filament\Infolists\Components\TextEntry` | View display |
| `IconEntry` | `Filament\Infolists\Components\IconEntry` | View display |
| `TextColumn` | `Filament\Tables\Columns\TextColumn` | Table columns |
| `Action` | `Filament\Actions\Action` | Actions |

---

## 8. Key Principles

1. **Layout components** (Section, Grid, Tabs, etc.) → `Filament\Schemas\Components\*`
2. **Form input components** → `Filament\Forms\Components\*`
3. **Display components** (for viewing) → `Filament\Infolists\Components\*`
4. **Table components** → `Filament\Tables\*`
5. **Utilities** (Get, Set, etc.) → `Filament\Schemas\Components\Utilities\*`

---

## 9. Files Updated in This Project

### ✅ Correctly Updated Files:

1. **BirthEventForm.php**
   ```php
   use Filament\Schemas\Components\Grid;
   use Filament\Schemas\Components\Section;
   use Filament\Schemas\Components\Utilities\Get;
   ```

2. **BirthEventsTable.php**
   ```php
   use Filament\Infolists\Components\TextEntry;  // Display
   use Filament\Schemas\Components\Grid;         // Layout
   use Filament\Schemas\Components\Section;      // Layout
   ```

3. **AbortedPregnancyForm.php**
   ```php
   use Filament\Schemas\Components\Grid;
   use Filament\Schemas\Components\Section;
   use Filament\Schemas\Components\Utilities\Get;
   ```

4. **AbortedPregnanciesTable.php**
   ```php
   use Filament\Infolists\Components\TextEntry;
   use Filament\Schemas\Components\Grid;
   use Filament\Schemas\Components\Section;
   ```

5. **EventLogFormHelpers.php**
   ```php
   use Filament\Schemas\Components\Utilities\Get;
   ```

---

## 10. Debugging Tips

### If you see "Class not found" errors:

1. **Check the namespace** - Is it from the correct package?
2. **Clear cache** - Run `php artisan optimize:clear`
3. **Check Filament version** - Run `composer show | grep filament`
4. **Refer to docs** - https://filamentphp.com/docs/4.x/

### Common Error Messages:

| Error | Wrong Namespace | Correct Namespace |
|-------|-----------------|-------------------|
| `Class "Filament\Forms\Components\Section" not found` | `Forms\Components\Section` | `Schemas\Components\Section` |
| `Class "Filament\Infolists\Components\Section" not found` | `Infolists\Components\Section` | `Schemas\Components\Section` |
| `Class "Filament\Forms\Components\Grid" not found` | `Forms\Components\Grid` | `Schemas\Components\Grid` |
| `Get must be of type Filament\Forms\Get` | `Forms\Get` | `Schemas\Components\Utilities\Get` |

---

## 11. Summary

**Rule of Thumb:**
- **Schemas package** = Layout and structure (Section, Grid, Get utility)
- **Forms package** = Input fields (TextInput, Select, DatePicker, etc.)
- **Infolists package** = Display/read-only entries (TextEntry, IconEntry, etc.)
- **Tables package** = Table structure and columns
- **Actions package** = User interactions (buttons, bulk actions, etc.)

**In Filament v4, almost all layout/structural components come from `Filament\Schemas\Components\*`, not from Forms or Infolists!**

