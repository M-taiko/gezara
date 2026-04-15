<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DarkModeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\TypingIndicatorController;
use App\Http\Controllers\Api\UserSearchController;
// Udhiya Controllers
use App\Http\Controllers\Udhiya\DashboardController;
use App\Http\Controllers\Udhiya\SupplierController;
use App\Http\Controllers\Udhiya\PurchaseController;
use App\Http\Controllers\Udhiya\AnimalController;
use App\Http\Controllers\Udhiya\CustomerController;
use App\Http\Controllers\Udhiya\ContractController;
use App\Http\Controllers\Udhiya\PaymentController;
use App\Http\Controllers\Udhiya\ReportController;
use App\Http\Controllers\Udhiya\SlaughterGroupController;
use App\Http\Controllers\Udhiya\ProductController;
use App\Http\Controllers\Udhiya\ExpenseController;
use App\Http\Controllers\Udhiya\MeatInventoryController;
use App\Http\Controllers\Udhiya\MeatSaleController;
use App\Http\Controllers\Udhiya\WalletController;
use App\Http\Controllers\Udhiya\ContractRequestController;
use App\Http\Controllers\PublicController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
    Route::get('/signin', [AuthController::class, 'showSignin'])->name('signin');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Company Settings Routes
Route::middleware('auth')->group(function () {
    Route::get('/company/settings', [CompanyController::class, 'show'])->name('company.settings');
    Route::put('/company/settings', [CompanyController::class, 'update'])->name('company.update');
});

// Dark Mode Routes
Route::middleware('auth')->group(function () {
    Route::post('/api/dark-mode/toggle', [DarkModeController::class, 'toggle'])->name('dark-mode.toggle');
});

// Messaging Routes
Route::middleware('auth')->group(function () {
    // Message CRUD
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::put('/messages/{message}', [MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // Message API endpoints
    Route::get('/api/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/api/messages/recent', [MessageController::class, 'getRecentMessages'])->name('messages.recent');
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

    // Message Reactions
    Route::post('/messages/{message}/reactions', [MessageReactionController::class, 'store'])->name('messages.reactions.store');
    Route::delete('/messages/{message}/reactions/{reactionType}', [MessageReactionController::class, 'destroy'])->name('messages.reactions.destroy');

    // Typing Indicators
    Route::post('/api/typing/start', [TypingIndicatorController::class, 'start'])->name('typing.start');
    Route::post('/api/typing/stop', [TypingIndicatorController::class, 'stop'])->name('typing.stop');
});

// User Search API Routes
Route::middleware('auth')->group(function () {
    Route::get('/api/users/search', [UserSearchController::class, 'search'])->name('api.users.search');
    Route::get('/api/users/{userId}', [UserSearchController::class, 'show'])->name('api.users.show');
});

// Admin Routes - Users and Permissions Management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users CRUD
    Route::resource('users', UserController::class);
    Route::post('users/{id}/status', [UserController::class, 'updateStatus'])->name('users.status');
    Route::get('users/export/excel', [UserController::class, 'export'])->name('users.export');

    // Roles CRUD
    Route::resource('roles', RoleController::class);
    Route::post('roles/assign/{userId}', [RoleController::class, 'assignRole'])->name('roles.assign');

    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs/user/{userId}', [ActivityLogController::class, 'userActivity'])->name('activity-logs.user');
    Route::get('activity-logs/export/excel', [ActivityLogController::class, 'export'])->name('activity-logs.export');
});

