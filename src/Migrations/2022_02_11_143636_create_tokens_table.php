<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('tokenable_id')->nullable();
            $table->string('tokenable_type')->nullable();
            $table->string('guard')->nullable();
            $table->longText('token')->nullable();
            $table->longText('payload')->nullable();
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('last_access_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authentication_tokens');
    }
};
