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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id'); // File upload karne wale user ki ID

            $table->string('file_name');          // Original file ka naam (e.g., 'report.pdf')
            $table->string('file_path');          // Storage path (e.g., 'chat_files/abc123.pdf')
            $table->string('file_type');          // MIME type (e.g., 'application/pdf', 'image/jpeg')
            $table->string('file_category');      // Custom category (e.g., 'document', 'image', 'video')
            $table->unsignedBigInteger('file_size'); // File size in Bytes 
            $table->string('thumbnail_path')->nullable(); // Video/Image ke liye optional thumbnail
            
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
