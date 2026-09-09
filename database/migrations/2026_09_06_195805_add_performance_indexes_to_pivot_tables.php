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
        Schema::table('dialogs', function (Blueprint $table) {
            $table->index('sag_id', 'dialogs_sag_id_index');
        });

        Schema::table('dialog_messages', function (Blueprint $table) {
            $table->index('dialog_id', 'dialog_messages_dialog_id_index');
            $table->index('sender_id', 'dialog_messages_sender_id_index');
            $table->index('read_at', 'dialog_messages_read_at_index');
        });

        Schema::table('sagers', function (Blueprint $table) {
            $table->index('deleted_at', 'sagers_deleted_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dialogs', function (Blueprint $table) {
            $table->dropIndex('dialogs_sag_id_index');
        });

        Schema::table('dialog_messages', function (Blueprint $table) {
            $table->dropIndex('dialog_messages_dialog_id_index');
            $table->dropIndex('dialog_messages_sender_id_index');
            $table->dropIndex('dialog_messages_read_at_index');
        });

        Schema::table('sagers', function (Blueprint $table) {
            $table->dropIndex('sagers_deleted_at_index');
        });
    }
};