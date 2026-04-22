# Deployment Notes - 2026-04-22

## Changes Made

### 1. Collections (تحصيل الدفعات)
**File:** `resources/views/udhiya/collections/edit.blade.php`

Changes:
- ✅ Added editable `reference_number` field (replaces read-only receipt number)
- ✅ Added `enctype="multipart/form-data"` to form for file uploads
- ✅ Fixed attachment display logic to properly link files from `attachment_paths` JSON

### 2. Contracts (الصكوك)
**File:** `resources/views/udhiya/contracts/edit.blade.php`

Changes:
- ✅ Added editable `contract_number` field in details section
- ✅ Added attachment handling with delete capability
- ✅ Supports reuploading and removing existing attachments

### 3. Contract Service
**File:** `app/Services/Udhiya/ContractService.php`

Changes:
- ✅ Added attachment handling in `update()` method
- ✅ Added logic to handle attachment removal and addition
- ✅ Fixed `cancel()` method to reset `paid_amount` and `remaining_amount` to 0

### 4. Composer Configuration
**File:** `composer.json`

Changes:
- ✅ Removed `@php artisan boost:update --ansi` from post-update-cmd hook
- ✅ This command was causing "no commands defined in boost namespace" error

## Deployment Steps

### On Production Server (Masarsoft):

1. **Pull latest code:**
   ```bash
   cd /path/to/gzara
   git pull origin master
   ```

2. **Run composer update (now without boost error):**
   ```bash
   composer update
   ```

3. **Clear Laravel cache:**
   ```bash
   php artisan cache:clear
   php artisan config:cache
   php artisan view:cache
   ```

4. **Verify changes:**
   - Visit `/udhiya/collections` and edit a payment
   - Visit `/udhiya/contracts` and edit a contract
   - Verify form structure matches localhost

## Testing Checklist

- [ ] **Payment Edit** - Reference number field appears and is editable
- [ ] **Payment Edit** - File upload works (form has enctype)
- [ ] **Payment Edit** - Existing attachments display correctly
- [ ] **Payment Edit** - Attachment deletion works
- [ ] **Contract Edit** - Contract number field appears and is editable
- [ ] **Contract Edit** - Attachment upload/delete works
- [ ] **Contract Cancel** - Remaining amount becomes 0
- [ ] **New Payments/Contracts** - Attachments work
- [ ] **View Payments/Contracts** - Attachments display with correct links
- [ ] **Composer Update** - Completes without boost namespace error

## Files Modified

1. `composer.json` - Removed boost:update hook
2. `resources/views/udhiya/collections/edit.blade.php` - Added form fields and attachment display
3. `resources/views/udhiya/contracts/edit.blade.php` - Added contract_number field and attachment handling
4. `app/Services/Udhiya/ContractService.php` - Added attachment handling in update() and fixed cancel()

## No Database Migrations Needed

All changes are view/logic/config changes. No database schema modifications required.

## If Issues Occur

If the production page still shows old layout:
1. Check `git status` to confirm files are updated
2. Run `php artisan view:clear` (more aggressive than view:cache)
3. Check browser cache - may need Ctrl+Shift+Delete to clear
4. Verify `storage/` directory permissions for file uploads
5. Check `storage/logs/laravel.log` for errors

## Rollback Plan

If something breaks, revert with:
```bash
git checkout HEAD -- resources/views/udhiya/collections/edit.blade.php
git checkout HEAD -- resources/views/udhiya/contracts/edit.blade.php
git checkout HEAD -- app/Services/Udhiya/ContractService.php
git checkout HEAD -- composer.json
php artisan view:clear
composer update
```
