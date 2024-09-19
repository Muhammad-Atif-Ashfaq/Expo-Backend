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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->on('users')->onDelete('cascade');
            $table->foreignId('contest_id')->constrained()->on('contests')->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->string('label');
            $table->boolean('required')->default(true);
            $table->boolean('is_important')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
