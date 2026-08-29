<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_support_agent')->default(false)->index();
        });

        Schema::create('chat_conversations', function (Blueprint $table): void {
            $table->id();
            $table->char('visitor_token_hash', 64)->unique();
            $table->string('visitor_name', 120);
            $table->string('visitor_email', 190);
            $table->string('status')->default('open')->index();
            $table->timestamp('last_message_at')->index();
            $table->timestamp('notification_dispatched_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sender_type', 20)->index();
            $table->text('body');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();

            $table->index(['chat_conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_support_agent');
        });
    }
};
