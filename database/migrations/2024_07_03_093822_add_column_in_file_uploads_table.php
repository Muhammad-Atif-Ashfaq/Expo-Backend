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
        Schema::table('file_uploads', function (Blueprint $table) {
            if(!Schema::hasColumn('file_uploads','contest_id')){
            $table->foreignId('contest_id')->constrained()->on('contests')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_uploads', function (Blueprint $table) {
            $table->dropForeign(['contest_id']);
            $table->dropColumn('contest_id');
        });
    }
};
