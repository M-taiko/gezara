<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        // Only show active accounts (not soft deleted)
        $accounts = Account::whereNull('deleted_at')
            ->orderBy('code')
            ->get()
            ->groupBy('type');

        $deleted = Account::onlyTrashed()->orderBy('code')->get();

        return view('udhiya.accounts.index', compact('accounts', 'deleted'));
    }

    public function create()
    {
        $types = Account::TYPE_LABELS;
        return view('udhiya.accounts.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPE_LABELS)),
        ]);

        Account::create($validated);

        return redirect()->route('udhiya.accounts.index')
            ->with('toast_success', 'تم إنشاء الحساب بنجاح');
    }

    public function edit(Account $account)
    {
        $types = Account::TYPE_LABELS;
        return view('udhiya.accounts.edit', compact('account', 'types'));
    }

    public function update(Request $request, Account $account)
    {
        // Prevent editing system accounts
        if ($account->is_system) {
            return back()->with('toast_error', 'لا يمكن تعديل حسابات النظام');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPE_LABELS)),
        ]);

        $account->update($validated);

        return redirect()->route('udhiya.accounts.index')
            ->with('toast_success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy(Account $account)
    {
        // Prevent deleting system accounts
        if ($account->is_system) {
            return back()->with('toast_error', 'لا يمكن حذف حسابات النظام');
        }

        // Check if account has journal entries
        if ($account->journalItems()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف حساب له تسجيلات محاسبية. استخدم soft delete بدلاً من ذلك.');
        }

        // Soft delete the account
        $account->delete();

        return back()->with('toast_success', 'تم حذف الحساب. سيظهر في سجل الحسابات المحذوفة.');
    }

    public function restore(Account $account)
    {
        // Check if account code already exists (another account)
        $existing = Account::where('code', $account->code)
            ->whereNull('deleted_at')
            ->first();

        if ($existing && $existing->id !== $account->id) {
            return back()->with('toast_error', 'كود الحساب موجود بالفعل. لا يمكن استرجاع الحساب.');
        }

        $account->restore();

        return back()->with('toast_success', 'تم استرجاع الحساب بنجاح');
    }
}
