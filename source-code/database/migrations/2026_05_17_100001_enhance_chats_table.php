<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->addColumn('message_type', fn (Blueprint $table) => $table->tinyInteger('message_type')->default(1)->after('message'));
        $this->addColumn('file_path', fn (Blueprint $table) => $table->string('file_path')->nullable()->after('message_type'));
        $this->addColumn('file_name', fn (Blueprint $table) => $table->string('file_name')->nullable()->after('file_path'));
        $this->addColumn('file_size', fn (Blueprint $table) => $table->unsignedInteger('file_size')->nullable()->after('file_name'));
        $this->addColumn('file_mime', fn (Blueprint $table) => $table->string('file_mime')->nullable()->after('file_size'));
        $this->addColumn('reply_to_id', fn (Blueprint $table) => $table->unsignedBigInteger('reply_to_id')->nullable()->after('file_mime'));
        $this->addColumn('is_delivered', fn (Blueprint $table) => $table->tinyInteger('is_delivered')->default(0)->after('is_seen'));
        $this->addColumn('deleted_for_sender', fn (Blueprint $table) => $table->boolean('deleted_for_sender')->default(false)->after('is_delivered'));
        $this->addColumn('deleted_for_receiver', fn (Blueprint $table) => $table->boolean('deleted_for_receiver')->default(false)->after('deleted_for_sender'));
        $this->addColumn('reactions', fn (Blueprint $table) => $table->json('reactions')->nullable()->after('deleted_for_receiver'));
        $this->addColumn('edited_at', fn (Blueprint $table) => $table->timestamp('edited_at')->nullable()->after('reactions'));

        // Typing indicators — ephemeral, rows expire after 4s of inactivity
        if (!Schema::hasTable('chat_typing')) {
            Schema::create('chat_typing', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('receiver_id')->index();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->unique(['user_id', 'receiver_id']);
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'edited_at',
            'reactions',
            'deleted_for_receiver',
            'deleted_for_sender',
            'is_delivered',
            'reply_to_id',
            'file_mime',
            'file_size',
            'file_name',
            'file_path',
            'message_type',
        ] as $column) {
            if (Schema::hasColumn('chats', $column)) {
                Schema::table('chats', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
        Schema::dropIfExists('chat_typing');
    }

    private function addColumn(string $column, callable $definition): void
    {
        if (!Schema::hasColumn('chats', $column)) {
            Schema::table('chats', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }
};
