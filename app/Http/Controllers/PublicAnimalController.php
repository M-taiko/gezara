<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ContractRequest;
use Illuminate\Http\Request;

class PublicAnimalController extends Controller
{
    /**
     * عرض أنواع الأضاحي المتاحة للعملاء
     */
    public function index()
    {
        // Get all main categories
        $categories = \App\Models\MainCategory::orderBy('name')->get();

        // Prepare category data with available shares
        $categoriesData = $categories->map(function ($category) {
            // Get animals for this category
            $animals = Animal::whereHas('product', function ($q) use ($category) {
                $q->where('main_category_id', $category->id);
            })->whereIn('status', ['available', 'partially_allocated'])->get();

            if ($animals->isEmpty()) {
                return null; // Skip categories with no animals
            }

            $availableShares = [];

            foreach ($animals as $animal) {
                // Check full share availability
                $fullSold = \App\Models\ContractItem::where('animal_id', $animal->id)
                    ->where('share_type', 'full')
                    ->exists();

                if (!$fullSold && $animal->price_full) {
                    if (!isset($availableShares['full'])) {
                        $availableShares['full'] = ['label' => 'كامل', 'prices' => []];
                    }
                    $availableShares['full']['prices'][] = (float)$animal->price_full;
                }

                // Check fractional shares
                if ($animal->shareSetting && $animal->shareSetting->remaining_shares > 0) {
                    $shareType = $animal->shareSetting->share_type;

                    $shareLabels = [
                        'seven'   => 'سُبع',
                        'six'     => 'سُدس',
                        'five'    => 'خُمس',
                        'quarter' => 'ربع',
                        'third'   => 'ثُلث',
                        'half'    => 'نصف'
                    ];

                    $priceField = 'price_' . $shareType;
                    $price = $animal->$priceField ?? 0;

                    if ($price > 0) {
                        if (!isset($availableShares[$shareType])) {
                            $availableShares[$shareType] = ['label' => $shareLabels[$shareType], 'prices' => []];
                        }
                        $availableShares[$shareType]['prices'][] = (float)$price;
                    }
                }
            }

            // Only include category if it has available shares
            if (empty($availableShares)) {
                return null;
            }

            // Calculate price ranges
            foreach ($availableShares as $type => &$share) {
                if (!empty($share['prices'])) {
                    $share['minPrice'] = min($share['prices']);
                    $share['maxPrice'] = max($share['prices']);
                    unset($share['prices']);
                }
            }

            return [
                'id' => $category->id,
                'name' => $category->name,
                'availableShares' => $availableShares,
                'animalCount' => $animals->count(),
            ];
        })->filter()->values()->toArray(); // Remove null values

        return view('public.animals', compact('categoriesData'));
    }

    /**
     * حفظ طلب اشتراك جديد من الموقع
     */
    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:main_categories,id',
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'share_type'     => 'required|in:full,seven,six,five,quarter,third,half',
            'share_price'    => 'required|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Get the category to store with the request
        $category = \App\Models\MainCategory::findOrFail($validated['category_id']);

        // Store request with category info - no specific animal yet
        // We'll match animals later when converting to contract
        ContractRequest::create([
            'animal_id'      => null, // Will be assigned when converting to contract
            'customer_name'  => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'share_type'     => $validated['share_type'],
            'share_price'    => $validated['share_price'],
            'notes'          => 'نوع الأضحية: ' . $category->name . (!empty($validated['notes']) ? ' | ' . $validated['notes'] : ''),
            'status'         => 'pending',
        ]);

        return back()->with('toast_success', 'تم استقبال طلبك بنجاح! سيتم التواصل معك قريباً للتأكيد.');
    }
}
