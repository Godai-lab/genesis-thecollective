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
        Schema::table('subscriptions', function (Blueprint $table) {
            
           $table->dropColumn(['type','stripe_id', 'stripe_status', 'stripe_price', 'quantity', 'trial_ends_at']);
    
            // Agregar nuevas columnas necesarias
            $table->foreignId('plan_id')->after('user_id')->constrained()->onDelete('cascade');
            $table->date('expires_at')->after('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Revertir cambios si es necesario
            // $table->string('type');
            // $table->string('stripe_id')->unique();
            // $table->string('stripe_status');
            // $table->string('stripe_price')->nullable();
            // $table->integer('quantity')->nullable();
            // $table->timestamp('trial_ends_at')->nullable();

          // $table->dropColumn(['plan_id', 'expires_at']);
          
        });
    }
};
