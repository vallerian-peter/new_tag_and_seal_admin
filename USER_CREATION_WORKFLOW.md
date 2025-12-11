# User Creation Workflow

## Overview

The User table contains ALL users across the system (farmers, system users, vets, extension officers). Each user's `roleId` points to their profile in either the `farmers` table or `system_users` table depending on their role.

---

## How to Create a New User

### Step 1: Create Profile First

Based on the role you want to assign, create the appropriate profile:

#### **For Farmer Role:**
1. Go to **Farmers** resource
2. Click **"New Farmer"**
3. Fill in all farmer details:
   - farmerNo, firstName, middleName, surname
   - phone1, phone2, email
   - gender, dateOfBirth
   - Location details (country, region, district, ward, village)
   - Identity card info
   - Farmer type (individual/organization)
4. Click **Save**
5. **Note the ID** of the created farmer (e.g., ID: 45)

#### **For SystemUser/ExtensionOfficer/Vet/FarmInvitedUser Roles:**
1. Go to **System User Profiles** resource
2. Click **"New System User Profile"**
3. Fill in details:
   - firstName, middleName, lastName
   - phone
   - address
4. Click **Save**
5. **Note the ID** of the created system user (e.g., ID: 3)

---

### Step 2: Create User Account

1. Go to **Users** resource
2. Click **"New User"**
3. Fill in **Account Information:**
   - **Username:** Unique username for login
   - **Email:** Email address
   - **Password:** Login password
   - **Role:** Select from dropdown:
     - System User
     - Farmer
     - Extension Officer
     - Veterinarian
     - Farm Invited User
   - **Status:** Active or Not Active

4. Fill in **Profile Information:**
   - **roleId:** Enter the ID from Step 1
     - If role = Farmer → Enter Farmer ID from Farmers table
     - If role = SystemUser/ExtensionOfficer/Vet → Enter ID from System User Profiles table

5. Click **Save**

---

## Example Workflows

### Example 1: Creating a Farmer User

**Step 1 - Create Farmer Profile:**
```
Navigate to: Farmers → New Farmer

Fill in:
- firstName: John
- middleName: Peter
- surname: Doe
- phone1: +255712345678
- gender: male
- ... (other fields)

Save → Gets ID: 45
```

**Step 2 - Create User Account:**
```
Navigate to: Users → New User

Fill in:
- username: johndoe
- email: john@example.com
- password: SecurePass123
- role: Farmer  ← Select this
- roleId: 45    ← Use the Farmer ID from Step 1
- status: Active

Save → User created! ✅
```

**Result:** User "johndoe" can now login and access as a Farmer

---

### Example 2: Creating a System User (Admin)

**Step 1 - Create System User Profile:**
```
Navigate to: System User Profiles → New System User Profile

Fill in:
- firstName: Admin
- middleName: (optional)
- lastName: User
- phone: +255700000000
- address: Dar es Salaam

Save → Gets ID: 3
```

**Step 2 - Create User Account:**
```
Navigate to: Users → New User

Fill in:
- username: admin2
- email: admin2@tagandseals.com
- password: AdminPass123
- role: System User  ← Select this
- roleId: 3          ← Use the System User Profile ID from Step 1
- status: Active

Save → User created! ✅
```

**Result:** User "admin2" can now login and access the admin panel

---

## Navigation Structure

### People & Users Group:
1. **Users** (All user accounts)
   - Shows: username, email, role, status
   - Actions: View, Edit, Delete
   
2. **Farmers** (Farmer profiles)
   - Shows: farmerNo, full name, phone, location
   - Actions: View, Edit, Delete
   
3. **System User Profiles** (Admin/Staff profiles)
   - Shows: full name, phone, address
   - Actions: Edit, Delete

---

## Important Notes

### ✅ **Why This Two-Step Process?**

The system uses a **polymorphic profile pattern**:
- User table stores login credentials (username, password, role)
- Farmer table stores farmer-specific data (farmerNo, surname, etc.)
- SystemUser table stores staff data (lastName instead of surname)
- User.roleId → Points to the appropriate profile table based on role

**Benefits:**
- Clean separation of auth and profile data
- Different profile structures for different roles
- Flexible role-based system

### ⚠️ **Important:**
- Always create the profile FIRST before creating the User
- The roleId MUST match the ID of the profile you created
- Wrong roleId = User won't be able to load their profile
- Delete cascade should handle deleting profiles when users are deleted

---

## Role → Profile Table Mapping

| User Role | roleId → | Profile Table | Required Fields |
|-----------|----------|---------------|-----------------|
| farmer | Farmer ID | `farmers` | farmerNo, firstName, **surname**, phone1, gender |
| systemUser | SystemUser ID | `system_users` | firstName, **lastName**, phone |
| extensionOfficer | SystemUser ID | `system_users` | firstName, **lastName**, phone |
| vet | SystemUser ID | `system_users` | firstName, **lastName**, phone |
| farmInvitedUser | SystemUser ID | `system_users` | firstName, **lastName**, phone |

**Note:** Farmers use `surname`, others use `lastName`

---

## Quick Reference

### To Create Farmer User:
1. Farmers → New Farmer → Save (note ID)
2. Users → New User → Select role: Farmer → Enter roleId → Save

### To Create Admin User:
1. System User Profiles → New → Save (note ID)
2. Users → New User → Select role: System User → Enter roleId → Save

### To Create Extension Officer:
1. System User Profiles → New → Save (note ID)
2. Users → New User → Select role: Extension Officer → Enter roleId → Save

---

## Future Enhancement Ideas

- Auto-create profile when creating user (wizard approach)
- Select existing profile from dropdown instead of entering ID
- Show profile preview when roleId is entered
- Validate roleId exists in the correct table

---

**Date:** October 26, 2025  
**Status:** 🟢 **Workflow Documented**























