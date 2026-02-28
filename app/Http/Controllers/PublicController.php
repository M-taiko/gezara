<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\MainCategory;

class PublicController extends Controller
{
    public function livestock()
    {
        $animals = Animal::with(['product.mainCategory', 'supplier', 'shareSetting'])
            ->orderByRaw("FIELD(status, 'available', 'partially_allocated', 'fully_allocated', 'slaughtered')")
            ->get();

        $categories = MainCategory::all();

        $totalSpots = $animals->sum(function ($a) {
            if ($a->is_grouped && $a->shareSetting) {
                return $a->shareSetting->remaining_shares;
            }
            return $a->status === 'available' ? 1 : 0;
        });

        $stats = [
            'total'       => $animals->count(),
            'available'   => $animals->whereIn('status', ['available', 'partially_allocated'])->count(),
            'allocated'   => $animals->where('status', 'fully_allocated')->count(),
            'slaughtered' => $animals->where('status', 'slaughtered')->count(),
            'total_spots' => $totalSpots,
        ];

        return view('public.livestock', compact('animals', 'categories', 'stats'));
    }
}
