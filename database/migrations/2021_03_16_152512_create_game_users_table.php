<?php

use App\Http\Enums\TurnVariantEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_users', function (Blueprint $table) {
            $table->id();
            $table->string('nickname');
            $table->enum('turn', TurnVariantEnum::getConstants())->nullable();
            $table->integer('points')->default(0);
            $table->unique(['nickname', 'game_id']);
            $table->foreignId('game_id')->constrained('games')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_users');
    }
}
