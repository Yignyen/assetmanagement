<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Manufacturer;

class ModelController extends Controller
{

    // SHOW CREATE PAGE
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $manufacturers = Manufacturer::orderBy('name')->get();

        return view('models.create', compact('categories','manufacturers'));
    }


    // STORE MODEL FROM PAGE FORM
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'require_serial' => 'nullable'
        ]);

        AssetModel::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'manufacturer_id' => $request->manufacturer_id,
            'require_serial' => $request->has('require_serial')
        ]);

        return redirect()
            ->route('assets.create')
            ->with('success','Model created successfully');
    }



    // OPTIONAL: AJAX METHOD (you already had this)
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'require_serial' => 'boolean'
        ]);

        $model = AssetModel::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'manufacturer_id' => $request->manufacturer_id,
            'require_serial' => $request->require_serial ? 1 : 0
        ]);

        return response()->json([
            'id' => $model->id,
            'name' => $model->manufacturer
                ? $model->manufacturer->name . ' — ' . $model->name
                : $model->name,
            'manufacturer_id' => $model->manufacturer_id
        ]);
    }

}