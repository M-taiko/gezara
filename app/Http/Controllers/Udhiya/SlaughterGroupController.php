<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\MeatInventory;
use App\Models\Product;
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
            ->orderBy('code')
            ->get();

        $shareLabels = SlaughterGroup::SHARE_LABELS;
        $products = Product::with('mainCategory')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('udhiya.groups.create', compact('animals', 'shareLabels', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'animal_id'          => 'nullable|exists:animals,id',
            'animal_type_label'  => 'nullable|string|max:255',
            'share_type'         => 'required|in:' . implode(',', array_keys(\App\Models\SlaughterGroup::SHARE_MAP)),
            'slaughter_day'      => 'nullable|date',
            'notes'              => 'nullable|string',
        ]);

        $group = SlaughterGroup::create($data);

        return redirect()->route('udhiya.groups.show', $group)
            ->with('toast_success', 'تم إنشاء المجموعة بنجاح');
    }

    public function storeFromContracts(Request $request)
    {
        $request->validate([
            'contract_ids' => 'required|array|min:1',
            'contract_ids.*' => 'exists:contracts,id',
            'group_name' => 'required|string|max:255',
        ]);

        $contracts = \App\Models\Contract::with('items')->whereIn('id', $request->contract_ids)->get();

        $shareType = null;
        $totalSharesNeeded = 0;

        foreach ($contracts as $contract) {
            if ($contract->items->count() > 1) {
                return back()->with('toast_error', "الصك #" . $contract->contract_number . " يحتوي على أكثر من حيوان ولا يمكن إضافته لمجموعة.");
            }
            $item = $contract->items->first();
            if ($item->group_id) {
                return back()->with('toast_error', "الصك #" . $contract->contract_number . " مرتبط بمجموعة بالفعل.");
            }
            if ($item->animal_id) {
                return back()->with('toast_error', "الصك #" . $contract->contract_number . " مخصص له حيوان. يرجى إزالة الحيوان من الصك أولاً قبل إضافته للمجموعة.");
            }
            if (!$shareType) {
                $shareType = $item->share_type;
            } elseif ($shareType !== $item->share_type) {
                return back()->with('toast_error', "يجب أن تكون جميع الصكوك المختارة من نفس نوع الحصة.");
            }
            if ($item->share_type === 'full') {
                return back()->with('toast_error', "الصك #" . $contract->contract_number . " من نوع كامل، ولا يمكن إضافته למجموعة تشاركية.");
            }
            $totalSharesNeeded += $item->shares_count;
        }

        // Validate max shares
        $maxShares = \App\Models\Animal::SHARE_MAP[$shareType] ?? null;
        if ($maxShares && $totalSharesNeeded > $maxShares) {
            return back()->with('toast_error', "عدد الأنصبة المختارة ($totalSharesNeeded) يتجاوز الحد الأقصى للمجموعة ($maxShares).");
        }

        $group = SlaughterGroup::create([
            'name' => $request->group_name,
            'share_type' => $shareType,
        ]);

        foreach ($contracts as $contract) {
            $item = $contract->items->first();
            $item->update(['group_id' => $group->id]);

            SlaughterGroupMember::create([
                'group_id' => $group->id,
                'customer_id' => $contract->customer_id,
                'contract_item_id' => $item->id,
                'shares_count' => $item->shares_count,
            ]);
        }

        return redirect()->route('udhiya.groups.show', $group)
            ->with('toast_success', 'تم إنشاء المجموعة وربط الصكوك بنجاح.');
    }

    public function show(SlaughterGroup $group)
    {
        $group->load([
            'animal.product.mainCategory',
            'animal.shareSetting',
            'animal.meatInventory',
            'members.customer',
            'members.contractItem.contract.payments',
        ]);

        // Calculate price per share from the animal
        $priceField    = 'price_' . $group->share_type;
        $pricePerShare = $group->animal ? (float) ($group->animal->$priceField ?? 0) : 0;

        $customers = Customer::orderBy('name')->get();
        $animals   = Animal::with('product.mainCategory')
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
        // Block adding members after slaughter
        if ($group->animal?->status === 'slaughtered') {
            return back()->with('toast_error', 'لا يمكن إضافة أعضاء بعد الذبح');
        }

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

        $group->load('members', 'animal');
        $isSlaughtered = $group->animal?->status === 'slaughtered';

        $data = $request->validate([
            'shares_count'    => 'required|integer|min:1',
            'notes'           => 'nullable|string',
            'contract_number' => 'nullable|string|max:50',
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

        if (!$isSlaughtered && $group->remainingSlots() < $data['shares_count']) {
            return back()->with('toast_error', 'لا تتوفر أنصبة كافية في هذه المجموعة');
        }

        $member = SlaughterGroupMember::create([
            'group_id'     => $group->id,
            'customer_id'  => $customerId,
            'shares_count' => $data['shares_count'],
            'notes'        => $data['notes'] ?? null,
        ]);

        // If manual contract number provided → create contract + item immediately
        if (!empty($data['contract_number']) && $group->animal) {
            $priceField    = 'price_' . $group->share_type;
            $pricePerShare = (float) ($group->animal->{$priceField} ?? 0);
            $totalPrice    = $pricePerShare * $data['shares_count'];

            // Check contract_number uniqueness
            $numExists = \App\Models\Contract::where('contract_number', $data['contract_number'])->exists();
            if ($numExists) {
                $member->delete();
                return back()->with('toast_error', 'رقم الصك ' . $data['contract_number'] . ' مستخدم من قبل');
            }

            $contract = \App\Models\Contract::create([
                'customer_id'      => $customerId,
                'contract_number'  => $data['contract_number'],
                'total_amount'     => $totalPrice,
                'paid_amount'      => 0,
                'remaining_amount' => $totalPrice,
                'status'           => 'active',
                'slaughter_day'    => $group->slaughter_day,
            ]);

            $contractItem = ContractItem::create([
                'contract_id'  => $contract->id,
                'animal_id'    => $group->animal_id,
                'shares_count' => $data['shares_count'],
                'total_price'  => $totalPrice,
                'group_id'     => $group->id,
            ]);

            $member->update(['contract_item_id' => $contractItem->id]);
        }

        return back()->with('toast_success', 'تم إضافة العضو للمجموعة' . (!empty($data['contract_number']) ? ' وإنشاء الصك' : ''));
    }

    public function slaughter(SlaughterGroup $group)
    {
        $animal = $group->animal;

        if (!$animal) {
            return back()->with('toast_error', 'لا يوجد حيوان مرتبط بهذه المجموعة');
        }

        if ($animal->status === 'slaughtered') {
            return back()->with('toast_error', 'تم ذبح هذا الحيوان من قبل');
        }

        // If weight is known and shares not fully contracted → store remaining weight in inventory
        $setting = $animal->shareSetting;
        if ($animal->weight && $setting && $setting->remaining_shares > 0) {
            $remainingKg = round(
                ($setting->remaining_shares / $setting->total_shares) * $animal->weight,
                2
            );
            MeatInventory::create([
                'animal_id' => $animal->id,
                'weight_kg' => $remainingKg,
                'status'    => 'available',
                'notes'     => "أنصبة غير مبيعة من الذبيحة {$animal->code} ({$setting->remaining_shares}/{$setting->total_shares} نصيب)",
            ]);
        }

        $animal->update([
            'status'         => 'slaughtered',
            'slaughtered_at' => now(),
        ]);

        return back()->with('toast_success', "✅ تم ذبح الذبيحة {$animal->code} بنجاح");
    }

    public function deliverMember(SlaughterGroup $group, SlaughterGroupMember $member)
    {
        $item = $member->contractItem;

        if (!$item) {
            return back()->with('toast_error', 'لا يوجد صك مرتبط بهذا العضو');
        }

        if ($item->delivered_at) {
            return back()->with('toast_warning', 'تم تسليم هذا العضو مسبقاً');
        }

        $item->update(['delivered_at' => now()]);

        return back()->with('toast_success', "✅ تم تسجيل تسليم {$member->customer?->name}");
    }

    public function updateMember(Request $request, SlaughterGroup $group, SlaughterGroupMember $member)
    {
        if ($member->contract_item_id) {
            return back()->with('toast_error', 'لا يمكن تعديل عضو مربوط بصك');
        }

        $data = $request->validate([
            'shares_count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $oldShares = $member->shares_count;
        $newShares = $data['shares_count'];
        $shareDiff = $newShares - $oldShares;

        // Check if remaining slots allow the new share count
        if ($shareDiff > 0 && $group->remainingSlots() < $shareDiff) {
            return back()->with('toast_error', "لا تتوفر {$shareDiff} أنصبة إضافية في المجموعة");
        }

        $member->update($data);

        return back()->with('toast_success', "تم تحديث بيانات العضو");
    }

    public function removeMember(SlaughterGroup $group, SlaughterGroupMember $member)
    {
        if ($member->contract_item_id) {
            return back()->with('toast_error', 'لا يمكن حذف عضو مربوط بصك — ألغِ الصك أولاً');
        }

        $member->delete();

        return back()->with('toast_success', 'تم حذف العضو من المجموعة');
    }

    /**
     * Edit a slaughter group
     */
    public function edit(SlaughterGroup $group)
    {
        $group->load([
            'animal.product.mainCategory',
            'members.customer',
            'members.contractItem.contract.payments',
        ]);

        // Get animals that are:
        // 1. Not slaughtered
        // 2. Either not assigned to any group OR assigned to this group
        $animals = Animal::with('product.mainCategory')
            ->where('status', '!=', 'slaughtered')
            ->where(function ($q) use ($group) {
                $q->whereNotIn('id', function ($subquery) use ($group) {
                    $subquery->select('animal_id')
                        ->from('slaughter_groups')
                        ->where('animal_id', '!=', null)
                        ->where('id', '!=', $group->id);
                })->orWhere('id', $group->animal_id);
            })
            ->orderBy('code')
            ->get();

        $products = Product::with('mainCategory')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('udhiya.groups.edit', compact('group', 'animals', 'products'));
    }

    /**
     * Update a slaughter group
     */
    public function update(Request $request, SlaughterGroup $group)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'animal_id' => 'nullable|exists:animals,id',
            'animal_type_label' => 'nullable|string|max:255',
            'slaughter_day' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $changes = [];

        // Track what changed
        if ($group->name !== $data['name']) {
            $changes[] = "تغيير اسم المجموعة من \"{$group->name}\" إلى \"{$data['name']}\"";
        }

        $newAnimalId = $data['animal_id'] ?? null;
        if ($group->animal_id !== $newAnimalId) {
            $oldAnimal = $group->animal?->code ?? 'بدون';
            $newAnimal = Animal::find($newAnimalId)?->code ?? 'بدون';
            $changes[] = "تغيير الحيوان من \"{$oldAnimal}\" إلى \"{$newAnimal}\"";
        }

        $newLabel = $data['animal_type_label'] ?? null;
        if ($group->animal_type_label !== $newLabel) {
            $oldLabel = $group->animal_type_label ?? 'لم يحدد';
            $newLabelDisplay = $newLabel ?? 'لم يحدد';
            $changes[] = "تغيير نوع الذبيحة من \"{$oldLabel}\" إلى \"{$newLabelDisplay}\"";
        }

        if ($group->slaughter_day !== $request->input('slaughter_day')) {
            $oldDate = $group->slaughter_day?->format('Y-m-d') ?? 'لم يحدد';
            $newDate = $data['slaughter_day'] ?? 'لم يحدد';
            $changes[] = "تغيير تاريخ الذبح من \"{$oldDate}\" إلى \"{$newDate}\"";
        }

        $group->update($data);

        // Add to edit history
        if (!empty($changes)) {
            $group->addEditHistory('تعديل المجموعة', implode(', ', $changes));
        }

        return back()->with('toast_success', '✅ تم تحديث المجموعة بنجاح');
    }

    /**
     * Delete a slaughter group
     */
    public function destroy(SlaughterGroup $group)
    {
        // Can only delete if not slaughtered
        if ($group->isSlaughtered()) {
            return back()->with('toast_error', '❌ لا يمكن حذف مجموعة تم ذبح حيوانها');
        }

        // Can only delete if has no members
        if ($group->members()->count() > 0) {
            return back()->with('toast_error', '❌ لا يمكن حذف مجموعة بها أعضاء — أزل الأعضاء أولاً');
        }

        $groupName = $group->name;
        $group->delete();

        return back()->with('toast_success', "✅ تم حذف المجموعة \"{$groupName}\" بنجاح");
    }

    /**
     * Print view for slaughter group
     */
    public function printView(SlaughterGroup $group)
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

        return view('udhiya.print.group', compact('group', 'pricePerShare'));
    }
}