// ============================================================
// UDHIYA (Sacrifice Management) Routes — Clean Architecture
// ============================================================
Route::middleware('auth')->prefix('udhiya')->name('udhiya.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Wallets
    Route::resource('wallets', WalletController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('wallets/transfer', [WalletController::class, 'transfer'])->name('wallets.transfer');

    // Suppliers (Modal CRUD)
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);
    Route::post('suppliers/{supplier}/pay', [SupplierController::class, 'pay'])->name('suppliers.pay');

    // Purchases
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    // Animals
    Route::get('animals', [AnimalController::class, 'index'])->name('animals.index');
    Route::get('animals-by-warehouse', [AnimalController::class, 'byWarehouse'])->name('animals.by-warehouse');
    Route::post('animals', [AnimalController::class, 'store'])->name('animals.store');
    Route::get('animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');
    Route::post('animals/{animal}/set-grouped', [AnimalController::class, 'setGrouped'])->name('animals.set-grouped');
    Route::post('animals/{animal}/unset-grouped', [AnimalController::class, 'unsetGrouped'])->name('animals.unset-grouped');
    Route::post('animals/{animal}/transfer', [AnimalController::class, 'transfer'])->name('animals.transfer');
    Route::patch('animals/{animal}/prices', [AnimalController::class, 'updatePrices'])->name('animals.update-prices');
    Route::patch('animals/{animal}/code', [AnimalController::class, 'updateCode'])->name('animals.update-code');

    // Products (Modal CRUD — from animals page)
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::patch('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Customers (Modal CRUD)
    Route::resource('customers', CustomerController::class)->except(['show', 'create', 'edit']);

    // Contracts
    Route::resource('contracts', ContractController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('contracts/{contract}/print', [ContractController::class, 'printView'])->name('contracts.print');
    Route::patch('contract-items/{item}/assign-animal', [ContractController::class, 'assignAnimal'])->name('contract-items.assign-animal');

    // Payments
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}/print', [PaymentController::class, 'printView'])->name('payments.print');

    // Slaughter Groups
    Route::resource('groups', SlaughterGroupController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('groups/from-contracts', [SlaughterGroupController::class, 'storeFromContracts'])->name('groups.store-from-contracts');
    Route::post('groups/{group}/members', [SlaughterGroupController::class, 'addMember'])->name('groups.members.add');
    Route::delete('groups/{group}/members/{member}', [SlaughterGroupController::class, 'removeMember'])->name('groups.members.remove');
    Route::patch('groups/{group}/assign-animal', [SlaughterGroupController::class, 'assignAnimal'])->name('groups.assign-animal');
    Route::post('groups/{group}/slaughter', [SlaughterGroupController::class, 'slaughter'])->name('groups.slaughter');
    Route::patch('groups/{group}/members/{member}/deliver', [SlaughterGroupController::class, 'deliverMember'])->name('groups.members.deliver');

    // Meat Inventory (ثلاجة)
    Route::get('meat-inventory', [MeatInventoryController::class, 'index'])->name('meat-inventory.index');
    Route::patch('meat-inventory/{item}/deliver', [MeatInventoryController::class, 'deliver'])->name('meat-inventory.deliver');
    Route::delete('meat-inventory/{item}', [MeatInventoryController::class, 'destroy'])->name('meat-inventory.destroy');

    // Meat Sales (بيع اللحوم)
    Route::post('meat-sales', [MeatSaleController::class, 'store'])->name('meat-sales.store');
    Route::delete('meat-sales/{sale}', [MeatSaleController::class, 'destroy'])->name('meat-sales.destroy');

    // Expenses
    Route::get('expenses',              [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses',             [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Contract Requests
    Route::get('contract-requests', [ContractRequestController::class, 'index'])->name('contract-requests.index');
    Route::post('contract-requests', [ContractRequestController::class, 'store'])->name('contract-requests.store');
    Route::patch('contract-requests/{contractRequest}/status', [ContractRequestController::class, 'updateStatus'])->name('contract-requests.update-status');
    Route::post('contract-requests/{contractRequest}/convert', [ContractRequestController::class, 'convertToContract'])->name('contract-requests.convert');

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/animals', [ReportController::class, 'animals'])->name('reports.animals');
    Route::get('reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('reports/slaughter', [ReportController::class, 'slaughter'])->name('reports.slaughter');
    Route::get('reports/customer/{customer}', [ReportController::class, 'customer'])->name('reports.customer');
    Route::get('reports/supplier/{supplier}', [ReportController::class, 'supplier'])->name('reports.supplier');
});

// Public livestock listing — no auth required
Route::get('/', [PublicController::class, 'livestock'])->name('public.livestock');
