<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $company = Company::getInstance();
        return view('company.settings', compact('company'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sidebar_logo_expanded' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sidebar_logo_collapsed' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $company = Company::first();
        if (!$company) {
            $company = new Company();
        }

        // Handle main logo upload
        if ($request->hasFile('logo')) {
            if ($company->logo && file_exists(public_path($company->logo))) {
                unlink(public_path($company->logo));
            }
            $logoPath = $request->file('logo')->store('company', 'public');
            $validated['logo'] = 'storage/' . $logoPath;
        }

        // Handle sidebar expanded logo upload
        if ($request->hasFile('sidebar_logo_expanded')) {
            if ($company->sidebar_logo_expanded && file_exists(public_path($company->sidebar_logo_expanded))) {
                unlink(public_path($company->sidebar_logo_expanded));
            }
            $sidebarExpandedPath = $request->file('sidebar_logo_expanded')->store('company', 'public');
            $validated['sidebar_logo_expanded'] = 'storage/' . $sidebarExpandedPath;
        }

        // Handle sidebar collapsed logo upload
        if ($request->hasFile('sidebar_logo_collapsed')) {
            if ($company->sidebar_logo_collapsed && file_exists(public_path($company->sidebar_logo_collapsed))) {
                unlink(public_path($company->sidebar_logo_collapsed));
            }
            $sidebarCollapsedPath = $request->file('sidebar_logo_collapsed')->store('company', 'public');
            $validated['sidebar_logo_collapsed'] = 'storage/' . $sidebarCollapsedPath;
        }

        $company->fill($validated)->save();

        return redirect()->route('company.settings')->with('success', 'Company settings updated successfully!');
    }
}
