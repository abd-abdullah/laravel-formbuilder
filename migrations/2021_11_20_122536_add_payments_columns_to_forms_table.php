<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentsColumnsToFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->tinyInteger('multiple_submit')->after('custom_submit_url')->default(0);
            $table->tinyInteger('payment_enable')->after('multiple_submit')->default(0);
            $table->string('paymentable_class_path')->after('payment_enable')->nullable();
            $table->text('payment_details')->after('paymentable_class_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('multiple_submit');
            $table->dropColumn('payment_enable');
            $table->dropColumn('paymentable_class_path');
            $table->dropColumn('payment_details');
        });
    }
}
