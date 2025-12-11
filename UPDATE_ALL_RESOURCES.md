# Resource Updates Plan

## Navigation Groups & Icons

### 1. Geographical (Location Data)
- **Icon Theme:** Map/Location icons
- **Actions:** Edit + Delete only (no View)
- **Resources:**
  - Countries → 'heroicon-o-globe-americas' → Country Management
  - Regions → 'heroicon-o-map' → Regional Divisions  
  - Districts → 'heroicon-o-map-pin' → District Administration
  - Wards → 'heroicon-o-building-office' → Ward Management
  - Villages → (need to create) 'heroicon-o-home-modern' → Village Records
  - Streets → (need to create) 'heroicon-o-road' → Street Registry
  - Divisions → (need to create) 'heroicon-o-squares-2x2' → Division Management

### 2. People & Users  
- **Icon Theme:** User/People icons
- **Actions:** View + Edit + Delete (ThreeDotsVert)
- **Resources:**
  - Users → 'heroicon-o-users' → System Users
  - Farmers → 'heroicon-o-user-group' → Farmer Profiles
  - SystemUsers → (need to create) 'heroicon-o-user-circle' → Admin Profiles

### 3. Livestock & Data
- **Icon Theme:** Animal/Farm icons
- **Actions:** View + Edit + Delete (ThreeDotsVert)
- **Resources:**
  - Farms → 'heroicon-o-building-storefront' → Farm Management
  - Livestock → 'heroicon-o-cube' → Livestock Records
  - Breeds → 'heroicon-o-tag' → Breed Registry
  - Species → 'heroicon-o-beaker' → Species Classification
  - LivestockTypes → 'heroicon-o-squares-plus' → Livestock Types
  - LivestockObtainedMethods → 'heroicon-o-arrow-path' → Acquisition Methods

### 4. System & Configuration (Reference Data)
- **Icon Theme:** Settings/Config icons
- **Actions:** Edit + Delete only (no View)
- **Resources:**
  - LegalStatus → (need to create) 'heroicon-o-scale' → Legal Statuses
  - IdentityCardTypes → (need to create) 'heroicon-o-identification' → ID Card Types
  - SchoolLevels → (need to create) 'heroicon-o-academic-cap' → Education Levels

## Actions Configuration

### Reference Data & Locations (Edit + Delete only):
- Countries, Regions, Districts, Wards, Villages, Streets, Divisions
- LegalStatus, IdentityCardTypes, SchoolLevels
- Breeds, Species, LivestockTypes, LivestockObtainedMethods

### Main Data (View + Edit + Delete with ThreeDotsVert):
- Users, Farmers, SystemUsers
- Farms, Livestock

## InfoList Views (for View action)
Create InfoList schemas for:
- Users
- Farmers
- Farms
- Livestock

