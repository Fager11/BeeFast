<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
//use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

   
    public function index()
    {
        $products = Product::with(['store'])->orderBy('created_at', 'desc')->get();
        return view('products.index', compact('products'));
    }
    public function allStoresProducts()
    {
       
        $stores = \App\Models\Store::with('products')->get();

    $favoriteIds = [];
    if (auth()->check()) {
        $favoriteIds = auth()->user()
            ->favorites()
            ->pluck('products.id')
            ->toArray();
    }
    
        return view('products.all_stores',  compact('stores', 'favoriteIds'));
    }
    
    
    public function create()
    {
        $stores = Store::all();      
        //$categories = Category::all(); 
        return view('products.create', compact('stores',));
    }

  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'store_id' => 'required|exists:stores,id',
            'quantity' => 'required|integer|min:0',
            //'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $image = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'store_id' => $request->store_id,
            'quantity' => $request->quantity,
           // 'category_id' => $request->category_id,
            'image' => $image,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

   
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    
    public function edit(Product $product)
    {
        $stores = Store::all();
       // $categories = Category::all();
        return view('products.edit', compact('product', 'stores'));
    }

   
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'store_id' => 'required|exists:stores,id',
            'quantity' => 'required|integer|min:0',
            //'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'store_id' => $request->store_id,
            'quantity' => $request->quantity,
            //'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

   
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
