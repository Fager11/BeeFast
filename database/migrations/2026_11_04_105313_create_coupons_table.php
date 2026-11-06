<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                    // الكود مثل BEE10
            $table->enum('type', ['percent', 'fixed']);          // نسبة أو مبلغ ثابت
            $table->decimal('value', 10, 2);                     // قيمة الخصم
            $table->decimal('min_order', 10, 2)->default(0);     // الحد الأدنى للطلب
            $table->unsignedInteger('max_uses')->nullable();     // أقصى عدد استخدامات (فارغ = بلا حد)
            $table->unsignedInteger('used_count')->default(0);   // كم مرة استُخدم
            $table->date('expires_at')->nullable();              // تاريخ الانتهاء
            $table->boolean('is_active')->default(true);         // مفعّل أم لا
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};