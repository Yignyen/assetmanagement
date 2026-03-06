<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    // Show create form
    public function create()
    {
        return view('categories.create');
    }

    // Store category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,accessory,component',
            'description' => 'nullable|string'
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description
        ]);

       /*  return redirect()->back()->with('success','Category created successfully'); */
        return redirect()
            ->route('assets.create')
            ->with('success','Model created successfully');
    }
    }

