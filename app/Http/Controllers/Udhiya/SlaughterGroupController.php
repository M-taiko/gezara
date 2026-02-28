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
            'animal_id'     => 'required|exists:animals,id',
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
            'members.contractItem.contract',
        ]);

        $customers = Customer::orderBy('name')->get();

        return view('udhiya.groups.show', compact('group', 'customers'));
    }

    public function addMember(Request $request, SlaughterGroup $group)
    {
        $data = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'shares_count' => 'required|integer|min:1|max:' . $group->totalSlots(),
            'notes'        => 'nullable|string',
        ]);

        // Check uniqueness manually to return a friendly error
        $exists = SlaughterGroupMember::where('group_id', $group->id)
            ->where('customer_id', $data['customer_id'])
            ->exists();

        if ($exists) {
            return back()->with('toast_error', 'هذا العميل مضاف بالفعل في المجموعة');
        }

        // Check remaining slots
        $group->load('members');
        if ($group->remainingSlots() < $data['shares_count']) {
            return back()->with('toast_error', 'لا تتوفر أنصبة كافية في هذه المجموعة');
        }

        SlaughterGroupMember::create([
            'group_id'     => $group->id,
            'customer_id'  => $data['customer_id'],
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
