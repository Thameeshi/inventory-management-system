# 📦 Inventory Management System

A **fully functional**, interview-worthy single-user inventory management system demonstrating modern Laravel architecture, design patterns, and best practices.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vue.js)
![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=flat-square)
![SQLite](https://img.shields.io/badge/SQLite-3.x-003B57?style=flat-square&logo=sqlite)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=flat-square&logo=tailwindcss)

---

## 📋 Table of Contents

1. [Quick Start](#-quick-start)
2. [Architecture Overview](#-architecture-overview)
3. [Database Design](#-database-design)
4. [Design Patterns](#-design-patterns)
5. [Laravel Best Practices](#-laravel-best-practices)
6. [Security Practices](#-security-practices)
7. [Code Quality](#-code-quality)
8. [UI/UX Design](#-uiux-design)
9. [Project Structure](#-project-structure)
10. [API Routes](#-api-routes)

---

## 🚀 Quick Start

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup environment
cp .env.example .env && php artisan key:generate

# 3. Run migrations & seed
php artisan migrate:fresh --seed

# 4. Build assets & start server
npm run build && php artisan serve
```

**Access:** http://127.0.0.1:8000  
**Login:** `test@example.com` / `password`

---

## 🏗️ Architecture Overview

### Layered Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                          │
│  Vue 3 Components + Inertia.js (SPA without API complexity)    │
├─────────────────────────────────────────────────────────────────┤
│                     APPLICATION LAYER                           │
│  Controllers (HTTP handling) + Form Requests (Validation)      │
├─────────────────────────────────────────────────────────────────┤
│                       SERVICE LAYER                             │
│  InventoryService (Business Logic + Transaction Management)    │
├─────────────────────────────────────────────────────────────────┤
│                      REPOSITORY LAYER                           │
│  ItemRepository (Data Access Abstraction)                      │
├─────────────────────────────────────────────────────────────────┤
│                        DATA LAYER                               │
│  Eloquent Models + SQLite Database                             │
└─────────────────────────────────────────────────────────────────┘
```

### Data Flow

```
User Action → Vue Component → Inertia Request → Controller
                                                    ↓
                                            Form Request (Validation)
                                                    ↓
                                            Service Layer (Business Logic)
                                                    ↓
                                            Repository (Data Access)
                                                    ↓
                                            Eloquent Model → Database
```

---

## 🗄️ Database Design

### Entity Relationship Diagram

```
┌──────────────────────┐         ┌────────────────────────────────┐
│        users         │         │            items               │
├──────────────────────┤         ├────────────────────────────────┤
│ id (PK)              │         │ id (PK)                        │
│ name                 │         │ name (UNIQUE, INDEXED)         │
│ email (UNIQUE)       │         │ unit (INDEXED)                 │
│ password (HASHED)    │         │ quantity (DECIMAL 10,2)        │
│ created_at           │         │ created_at                     │
│ updated_at           │         │ updated_at                     │
└──────────────────────┘         └────────────────────────────────┘
                                              │
                                              │ 1:N (Foreign Key)
                                              ▼
                                 ┌────────────────────────────────┐
                                 │   inventory_transactions       │
                                 ├────────────────────────────────┤
                                 │ id (PK)                        │
                                 │ item_id (FK, INDEXED)          │
                                 │ type (ENUM: add/deduct)        │
                                 │ quantity (DECIMAL 10,2)        │
                                 │ note (NULLABLE)                │
                                 │ created_at (INDEXED)           │
                                 │ updated_at                     │
                                 └────────────────────────────────┘
```

### Schema Design Decisions

| Decision | Rationale |
|----------|-----------|
| `name` UNIQUE constraint | Prevents duplicate items |
| `quantity` DECIMAL(10,2) | Precise decimal storage for measurements |
| `type` ENUM | Database-level constraint for valid values |
| Composite indexes | Optimized for common query patterns |
| CASCADE delete | Maintains referential integrity |

---

## 🎨 Design Patterns

### 1. **Repository Pattern**
```php
// Interface defines contract
interface ItemRepositoryInterface {
    public function findById(int $id): ?Item;
    public function getAll(array $filters = []): Collection;
    // ...
}

// Concrete implementation
class ItemRepository implements ItemRepositoryInterface {
    public function findById(int $id): ?Item {
        return Item::find($id);
    }
}
```

**Benefits:**
- Abstracts data access logic
- Easy to swap implementations (e.g., for testing)
- Centralizes query logic

### 2. **Service Layer Pattern**
```php
class InventoryService {
    public function __construct(
        private ItemRepositoryInterface $itemRepository
    ) {}

    public function addItems(array $items): array {
        return DB::transaction(function () use ($items) {
            // Business logic here
        });
    }
}
```

**Benefits:**
- Separates business logic from controllers
- Enables code reuse
- Simplifies testing

### 3. **Dependency Injection**
```php
// AppServiceProvider.php
$this->app->bind(
    ItemRepositoryInterface::class,
    ItemRepository::class
);

// Controller automatically receives injected dependency
public function __construct(
    private readonly InventoryService $inventoryService
) {}
```

### 4. **PHP 8.1 Enums**
```php
enum TransactionType: string {
    case ADD = 'add';
    case DEDUCT = 'deduct';

    public function label(): string {
        return match ($this) {
            self::ADD => 'Addition',
            self::DEDUCT => 'Deduction',
        };
    }
}
```

**Benefits:**
- Type safety
- IDE autocompletion
- Centralized constants

### 5. **Form Request Validation**
```php
class StoreInventoryRequest extends FormRequest {
    public function rules(): array {
        return [
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ];
    }
}
```

---

## ✅ Laravel Best Practices

### Code Organization

| Practice | Implementation |
|----------|----------------|
| **Single Responsibility** | Each class has one job (Controller→HTTP, Service→Logic, Repo→Data) |
| **Dependency Injection** | Services injected via constructor |
| **Route Model Binding** | `history(Item $item)` auto-resolves or 404s |
| **Form Requests** | Validation separated from controllers |
| **Eloquent Relationships** | `hasMany`, `belongsTo` defined on models |
| **Query Scopes** | Reusable query logic: `Item::lowStock()` |
| **Accessors** | Computed properties: `$item->status` |
| **Database Transactions** | Atomic operations in service layer |

### Model Best Practices

```php
class Item extends Model {
    // Mass assignment protection
    protected $fillable = ['name', 'unit', 'quantity'];

    // Type casting
    protected $casts = ['quantity' => 'decimal:2'];

    // Computed attributes
    protected $appends = ['status', 'status_color'];

    // Query scopes for reusable queries
    public function scopeLowStock(Builder $query): Builder {
        return $query->where('quantity', '<', 10);
    }

    // Business logic on model
    public function canDeduct(float $quantity): bool {
        return $this->quantity >= $quantity;
    }
}
```

---

## 🔒 Security Practices

### 1. **CSRF Protection**
All POST/PUT/DELETE requests automatically protected by Laravel.

### 2. **Input Validation**
```php
// Server-side validation in Form Requests
'items.*.name' => 'required|string|max:255',
'items.*.unit' => 'required|string|in:kg,m,cm,pcs,ltr,box',
'items.*.quantity' => 'required|numeric|min:0.01',
```

### 3. **SQL Injection Prevention**
```php
// Using Eloquent ORM (parameterized queries)
Item::where('name', 'like', "%{$search}%")->get();

// Search term sanitization
private function sanitizeSearchTerm(string $term): string {
    return preg_replace('/[%_]/', '', trim($term));
}
```

### 4. **Mass Assignment Protection**
```php
protected $fillable = ['name', 'unit', 'quantity'];
// Only these fields can be mass-assigned
```

### 5. **Authentication**
- Laravel Breeze with session-based auth
- Password hashing with bcrypt
- Protected routes via `auth` middleware

### 6. **XSS Prevention**
- Vue.js automatically escapes output
- Blade's `{{ }}` escapes HTML entities

### 7. **Rate Limiting**
Laravel's built-in throttling on login routes.

---

## 📊 Code Quality

### Type Declarations
```php
public function addItems(array $items): array
public function findById(int $id): ?Item
public function searchItems(array $filters = []): Collection
```

### PHPDoc Comments
```php
/**
 * Add items to inventory.
 *
 * @param array<int, array{name: string, unit: string, quantity: float}> $items
 * @return array<int, Item>
 * @throws \Throwable If transaction fails
 */
```

### Custom Exceptions
```php
class InsufficientStockException extends Exception {
    public function __construct(
        public readonly string $itemName,
        public readonly float $availableStock,
        public readonly float $requestedQuantity,
    ) {
        // Meaningful error message
    }
}
```

### Logging
```php
Log::info('Inventory added', [
    'item_id' => $item->id,
    'quantity_added' => $quantity,
    'new_total' => $item->quantity,
]);
```

---

## 🎨 UI/UX Design

### Design Principles

| Principle | Implementation |
|-----------|----------------|
| **Consistency** | Unified color scheme (emerald/cyan gradient) |
| **Feedback** | Flash messages, loading states, form validation |
| **Accessibility** | Semantic HTML, color contrast, keyboard nav |
| **Responsiveness** | Mobile-first Tailwind classes |
| **Visual Hierarchy** | Cards, typography scale, spacing |

### Key UI Components

- **StatCard** - Reusable dashboard metric display
- **InventoryTable** - Sortable, filterable item list
- **TransactionTable** - Color-coded history view
- **Filter System** - Search + dropdowns + sort + tags

### Color System
```
Primary:    emerald-500 (#10b981) - Actions, success
Secondary:  cyan-500 (#06b6d4) - Accents, links
Warning:    amber-500 (#f59e0b) - Low stock alerts
Danger:     red-500 (#ef4444) - Out of stock, errors
Background: slate-800/900 - Dark theme
```

---

## 📁 Project Structure

```
app/
├── Enums/
│   ├── StockStatus.php        # Stock level enum
│   └── TransactionType.php    # Add/deduct enum
├── Exceptions/
│   ├── InsufficientStockException.php
│   └── ItemNotFoundException.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   └── InventoryController.php
│   └── Requests/
│       ├── DeductInventoryRequest.php
│       └── StoreInventoryRequest.php
├── Models/
│   ├── Item.php               # With scopes & accessors
│   ├── InventoryTransaction.php
│   └── User.php
├── Repositories/
│   ├── Contracts/
│   │   └── ItemRepositoryInterface.php
│   └── ItemRepository.php
├── Services/
│   └── InventoryService.php   # Business logic
└── Providers/
    └── AppServiceProvider.php # DI bindings

resources/js/
├── Components/
│   ├── InventoryTable.vue
│   ├── TransactionTable.vue
│   └── StatCard.vue
├── Layouts/
│   └── AuthenticatedLayout.vue
└── Pages/
    ├── Dashboard.vue
    ├── Welcome.vue
    └── Inventory/
        ├── Index.vue
        ├── Create.vue
        ├── Deduct.vue
        └── History.vue
```

---

## 🔌 API Routes

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/` | - | Welcome page |
| GET | `/dashboard` | DashboardController@index | Dashboard with stats |
| GET | `/inventory` | InventoryController@index | List items (filterable) |
| GET | `/inventory/create` | InventoryController@create | Add item form |
| POST | `/inventory` | InventoryController@store | Store new item |
| GET | `/inventory/deduct` | InventoryController@showDeduct | Deduct form |
| POST | `/inventory/deduct` | InventoryController@deduct | Process deduction |
| GET | `/inventory/{item}/history` | InventoryController@history | Item history |

---

## 🧪 Testing (Interview Discussion)

While tests aren't implemented, here's how the architecture supports testing:

```php
// Unit test example (service layer)
public function test_deduct_throws_exception_for_insufficient_stock()
{
    $mockRepo = Mockery::mock(ItemRepositoryInterface::class);
    $mockRepo->shouldReceive('findById')->andReturn($item);
    
    $service = new InventoryService($mockRepo);
    
    $this->expectException(InsufficientStockException::class);
    $service->deductItems([['item_id' => 1, 'quantity' => 1000]]);
}
```

---

## 📝 Interview Discussion Points

1. **Why Service Layer?**
   - Keeps controllers thin
   - Business logic is reusable and testable
   - Single place for transaction management

2. **Why Repository Pattern?**
   - Abstracts data access
   - Easy to mock for testing
   - Centralizes query logic

3. **Why Enums?**
   - Type safety vs magic strings
   - IDE autocompletion
   - Centralized status/type definitions

4. **Why Inertia.js?**
   - SPA experience without API complexity
   - Shared validation between client/server
   - Server-side routing with Vue components

5. **Database Design Choices?**
   - Normalized schema (3NF)
   - Indexes on frequently queried columns
   - Foreign key constraints for integrity
   - Audit trail via transactions table

---

## 📜 License

MIT License - Built for learning and interviews!

---

<p align="center">
  <strong>Built with ❤️ demonstrating Laravel best practices</strong><br>
  Service Layer • Repository Pattern • Dependency Injection • Enums • Form Requests
</p>
