# Dynamic Forms & UUID Update - Admin Panel

## Overview
Updated all event log forms in the admin panel to have dynamic, cascading dropdowns and improved UUID generation with a standardized format.

---

## Changes Made

### 1. UUID Generation - New Format

**Old Format:**
```
1764658383497-1066693702-9
```

**New Format:**
```
timestamp-uuid-eventType
```

**Examples:**
- `1733155200-abc123def456-birth`
- `1733155300-ghi789jkl012-feeding`
- `1733155400-mno345pqr678-deworming`

**Benefits:**
- ✅ **Sortable by time** - Timestamp prefix allows chronological sorting
- ✅ **Identifiable** - Event type suffix makes it easy to identify
- ✅ **Unique** - UUID middle part ensures uniqueness
- ✅ **Traceable** - Can quickly find events by type or time range
- ✅ **Read-only** - Auto-generated, cannot be manually edited

---

### 2. Dynamic Form Cascading

#### Problem Solved
**Before:**
- Farm dropdown showed all farms
- Livestock dropdown showed ALL livestock (from all farms)
- User could accidentally select livestock from wrong farm
- No relationship validation

**After:**
- Farm dropdown shows all farms
- **Livestock dropdown is disabled** until farm is selected
- Livestock dropdown **only shows animals from the selected farm**
- Selecting a different farm **clears the livestock selection**
- Helper text guides the user

#### User Flow
```
1. User opens "Create Birth Event" form
2. Farm field is enabled, Livestock field is disabled
3. User selects "Farm A"
   → Livestock field becomes enabled
   → Shows only livestock from Farm A (with species info)
4. User selects "Livestock 001 - Bessie (Cattle)"
   → For birth events: Auto-sets event type to "Calving"
   → Filters birth types/problems by livestock type
5. User changes farm to "Farm B"
   → Livestock selection is cleared
   → Livestock dropdown updates to show Farm B's animals only
```

---

### 3. Files Created/Modified

#### New Helper File
**`app/Filament/Resources/Helpers/EventLogFormHelpers.php`**

Provides reusable form components:
- ✅ `uuidField($eventType)` - Generates UUID with timestamp-uuid-eventType format
- ✅ `farmField()` - Farm select with live updates and clearing logic
- ✅ `livestockField()` - Dynamic livestock select filtered by farm

```php
// Usage in any event log form:
EventLogFormHelpers::uuidField('feeding'),
EventLogFormHelpers::farmField(),
EventLogFormHelpers::livestockField(),
```

#### Updated Forms (Using Helper)

1. **`app/Filament/Resources/BirthEvents/Schemas/BirthEventForm.php`**
   - ✅ UUID: `timestamp-uuid-birth`
   - ✅ Dynamic livestock by farm
   - ✅ Auto-detection of event type (calving/farrowing)
   - ✅ Dynamic filtering of birth types/problems
   - ✅ Context-aware labels

2. **`app/Filament/Resources/AbortedPregnancies/Schemas/AbortedPregnancyForm.php`**
   - ✅ UUID: `timestamp-uuid-abortion`
   - ✅ Dynamic livestock by farm

3. **`app/Filament/Resources/Feedings/Schemas/FeedingForm.php`**
   - ✅ UUID: `timestamp-uuid-feeding`
   - ✅ Dynamic livestock by farm

4. **`app/Filament/Resources/Dewormings/Schemas/DewormingForm.php`**
   - ✅ UUID: `timestamp-uuid-deworming`
   - ✅ Dynamic livestock by farm

5. **`app/Filament/Resources/WeightChanges/Schemas/WeightChangeForm.php`**
   - ✅ UUID: `timestamp-uuid-weight`
   - ✅ Dynamic livestock by farm

6. **`app/Filament/Resources/Medications/Schemas/MedicationForm.php`**
   - ✅ UUID: `timestamp-uuid-medication`
   - ✅ Dynamic livestock by farm

7. **`app/Filament/Resources/Vaccinations/Schemas/VaccinationForm.php`**
   - ✅ UUID: `timestamp-uuid-vaccination`
   - ✅ Dynamic livestock by farm

