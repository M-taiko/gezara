<?php

namespace App\Http\Controllers\Udhiya;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'main_category_id' => 'required|exists:main_categories,id',
        ], [
            'name.required'             => 'اسم النوعية مطلوب.',
            'main_category_id.required' => 'يجب اختيار الفئة.',
        ]);

        Product::create([
            'name'             => $request->name,
            'main_category_id' => $request->main_category_id,
            'is_active'        => true,
        ]);

        return back()->with('toast_success', 'تم إضافة النوعية بنجاح.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'main_category_id' => 'required|exists:main_categories,id',
        ]);

        $product->update([
            'name'             => $request->name,
            'main_category_id' => $request->main_category_id,
        ]);

        return back()->with('toast_success', 'تم تعديل النوعية بنجاح.');
    }

    public function destroy(Product $product)
    {
        if ($product->animals()->exists()) {
            return back()->with('toast_error', 'لا يمكن حذف نوعية مرتبطة بحيوانات.');
        }

        $product->delete();
        return back()->with('toast_success', 'تم حذف النوعية.');
    }
}
