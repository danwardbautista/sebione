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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('first_name');
            $table->string('last_name');
            $table->bigInteger('FK_employees_companies')->unsigned()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
        });

        Schema::table('employees', function(Blueprint $table) {
            // $table->foreign('FK_employees_companies')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('FK_employees_companies')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
