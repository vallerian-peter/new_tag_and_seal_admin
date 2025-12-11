# Birth Event Admin Panel Update

## Overview
Updated the admin panel to properly handle multi-livestock type birth events (Calving for cattle, Farrowing for pigs) with improved UI/UX and proper data filtering.

## Changes Made

### 1. Birth Event Resource (`BirthEvents/`)

#### **Form Updates** (`Schemas/BirthEventForm.php`)
- ✅ Added sectioned layout for better organization
- ✅ 2-column grid layout for form fields
- ✅ Auto-detection of event type based on selected livestock species
- ✅ Dynamic filtering of Birth Types by livestock type
- ✅ Dynamic filtering of Birth Problems by livestock type
- ✅ Context-aware labels (shows "Farrowing Type" for pigs, "Birth Type" for cattle)
- ✅ Helper text for each field explaining its purpose
- ✅ Improved status dropdown with predefined options
- ✅ Live field updates using Filament's reactive forms

**Key Features:**
```php
- When user selects a livestock, the form automatically:
  1. Detects if it's a pig or cattle
  2. Sets the appropriate event type (farrowing/calving)
  3. Filters birth types to only show relevant ones
  4. Filters birth problems to only show relevant ones
  5. Updates labels dynamically
```

#### **Table/View Updates** (`Tables/BirthEventsTable.php`)
- ✅ 2-column grid layout for view modal on large screens (`modalWidth='2xl'`)
- ✅ Organized into logical sections:
  - **Basic Information**: UUID, Farm, Livestock details
  - **Event Timeline**: Start and end dates
  - **Medical Details**: Birth type, problems, reproductive issues
  - **Additional Information**: Remarks and timestamps
- ✅ Icons for each field for better visual hierarchy
- ✅ Color-coded badges (Calving = blue/info, Farrowing = yellow/warning)
- ✅ Copyable UUID field
- ✅ Dynamic modal heading based on event type
- ✅ Status badges with semantic colors

### 2. Aborted Pregnancy Resource (`AbortedPregnancies/`)

#### **Form Updates** (`Schemas/AbortedPregnancyForm.php`)
- ✅ Added sectioned layout matching Birth Events
- ✅ 2-column grid layout
- ✅ Improved status dropdown
- ✅ Better field organization
- ✅ Helper text for guidance

#### **Table/View Updates** (`Tables/AbortedPregnanciesTable.php`)
- ✅ 2-column grid layout for view modal (`modalWidth='2xl'`)
- ✅ Organized into logical sections
- ✅ Icons for visual clarity
- ✅ Color-coded status badges
- ✅ Improved date formatting

### 3. Legacy Resources Hidden

#### **Deprecated Resources** (Hidden from Navigation)
- ❌ `Calvings/CalvingResource.php` - Use BirthEvents instead
- ❌ `CalvingTypes/CalvingTypeResource.php` - Use BirthTypes instead
- ❌ `CalvingProblems/CalvingProblemResource.php` - Use BirthProblems instead

**Implementation:**
```php
protected static bool $shouldRegisterNavigation = false;
```

### 4. Reference Data Resources

#### **Birth Types** (`BirthTypes/`)
- ✅ Already properly configured with livestock type filtering
- ✅ Shows livestock type in table
- ✅ Badge shows "Generic (All Types)" for universal types

#### **Birth Problems** (`BirthProblems/`)
- ✅ Already properly configured with livestock type filtering
- ✅ Shows livestock type in table
- ✅ Badge shows "Generic (All Types)" for universal types

## Data Flow

### Creating a Birth Event
1. User selects a Farm
2. User selects Livestock from that farm
3. **Auto-detection**: System checks livestock species
   - If Pig → Sets event type to "Farrowing"
   - If Cattle → Sets event type to "Calving"
4. **Dynamic Filtering**: Birth Types and Problems are filtered by livestock type
5. User fills in remaining fields
6. Form validates and saves

### Viewing a Birth Event
1. Click "View" action on any birth event
2. Modal opens with **2-column grid** on large screens
3. Information is organized in logical sections
4. All relationships are loaded and displayed
5. Dynamic labels based on event type

## Database Schema

### `birth_events` Table
```sql
- id (primary key)
- uuid (unique)
- farmUuid (foreign key → farms.uuid)
- livestockUuid (foreign key → livestock.uuid)
- eventType (enum: 'calving', 'farrowing')
- startDate (date)
- endDate (date, nullable)
- birthTypeId (foreign key → birth_types.id, nullable)
- birthProblemsId (foreign key → birth_problems.id, nullable)
- reproductiveProblemId (foreign key → reproductive_problems.id, nullable)
- remarks (text, nullable)
- status (string)
- created_at
- updated_at
```

### `birth_types` Table
```sql
- id (primary key)
- name (string)
- livestockTypeId (foreign key → livestock_types.id, nullable)
- created_at
- updated_at
```

