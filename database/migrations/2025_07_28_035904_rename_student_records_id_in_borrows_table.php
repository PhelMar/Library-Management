<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->renameColumn('student_records_id', 'student_record_id');
        });
    }

    public function down(): void
    {
        Schema::table('borrows', function (Blueprint $table) {
            $table->renameColumn('student_record_id', 'student_records_id');
        });
    }
};
