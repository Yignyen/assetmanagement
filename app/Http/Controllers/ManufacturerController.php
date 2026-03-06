<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    // Show create form
    public function create()
    {
        return view('manufacturers.create');
    }

    // Store manufacturer
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:manufacturers,name',
            'support_url' => 'nullable|url|max:255',
            'warranty_lookup_url' => 'nullable|url|max:255',
            'support_phone' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
        ]);

        Manufacturer::create([
            'name' => $request->name,
            'support_url' => $request->support_url,
            'warranty_lookup_url' => $request->warranty_lookup_url,
            'support_phone' => $request->support_phone,
            'support_email' => $request->support_email,
        ]);

        return redirect()
            ->route('models.create')
            ->with('success', 'Manufacturer created successfully');
    }
}