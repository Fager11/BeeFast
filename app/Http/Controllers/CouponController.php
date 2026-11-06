<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // السماح للأدمن فقط
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->role === 'admin', 403);
            return $next($request);
        });
    }

    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        // توحيد الكود (أحرف كبيرة) قبل التحقق من التكرار
        $request->merge(['code' => strtoupper(trim($request->code))]);

        $data = $request->validate([
            'code'       => 'required|string|max:50|unique:coupons,code',
            'type'       => 'required|in:percent,fixed',
            'value'      => 'required|numeric|min:1',
            'min_order'  => 'nullable|numeric|min:0',
            'max_uses'   => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after_or_equal:today',
        ]);

        if ($data['type'] === 'percent' && $data['value'] > 100) {
            return back()->withInput()
                ->withErrors(['value' => 'النسبة لا يمكن أن تتجاوز 100%.']);
        }

        $data['min_order'] = $data['min_order'] ?? 0;

        Coupon::create($data);

        return back()->with('success', 'تم إنشاء كود الخصم بنجاح 🎟️');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', $coupon->is_active ? 'تم تفعيل الكود.' : 'تم تعطيل الكود.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'تم حذف الكود.');
    }
}