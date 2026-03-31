<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->from) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('date', '<=', $request->to);
        }

        if ($request->animal_id) {
            $query->where('animal_id', $request->animal_id);
        }

        $expenses = $query->with('animal.product')->orderByDesc('date')->orderByDesc('id')->paginate(20)->withQueryString();

        $totals = [];
        foreach (Expense::CATEGORIES as $key => $cat) {
            $totals[$key] = Expense::where('category', $key)
                ->when($request->from,      fn($q) => $q->where('date', '>=', $request->from))
                ->when($request->to,        fn($q) => $q->where('date', '<=', $request->to))
                ->when($request->animal_id, fn($q) => $q->where('animal_id', $request->animal_id))
                ->sum('amount');
        }
        $grandTotal = array_sum($totals);

        $animals = Animal::with('product')->orderBy('code')->get();

        return view('udhiya.expenses.index', compact('expenses', 'totals', 'grandTotal', 'animals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'animal_id'   => 'nullable|exists:animals,id',
            'category'    => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'description' => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'notes'       => 'nullable|string',
        ]);

        Expense::create($data);

        return back()->with('toast_success', 'تم تسجيل المصروف بنجاح.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('toast_success', 'تم حذف المصروف.');
    }
}