8. **`app/Filament/Resources/Disposals/Schemas/DisposalForm.php`**
   - ✅ UUID: `timestamp-uuid-disposal`
   - ✅ Dynamic livestock by farm

---

### 4. Technical Implementation

#### Farm Field with Live Updates
```php
Select::make('farmUuid')
    ->label('Farm')
    ->relationship('farm', 'name')
    ->searchable()
    ->preload()
    ->required()
    ->live()  // ← Enables reactive updates
    ->afterStateUpdated(function ($state, callable $set) {
        // Clear livestock when farm changes
        $set('livestockUuid', null);
    })
```

#### Livestock Field with Dynamic Options
```php
Select::make('livestockUuid')
    ->label('Livestock')
    ->options(function (Get $get) {
        $farmUuid = $get('farmUuid');
        if (!$farmUuid) {
            return [];  // No options until farm is selected
        }
        
        // Only show livestock from selected farm
        return Livestock::where('farmUuid', $farmUuid)
            ->get()
            ->mapWithKeys(function ($livestock) {
                $label = trim(
                    ($livestock->identificationNumber ?? $livestock->uuid) . 
                    ($livestock->name ? " - {$livestock->name}" : '') .
                    ($livestock->species ? " ({$livestock->species->name})" : '')
                );
                return [$livestock->uuid => $label];
            })
            ->toArray();
    })
    ->searchable()
    ->required()
    ->disabled(fn (Get $get) => !$get('farmUuid'))  // ← Disabled until farm selected
    ->helperText(fn (Get $get) => !$get('farmUuid') 
        ? 'Please select a farm first' 
        : null)
```

#### UUID Field with Custom Format
```php
TextInput::make('uuid')
    ->label('UUID')
    ->default(function () use ($eventType) {
        $timestamp = now()->timestamp;
        $uuid = UuidHelper::generate();
        return "{$timestamp}-{$uuid}-{$eventType}";
    })
    ->readOnly()
    ->required()
    ->maxLength(255)
    ->helperText('Auto-generated: timestamp-uuid-eventType')
```

---

### 5. Birth Event Specific Features

#### Auto-Detection of Event Type
```php
->afterStateUpdated(function ($state, callable $set) {
    if ($state) {
        $livestock = Livestock::where('uuid', $state)->first();
        if ($livestock && $livestock->species) {
            // Pig → Farrowing, Others → Calving
            $eventType = strtolower($livestock->species->name) === 'pig' 
                ? 'farrowing' 
                : 'calving';
            $set('eventType', $eventType);
        }
    }
})
```

#### Dynamic Filtering of Birth Types
```php
Select::make('birthTypeId')
    ->label(fn (Get $get) => $get('eventType') === 'farrowing' 
        ? 'Farrowing Type' 
        : 'Birth Type')
    ->options(function (Get $get) {
        $livestockUuid = $get('livestockUuid');
        if (!$livestockUuid) {
            return BirthType::pluck('name', 'id');
        }
        
        $livestock = Livestock::where('uuid', $livestockUuid)->first();
        if (!$livestock || !$livestock->livestockTypeId) {
            return BirthType::pluck('name', 'id');
        }
        
        // Filter by livestock type
        return BirthType::where('livestockTypeId', $livestock->livestockTypeId)
            ->pluck('name', 'id');
    })
```

---

### 6. UI/UX Improvements

#### Visual Cues
- ✅ **Disabled state** - Livestock field is visibly disabled until farm is selected
- ✅ **Helper text** - "Please select a farm first" when disabled
- ✅ **Species info** - Shows species in livestock label: "001 - Bessie (Cattle)"
- ✅ **UUID format** - Helper text explains the format
- ✅ **Read-only** - UUID field cannot be edited

#### Form Validation
- ✅ **Required fields** - Farm and Livestock are required
- ✅ **Data integrity** - Cannot select livestock from wrong farm
- ✅ **Clear feedback** - Helpers guide user through form

#### Responsive Behavior
- ✅ **Immediate updates** - Livestock dropdown updates as soon as farm changes
- ✅ **State clearing** - Previous selections are cleared when dependencies change
- ✅ **Smart defaults** - Event type auto-sets based on livestock

---

### 7. Benefits

