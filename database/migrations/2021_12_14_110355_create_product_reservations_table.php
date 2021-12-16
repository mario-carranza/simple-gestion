<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('hash');
            $table->foreignId('product_id')->constrained('products');
            $table->string('name');
            $table->string('email');
            $table->string('cellphone')->nullable();
            $table->string('type');
            $table->decimal('price', 16, 2 )->nullable();
            $table->integer('adults_number');
            $table->integer('childrens_number');
            $table->dateTime('check_in_date')->nullable();
            $table->dateTime('check_out_date')->nullable();
            $table->string('reservation_status');
            $table->text('customer_comment')->nullable();
            $table->text('seller_comment')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('order_id')->nullable()->constrained('orders');

            $table->longText('json_value')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_reservations');
    }
}