### `birth_problems` Table
```sql
- id (primary key)
- name (string)
- livestockTypeId (foreign key → livestock_types.id, nullable)
- created_at
- updated_at
```

## Model Relationships

### `BirthEvent` Model
```php
- birthType() → BelongsTo BirthType
- birthProblem() → BelongsTo BirthProblem
- reproductiveProblem() → BelongsTo ReproductiveProblem
- farm() → BelongsTo Farm (via farmUuid)
- livestock() → BelongsTo Livestock (via livestockUuid)

// Computed attributes
- eventName → 'Calving' or 'Farrowing'
- offspringName → 'Calf' or 'Piglet'
```

## UI/UX Improvements

### Form Layout
- **Before**: Single column, flat list of fields
- **After**: 
  - Organized sections (Basic Info, Event Details)
  - 2-column grid for efficient space usage
  - Context-aware labels
  - Helper text for guidance
  - Live field updates

### View Modal
- **Before**: Single column, modalWidth='lg'
- **After**:
  - 2-column grid on large screens
  - modalWidth='2xl' for more space
  - Organized sections
  - Icons for visual hierarchy
  - Color-coded badges
  - Copyable UUID

### Visual Hierarchy
- ✅ Section headings for grouping
- ✅ Icons next to labels
- ✅ Bold text for important values
- ✅ Badges for status and event type
- ✅ Semantic colors (success, warning, danger, info)

## Testing Checklist

### Birth Events
- [ ] Create a new birth event for cattle
  - [ ] Verify event type auto-sets to "calving"
  - [ ] Verify birth types are filtered for cattle
  - [ ] Verify birth problems are filtered for cattle
- [ ] Create a new birth event for pig
  - [ ] Verify event type auto-sets to "farrowing"
  - [ ] Verify birth types are filtered for pigs
  - [ ] Verify birth problems are filtered for pigs
  - [ ] Verify labels show "Farrowing Type" and "Farrowing Problem"
- [ ] View a birth event
  - [ ] Verify 2-column grid on large screens
  - [ ] Verify all sections are properly displayed
  - [ ] Verify reproductive problem shows correctly
- [ ] Edit a birth event
  - [ ] Verify all fields are editable
  - [ ] Verify filters still work correctly

### Aborted Pregnancies
- [ ] Create a new aborted pregnancy
  - [ ] Verify form layout is sectioned
  - [ ] Verify status dropdown works
- [ ] View an aborted pregnancy
  - [ ] Verify 2-column grid on large screens
  - [ ] Verify all fields display correctly

### Navigation
- [ ] Verify "Birth Events" appears in "Events & Logs" group
- [ ] Verify old "Calving" is hidden
- [ ] Verify "Birth Types" appears in "Logs Reference Data"
- [ ] Verify old "Calving Types" is hidden
- [ ] Verify "Birth Problems" appears in "Logs Reference Data"
- [ ] Verify old "Calving Problems" is hidden

## Migration Notes

### For Existing Data
- The `birth_events` table already exists with both calving and farrowing data
- Old `calvings` table data should have been migrated to `birth_events`
- `birth_types` and `birth_problems` tables include data for all livestock types

### For Reference Data
- Birth types should have `livestockTypeId` set for filtering
- Birth problems should have `livestockTypeId` set for filtering
- Generic types (apply to all livestock) should have `livestockTypeId = NULL`

## Admin Panel Structure

```
Events & Logs/
├── Birth Events ✅ (NEW - Unified)
│   ├── List/Create/Edit
│   └── View Modal (2-column grid)
├── Aborted Pregnancies ✅ (Updated)
│   ├── List/Create/Edit
│   └── View Modal (2-column grid)
├── Calving ❌ (Hidden)
└── ...other event logs

Logs Reference Data/
├── Birth Types ✅ (NEW - Unified)
├── Birth Problems ✅ (NEW - Unified)
├── Reproductive Problems ✅
├── Calving Types ❌ (Hidden)
├── Calving Problems ❌ (Hidden)
└── ...other reference data
```

## Next Steps

1. **Test thoroughly** - Verify all functionality works as expected
2. **Data verification** - Ensure all birth types and problems have correct `livestockTypeId`
3. **User training** - Inform users about the new unified Birth Events resource
4. **Remove old code** - After verification, consider removing deprecated Calving resources
5. **Documentation** - Update user documentation to reflect new structure

## Benefits

1. ✅ **Unified Interface** - Single resource for all birth events (cattle, pigs, etc.)
2. ✅ **Auto-detection** - System automatically sets correct event type
3. ✅ **Smart Filtering** - Only relevant types and problems are shown
4. ✅ **Better UX** - 2-column grids, sections, icons, and helper text
5. ✅ **Scalability** - Easy to add more livestock types in the future
6. ✅ **Consistency** - All event resources follow the same pattern
7. ✅ **Data Integrity** - Proper filtering prevents incorrect data entry

