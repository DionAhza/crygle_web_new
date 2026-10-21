<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tidak ada unique index (assignment_id, student_id): setiap revisi
        // adalah baris baru agar riwayat pengiriman tetap tersimpan.
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('attachment_url')->nullable();
            $table->text('student_note')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('mentor_feedback')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['assignment_id', 'student_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