#### For Users
1. **Easier data entry** - Clear, guided workflow
2. **Fewer errors** - Cannot select wrong livestock for farm
3. **Better context** - See species when selecting livestock
4. **Faster workflow** - Auto-detection of event types
5. **Clear feedback** - Helper text and disabled states guide them

#### For System
1. **Data integrity** - Farm-Livestock relationships are always correct
2. **Traceable UUIDs** - Can sort and filter by timestamp and type
3. **Maintainable code** - Reusable helper components
4. **Consistent UX** - All event logs follow same pattern
5. **Scalable** - Easy to add new event types

#### For Developers
1. **DRY principle** - Helpers avoid code duplication
2. **Easy to extend** - Add new event types with one line
3. **Type safety** - UUID format is consistent
4. **Testing** - Can test helpers independently
5. **Documentation** - Clear, self-explanatory code

---

### 8. UUID Format Breakdown

```
1733155200-abc123def456-birth
│          │               │
│          │               └─ Event Type (birth, feeding, etc.)
│          └───────────────── Unique Identifier
└──────────────────────────── Unix Timestamp (sortable)
```

**Advantages:**
- **Chronological sorting**: Database queries can sort by UUID
- **Type identification**: Know the event type at a glance
- **Debugging**: Easy to trace events by time range
- **Analytics**: Can group/filter by event type suffix
- **Human-readable**: Timestamp can be converted to date

**Example Query:**
```sql
-- Get all feeding events from today
SELECT * FROM feedings 
WHERE uuid LIKE '1733155%-feeding'
ORDER BY uuid DESC;
```

---

### 9. Testing Guide

#### Test Dynamic Livestock Filtering
1. Open any event log create form (e.g., Birth Events)
2. Verify livestock dropdown is disabled
3. Try to click it → Should not open (with helper text)
4. Select "Farm A"
5. Livestock dropdown becomes enabled
6. Verify it only shows livestock from Farm A
7. Select a livestock
8. Change farm to "Farm B"
9. Verify livestock selection is cleared
10. Verify dropdown now shows Farm B's livestock only

#### Test UUID Generation
1. Create a new birth event
2. Check UUID field shows: `{timestamp}-{uuid}-birth`
3. Verify field is read-only (cannot edit)
4. Save the record
5. Check database → UUID format matches expected pattern
6. Create another event → UUID should be different

#### Test Birth Event Auto-Detection
1. Create new birth event
2. Select a farm with both cattle and pigs
3. Select a cattle livestock
   - Verify event type auto-sets to "Calving"
   - Verify birth type label shows "Birth Type"
4. Change to a pig livestock
   - Verify event type auto-sets to "Farrowing"
   - Verify birth type label shows "Farrowing Type"

---

### 10. Migration Notes

#### Existing Data
- Old UUIDs will remain as-is (no breaking changes)
- New records will use the new format
- Both formats are valid and supported
- Database doesn't enforce specific UUID format

#### Rollback Plan
If needed, revert by changing UUID generation back to:
```php
->default(fn () => UuidHelper::generate())
```

---

### 11. Future Enhancements

### Potential Improvements
- [ ] Add date range to UUID (YYYYMMDD prefix)
- [ ] Add user ID to UUID for audit trails
- [ ] Implement UUID search by pattern
- [ ] Add UUID format validator
- [ ] Export functionality to filter by UUID pattern
- [ ] Dashboard widgets showing events by type (from UUID)

### Additional Dynamic Features
- [ ] Filter vaccines by livestock type
- [ ] Filter medicines by disease
- [ ] Auto-fill dosage based on livestock weight
- [ ] Suggest feeding type based on livestock age/stage
- [ ] Historical data display for selected livestock

---

## Summary

All event log forms now have:
- ✅ **Improved UUID**: `timestamp-uuid-eventType` format
- ✅ **Dynamic Livestock**: Filtered by selected farm
- ✅ **Cascade Clearing**: Selections clear when dependencies change
- ✅ **Visual Feedback**: Disabled states and helper text
- ✅ **Consistent UX**: All forms follow the same pattern
- ✅ **Reusable Code**: Helper components avoid duplication
- ✅ **Data Integrity**: Enforced farm-livestock relationships

The admin panel now provides a **better, safer, and more intuitive** experience for data entry!

