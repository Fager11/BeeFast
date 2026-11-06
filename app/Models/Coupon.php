<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'max_uses', 'used_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    /**
     * يرجع true إذا الكود صالح، أو رسالة الخطأ إذا لم يكن صالحاً
     */
    public function checkValid($subtotal)
    {
        if (!$this->is_active) {
            return 'هذا الكود غير مفعّل.';
        }

        if ($this->expires_at && $this->expires_at->lt(today())) {
            return 'انتهت صلاحية هذا الكود.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'تم استنفاد الحد الأقصى لاستخدام هذا الكود.';
        }

        if ($subtotal < $this->min_order) {
            return 'الحد الأدنى للطلب لاستخدام هذا الكود هو ' . number_format($this->min_order, 0) . ' ل.س';
        }

        return true;
    }

    /**
     * حساب قيمة الخصم (لا تتجاوز المبلغ المُمرَّر)
     */
    public function calculateDiscount($amount)
    {
        $discount = $this->type === 'percent'
            ? $amount * $this->value / 100
            : $this->value;

        return round(min($discount, $amount), 2);
    }
}