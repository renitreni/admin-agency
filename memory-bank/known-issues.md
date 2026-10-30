# Worker Blocklisting Feature - Known Issues & Solutions

## Issue 1: `isFraUser()` method not found on custom Resource
**Error**: `BadMethodCallException: Method App\Filament\Resources\WorkerBlocklistResource::isFraUser does not exist.`

**Root Cause**: The resource extended `Filament\Resources\Resource` instead of the project's `BaseResource` class.

**Solution**: 
```php
// Change from:
use Filament\Resources\Resource;
class WorkerBlocklistResource extends Resource

// To:
use App\Filament\Resources\BaseResource;
class WorkerBlocklistResource extends BaseResource
```

**Location**: `app/Filament/Resources/WorkerBlocklistResource.php`

---

## Issue 2: Header actions must be Filament\Actions\Action
**Error**: `InvalidArgumentException: Header actions must be an instance of Filament\Actions\Action, or Filament\Actions\ActionGroup.`

**Root Cause**: Used `Filament\Tables\Actions\Action` (table action) instead of `Filament\Actions\Action` (global/page action) in `ListRecords::getHeaderActions()`.

**Solution**:
```php
// Change from:
use Filament\Tables\Actions\Action;

// To:
use Filament\Actions\Action;
```

**Location**: `app/Filament/Resources/WorkerBlocklistResource/Pages/ListWorkerBlocklists.php`

---

## Issue 3: Route not defined after creating new Filament Resource
**Error**: `RouteNotFoundException: Route [filament.admin.resources.worker-blocklists.index] not defined.`

**Root Cause**: Filament's auto-discovery caches resource registration. New resources need cache clear.

**Solution**:
```bash
php artisan filament:upgrade
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## Issue 4: Docker storage permission issues
**Error**: `UnexpectedValueException: There is no existing directory at "/var/www/html/storage/logs" and it could not be created: Permission denied`

**Root Cause**: Docker container runs as different user, storage directories not writable.

**Solution**: Ensure Dockerfile sets correct permissions:
```dockerfile
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache
```

---

## Issue 5: Foreign key constraint in tests (sqlite)
**Error**: `SQLSTATE[23000]: Integrity constraint violation: 19 FOREIGN KEY constraint failed`

**Root Cause**: Creating `WorkerBlocklist` with `agency_id` and `blocked_by` that don't exist in sqlite test database.

**Solution**: Create agency and user with explicit IDs before creating blocklist entries:
```php
$agency = \App\Models\Agency::factory()->create(['id' => 1]);
$user = \App\Models\User::factory()->create([
    'id' => 1,
    'password' => bcrypt('test-password'),
]);
$user->agency()->attach($agency);
```

---

## Issue 6: User password hashing in tests
**Error**: `RuntimeException: Could not verify the hashed value's configuration.`

**Root Cause**: `User::factory()->create(['id' => 1])` generates invalid password hash.

**Solution**: Explicitly set password with bcrypt:
```php
$user = \App\Models\User::factory()->create([
    'id' => 1,
    'password' => bcrypt('test-password'),
]);
```

---

## Key Patterns Learned

### 1. Always extend BaseResource for Filament resources
The project's `BaseResource` provides `isFraUser()`, `authUser()`, and navigation controls. Never extend `Filament\Resources\Resource` directly.

### 2. Use correct Action class for context
- `Filament\Actions\Action` - Page header actions, global actions
- `Filament\Tables\Actions\Action` - Table row actions, bulk actions
- `Filament\Forms\Components\Actions\Action` - Form actions

### 3. Run cache clears after adding Filament resources
Filament auto-discovers resources but caches the registration. Always run `filament:upgrade` or clear caches after creating new resources.

### 4. Test database needs explicit FK setup
When testing models with foreign keys, create parent records (agency, user) with explicit IDs before creating child records.

### 5. Docker permissions must be in Dockerfile
Don't rely on runtime permission fixes. Build them into the Docker image.
