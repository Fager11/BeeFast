<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserOrderCount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewOrderNotification;
use App\Models\Coupon;

class CartController extends Controller
{

   public function index()
{
    $cart = Cart::firstOrCreate(
        ['user_id' => Auth::id()]
    );

    $items = $cart->items()->with('product.store')->get();

    $subtotal = $items->sum(function($item) {
        return $item->product->price * $item->quantity;
    });

    $stores = $items->pluck('product.store')->unique('id')->filter();

    $delivery_fee = $stores->sum(function($store) {
        return $store->delivery_price;
    });

    // خصم الولاء
    $userOrderCount = UserOrderCount::firstOrCreate(
        ['user_id' => Auth::id()]
    );

    $discount = 0.05 * $userOrderCount->order_count * $subtotal;
    $discount = min($discount, $subtotal * 0.2);

    // خصم الكوبون
    [$coupon, $couponDiscount] = $this->resolveCoupon($subtotal, $discount);

    $total = $subtotal + $delivery_fee - $discount - $couponDiscount;

    return view('cart.index', compact(
        'items', 'subtotal', 'delivery_fee', 'discount',
        'total', 'stores', 'coupon', 'couponDiscount'
    ));
}
/**
 * يتحقق من الكوبون المخزّن في الجلسة ويحسب قيمة خصمه
 */
private function resolveCoupon($subtotal, $loyaltyDiscount)
{
    $code = session('coupon_code');

    if (!$code) {
        return [null, 0];
    }

    $coupon = Coupon::where('code', $code)->first();

    if (!$coupon || $coupon->checkValid($subtotal) !== true) {
        session()->forget('coupon_code');
        return [null, 0];
    }

    return [$coupon, $coupon->calculateDiscount($subtotal - $loyaltyDiscount)];
}

public function applyCoupon(Request $request)
{
    $request->validate(['code' => 'required|string|max:50']);

    $cart  = Cart::firstOrCreate(['user_id' => Auth::id()]);
    $items = $cart->items()->with('product')->get();

    if ($items->isEmpty()) {
        return back()->with('error', 'السلة فارغة.');
    }

    $subtotal = $items->sum(function($item) {
        return $item->product->price * $item->quantity;
    });

    $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

    if (!$coupon) {
        return back()->with('error', 'كود الخصم غير صحيح.');
    }

    $check = $coupon->checkValid($subtotal);

    if ($check !== true) {
        return back()->with('error', $check);
    }

    session(['coupon_code' => $coupon->code]);

    return back()->with('success', 'تم تطبيق كود الخصم ' . $coupon->code . ' 🎟️');
}

public function removeCoupon()
{
    session()->forget('coupon_code');

    return back()->with('success', 'تم إلغاء كود الخصم.');
}

    public function add(Request $request, $productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً لإضافة منتجات إلى السلة.');
        }

        $product = Product::findOrFail($productId);
        $requestedQty = max(1, (int)$request->input('quantity', 1));

        if ($product->quantity < $requestedQty) {
            return redirect()->back()->with('error', 'الكمية المطلوبة غير متوفرة في المخزون.');
        }

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            if ($product->quantity < $item->quantity + $requestedQty) {
                return redirect()->back()->with('error', 'لا توجد كمية كافية لزيادة عدد الوحدات.');
            }
            $item->quantity += $requestedQty;
            $item->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $requestedQty,
            ]);
        }

        $product->quantity -= $requestedQty;
        $product->save();

        $cart_count = $cart->items()->sum('quantity');
        session()->put('cart_count', $cart_count);

        return redirect()->back()->with('success', 'تمت إضافة المنتج إلى السلة بنجاح ✅');
    }

    public function update(Request $request, $itemId)
    {
        $item = CartItem::whereHas('cart', function($q){
            $q->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $newQty = max(1, (int)$request->input('quantity', 1));
        $product = $item->product;

        $difference = $newQty - $item->quantity;

        if ($difference > 0) {
            if ($product->quantity < $difference) {
                return redirect()->back()->with('error', 'لا توجد كمية كافية في المخزون لزيادة الكمية.');
            }
            $product->quantity -= $difference;
        } else {
            $product->quantity += abs($difference);
        }

        $product->save();
        $item->quantity = $newQty;
        $item->save();

        // Update cart count in session
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart_count = $cart->items()->sum('quantity');
            session()->put('cart_count', $cart_count);
        }

        return redirect()->back()->with('success', 'تم تحديث السلة بنجاح.');
    }

    public function remove($itemId)
    {
        $item = CartItem::whereHas('cart', function($q){
            $q->where('user_id', Auth::id());
        })->findOrFail($itemId);

        $product = $item->product;

        // Return quantity to stock
        $product->quantity += $item->quantity;
        $product->save();

        $item->delete();

        // Update cart count in session
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart_count = $cart->items()->sum('quantity');
            session()->put('cart_count', $cart_count);
        }

        return redirect()->back()->with('success', 'تم إزالة المنتج من السلة.');
    }

    public function checkout(Request $request)
{
    $request->validate([
        'address' => 'required|string|max:255',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ], [
        'address.required' => 'يجب إدخال عنوان التوصيل أولاً.',
        'latitude.required' => 'يجب تحديد موقعك على الخريطة.',
        'longitude.required' => 'يجب تحديد موقعك على الخريطة.',
    ]);

    $cart = Cart::where('user_id', Auth::id())->firstOrFail();
    $items = $cart->items()->with('product.store')->get();

    if ($items->isEmpty()) {
        return redirect()->back()->with('error', 'السلة فارغة.');
    }

    // ===== الحسابات (قبل المعاملة) =====
    $subtotal = $items->sum(function($item) {
        return $item->product->price * $item->quantity;
    });

    $uniqueStores = $items->pluck('product.store')->unique('id')->filter();
    $delivery_fee = $uniqueStores->sum(function($store) {
        return $store->delivery_price;
    });

    $userOrderCount = UserOrderCount::firstOrCreate(['user_id' => Auth::id()]);
    $discount = 0.05 * $userOrderCount->order_count * $subtotal;
    $discount = min($discount, $subtotal * 0.2);

    // إعادة التحقق من الكوبون في الخادم
    $hadCoupon = session()->has('coupon_code');
    [$coupon, $couponDiscount] = $this->resolveCoupon($subtotal, $discount);

    if ($hadCoupon && !$coupon) {
        return redirect()->route('cart.index')
            ->with('error', 'كود الخصم لم يعد صالحاً وتم إلغاؤه. راجعي الإجمالي ثم أعيدي المحاولة.');
    }

    $total = $subtotal + $delivery_fee - $discount - $couponDiscount;

    // ===== الحفظ =====
    DB::transaction(function() use ($items, $cart, $request, $subtotal, $delivery_fee,
                                     $discount, $coupon, $couponDiscount, $total, $userOrderCount) {

        $order = Order::create([
            'user_id'         => Auth::id(),
            'subtotal'        => $subtotal,
            'delivery_price'  => $delivery_fee,
            'discount'        => $discount,
            'coupon_code'     => $coupon?->code,
            'coupon_discount' => $couponDiscount,
            'total'           => $total,
            'address'         => $request->address,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price,
            ]);
        }

        $userOrderCount->order_count += 1;
        $userOrderCount->save();

        if ($coupon) {
            $coupon->increment('used_count');
        }

        $cart->items()->delete();

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewOrderNotification($order));
        }
    });

    session()->forget(['cart_count', 'coupon_code']);

    return redirect()->route('cart.index')->with('success', 'تم تأكيد وإرسال طلبك بنجاح. يمكنك استعراض طلبك ومراقبة حالته.');
}
}
