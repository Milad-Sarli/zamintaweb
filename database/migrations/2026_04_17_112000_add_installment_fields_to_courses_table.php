<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('prepayment_amount')->default(0)->after('price');
            $table->unsignedSmallInteger('installment_months')->default(0)->after('prepayment_amount');
            $table->unsignedBigInteger('installment_monthly_amount')->default(0)->after('installment_months');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'prepayment_amount',
                'installment_months',
                'installment_monthly_amount',
            ]);
        });
    }
};
