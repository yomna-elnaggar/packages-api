<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_packages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_id')->index();
            $table->foreignUuid('package_id')->index()->constrained('packages')->onDelete('cascade');
            $table->date('subscribed_at')->nullable()->index();
            $table->date('expires_at')->nullable()->index();
            $table->integer('num_of_cars');
            $table->decimal('price', 10, 2);
            $table->decimal('price_with_tax', 10, 2)->nullable();
            $table->string('payment_status')->index(); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_packages');
    }
};
