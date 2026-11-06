<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Store;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, $storeId)
    {
        // تحقق أن المستخدم لديه طلب مكتمل من هذا المتجر
        $hasDeliveredOrder = Order::where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->whereHas('items.product', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->exists();

        if (!$hasDeliveredOrder) {
            return back()->with('error', 'يمكنك التقييم فقط بعد استلام طلب من هذا المتجر.');
        }

        // تحقق أنه لم يقيّم المتجر من قبل
        $alreadyReviewed = Review::where('user_id', Auth::id())
            ->where('store_id', $storeId)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'لقد قمت بتقييم هذا المتجر مسبقاً.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        Review::create([
            'user_id'  => Auth::id(),
            'store_id' => $storeId,
            'rating'   => $request->rating,
            'comment'  => $request->comment,
        ]);

        return back()->with('success', 'تم إضافة تقييمك بنجاح! ⭐');
    }

    public function destroy($storeId)
    {
        $review = Review::where('user_id', Auth::id())
                        ->where('store_id', $storeId)
                        ->firstOrFail();
        $review->delete();

        return back()->with('success', 'تم حذف تقييمك.');
    }
}