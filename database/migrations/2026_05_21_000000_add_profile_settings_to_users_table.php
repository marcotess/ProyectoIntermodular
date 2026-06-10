<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('receive_notification_emails')->default(true)->after('password');
            $table->string('theme_preference', 16)->default('light')->after('receive_notification_emails');
            $table->boolean('compact_tables')->default(false)->after('theme_preference');
            $table->boolean('reduce_motion')->default(false)->after('compact_tables');
            $table->boolean('show_quick_notifications')->default(true)->after('reduce_motion');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'receive_notification_emails',
                'theme_preference',
                'compact_tables',
                'reduce_motion',
                'show_quick_notifications',
            ]);
        });
    }
};
