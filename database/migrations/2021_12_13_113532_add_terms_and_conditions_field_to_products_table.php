<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermsAndConditionsFieldToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('terms_and_conditions')->nullable()->after('template_id');
            $table->decimal('price_factor', 8, 3)->nullable()->after('template_id');
            $table->boolean('is_housing')->nullable()->after('template_id');
            $table->boolean('is_tour')->nullable()->after('template_id');
            $table->text('housing_pricing')->nullable()->after('template_id');
            $table->text('tour_information')->nullable()->after('template_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('terms_and_conditions');
            $table->dropColumn('price_factor');
            $table->dropColumn('is_housing');
            $table->dropColumn('housing_pricing');
            $table->dropColumn('is_tour');
            $table->dropColumn('tour_information');
        });
    }
}
