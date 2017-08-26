<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned();
            $table->string('firstname');
            $table->string('lastname')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
	        $table->string('password');
	        $table->string('zipcode', 5)->nullable();
	        $table->date('dob');
	        $table->enum('gender', ['male', 'female']);
	        $table->string('contact', 15)->unique();
	        $table->boolean('is_active')->default(true)->comment('FALSE: User Inactive | TRUE: User Active');
	        $table->boolean('is_verified')->default(false)->comment('FALSE: User Unverified | TRUE: User Verified');
	        $table->boolean('is_deleted')->default(false)->comment('FALSE: User Available | TRUE: User Deleted');
	        $table->boolean('is_social')->default(false)->comment('FALSE: Normal Login | TRUE: Social Login');
	        $table->enum('social_type', ['google', 'facebook'])->nullable();
	        $table->string('social_token')->nullable();
	        $table->string('device_token')->nullable();
	        $table->string('activation_token', 128)->nullable();
	        $table->string('forgot_token', 128)->nullable();
	        $table->rememberToken();
	        $table->timestampTz('last_login')->nullable();
	        $table->timestampsTz();
	        $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
