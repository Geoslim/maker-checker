<?php

use App\Enum\Status;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable();
            $table->string('type');
            $table->string('status')->default(Status::PENDING->value);
            $table->json('data')->nullable();
            $table->foreignIdFor(User::class, 'maker_id')
                ->comment('The administrator making the request.');
            $table->foreignIdFor(User::class, 'checker_id')->nullable()
                ->comment('The administrator approving the request.');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
}
