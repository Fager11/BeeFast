<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'count' => ['integer','min:1'],
            'page' => ['integer','min:1'],
            'search_word' => ['string'],
            'type'=> ['string','in:store,restaurant,cafe'],
            'popular'=> ['boolean'],
            'user_mode'=> ['boolean'],
        ]);

        $query = Store::query();

        if($request->search_word)
            $query->where('name', 'like', '%'.$request->search_word.'%');

        if($request->type)
            $query->where('type', $request->type);

        if($request->popular == 1)
            $query->where('popular', 1);

        $stores = $query->paginate($request->count ?? 10);

        $noResults = false;
        if($request->search_word && $stores->total() == 0) {
            $noResults = true;
        }

        if (auth()->check() && auth()->user()->role == 'admin' && $request->user_mode != 1) {
            return view('store.admin-stores', [
                'stores' => $stores,
                'noResults' => $noResults,
            ]);
        }

        return view('store.stores', [
            'stores' => $stores,
            'noResults' => $noResults,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('store.add-store');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'image' => ['required','image'], // ,'max:2048'
            'delivery_price' => ['required','numeric','min:0'],
            'type' => ['required','string','in:store,restaurant,cafe'],
            'popular' => ['nullable','boolean'],
        ]);

        $image = $request->file('image')->store('stores', 'public');
        $popular = $request->boolean('popular');

        Store::create([
            'name' => $request->name,
            'image' => $image,
            'delivery_price' => $request->delivery_price,
            'type' => $request->type,
            'popular' => $popular,
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'تم إنشاء المتجر بنجاح.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
{

    $products = $store->products()->orderBy('created_at', 'desc')->get();

    $noProducts = $products->isEmpty();

    return view('store.store', [
        'store' => $store,
        'products' => $products,
        'noProducts' => $noProducts
    ]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        return view('store.update-store', [
            'store' => $store
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $image = $store->image;
    if ($request->hasFile('image')) {
        Storage::disk('public')->delete($store->image);
        $image = $request->file('image')->store('stores', 'public');
        
    }
        $request->validate([
            'name' => ['required','string','max:255'],
            'image' => ['nullable','image'], // ,'max:2048'
            'delivery_price' => ['required','numeric','min:0'],
            'type' => ['required','string','in:store,restaurant,cafe'],
            'popular' => ['nullable','boolean'],
        ]);

        $image = $store->image;
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($store->image);
            $image = $request->file('image')->store('stores', 'public');
        }

        $popular = $request->boolean('popular');

        $store->update([
            'name' => $request->name,
            'image' => $image,
            'delivery_price' => $request->delivery_price,
            'type' => $request->type,
            'popular' => $popular,
        ]);

        return redirect()->route('stores.index')
            ->with('success', 'تم تحديث المتجر بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        Storage::disk('public')->delete($store->image);

        $store->delete();

        return redirect()->route('stores.index')
            ->with('success', 'تم حذف المتجر بنجاح.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add_category(Request $request, Store $store)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        Category::create([
            'name' => $request->name,
            'store_id' => $store->id,
        ]);

        return redirect()->back()
            ->with('success', 'تم إنشاء الفئة بنجاح.');
    }
}
