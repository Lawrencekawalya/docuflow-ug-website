<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('business_name');
            $table->string('work_email');
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->json('document_types');
            $table->string('monthly_document_volume')->nullable();
            $table->text('current_process')->nullable();
            $table->text('biggest_challenge')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new')->index();
            $table->timestamp('notification_dispatched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
