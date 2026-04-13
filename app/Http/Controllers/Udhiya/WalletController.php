<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Services\Udhiya\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function index()
    {
        $wallets = Wallet::with('transactions')->orderBy('is_active', 'desc')->orderBy('name')->get();
        return view('udhiya.wallets.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:cash,mobile,bank',
            'notes' => 'nullable|string',
        ]);

        Wallet::create($data + ['balance' => 0, 'is_active' => true]);

        return redirect()->route('udhiya.wallets.index')->with('toast_success', '✅ تم إضافة الخزينة بنجاح');
    }

    public function update(Request $request, Wallet $wallet)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:cash,mobile,bank',
            'notes'     => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $wallet->update($data);

        return redirect()->route('udhiya.wallets.index')->with('toast_success', '✅ تم تحديث الخزينة بنجاح');
    }

    public function destroy(Request $request, Wallet $wallet)
    {
        if ($wallet->balance != 0) {
            return back()->with('toast_error', '❌ لا يمكن حذف خزينة ليها رصيد');
        }

        $name = $wallet->name;
        $wallet->delete();

        return redirect()->route('udhiya.wallets.index')->with('toast_success', "✅ تم حذف الخزينة ({$name})");
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'from_wallet_id' => 'required|exists:wallets,id',
            'to_wallet_id'   => 'required|exists:wallets,id|different:from_wallet_id',
            'amount'         => 'required|numeric|min:0.01',
            'date'           => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $from = Wallet::findOrFail($data['from_wallet_id']);
        $to = Wallet::findOrFail($data['to_wallet_id']);

        if ($from->balance < $data['amount']) {
            return back()->with('toast_error', "❌ الرصيد غير كافي في {$from->name}");
        }

        $this->walletService->transfer($from, $to, $data['amount'], $data['date'], $data['notes']);

        return redirect()->route('udhiya.wallets.index')->with('toast_success', '✅ تم التحويل بنجاح');
    }
}
