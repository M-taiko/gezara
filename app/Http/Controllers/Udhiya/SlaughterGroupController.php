<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Customer;
use App\Models\SlaughterGroup;
use App\Models\SlaughterGroupMember;
use Illuminate\Http\Request;

class SlaughterGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $groups = SlaughterGroup::with([
            'animal.product.mainCategory',
            'members.customer',
            'members.contractItem.contract',
        ])
        ->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('members.customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return view('udhiya.groups.index', compact('groups', 'search'));
    }

    public function create()
    {
        $animals = Animal::with('product.mainCategory')
            ->whereIn('status', ['available', 'partially_allocated'])
            ->orderBy('code')
            ->get();

        $shareLabels = SlaughterGroup::SHARE_LABELS;

        return view('udhiya.groups.create', compact('animals', 'shareLabels'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'animal_id'     => 'nullable|exists:animals,id',
            'share_type'    => 'required|in:seven,five,quarter,half,full',
            'slaughter_day' => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        $group = SlaughterGroup::create($data);

        return redirect()->route('udhiya.groups.show', $group)
            ->with('toast_success', 'تم إنشاء المجموعة بنجاح');
    }

    public function show(SlaughterGroup $group)
    {
        $group->load([
            'animal.product.mainCategory',
            'animal.shareSetting',
            'members.customer',
            'members.contractItem.contract.payments',
        ]);

        // Calculate price per share from the animal
        $priceField    = 'price_' . $group->share_type;
        $pricePerShare = $group->animal ? (float) ($group->animal->$priceField ?? 0) : 0;

        $customers = Customer::orderBy('name')->get();
        $animals   = Animal::with('product.mainCategory')
            ->whereIn('status', ['available', 'partially_allocated'])
            ->orderBy('code')
            ->get();

        return view('udhiya.groups.show', compact('group', 'customers', 'animals', 'pricePerShare'));
    }

    public function assignAnimal(Request $request, SlaughterGroup $group)
    {
        $data = $request->validate([
            'animal_id' => 'nullable|exists:animals,id',
        ]);

        $group->update(['animal_id' => $data['animal_id'] ?: null]);

        return back()->with('toast_success', 'تم تعيين الحيوان للمجموعة');
    }

    public function addMember(Request $request, SlaughterGroup $group)
    {
        // Quick-create customer if name provided and no existing customer selected
        $customerId = $request->input('customer_id');

        if (!$customerId && $request->filled('new_customer_name')) {
            $request->validate([
                'new_customer_name'  => 'required|string|max:255',
                'new_customer_phone' => 'nullable|string|max:20',
            ]);
            $customer   = Customer::create([
                'name'  => $request->input('new_customer_name'),
                'phone' => $request->input('new_customer_phone'),
            ]);
            $customerId = $customer->id;
        }

        $group->load('members');
        $data = $request->validate([
            'shares_count' => 'required|integer|min:1|max:' . $group->totalSlots(),
            'notes'        => 'nullable|string',
        ]);

        if (!$customerId) {
            return back()->with('toast_error', 'يرجى اختيار عميل أو إدخال اسم عميل جديد');
        }

        $exists = SlaughterGroupMember::where('group_id', $group->id)
            ->where('customer_id', $customerId)
            ->exists();

        if ($exists) {
            return back()->with('toast_error', 'هذا العميل مضاف بالفعل في المجموعة');
        }

        if ($group->remainingSlots() < $data['shares_count']) {
            return back()->with('toast_error', 'لا تتوفر أنصبة كافية في هذه المجموعة');
        }

        SlaughterGroupMember::create([
            'group_id'     => $group->id,
            'customer_id'  => $customerId,
            'shares_count' => $data['shares_count'],
            'notes'        => $data['notes'] ?? null,
        ]);

        return back()->with('toast_success', 'تم إضافة العضو للمجموعة');
    }

    public function removeMember(SlaughterGroup $group, SlaughterGroupMember $member)
    {
        if ($member->contract_item_id) {
            return back()->with('toast_error', 'لا يمكن حذف عضو مربوط بصك — ألغِ الصك أولاً');
        }

        $member->delete();

        return back()->with('toast_success', 'تم حذف العضو من المجموعة');
    }
}
