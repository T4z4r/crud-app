<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function fetch()
    {
        return response()->json(['data' => Product::latest()->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        Product::create($validated + ['description' => $request->description]);
        return response()->json(['status' => 'success']);
    }

    public function show($id)
    {
        return response()->json(Product::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric'
        ]);

        Product::findOrFail($id)->update($validated + ['description' => $request->description]);
        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
}
