<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('profile_pictures', function (Blueprint $table) {
		    $table->increments( 'id' );
		    $table->integer( 'user_id' )->unsigned();
		    $table->string( 'image' );
		    $table->boolean( 'is_active' )->default(true)->comment('FALSE: Profile Picture Not in user | TRUE: Profile Picture in use');
		    $table->boolean( 'is_deleted' )->default(false)->comment('FALSE: Profile Picture Not Deleted | TRUE: Profile Picture Deleted');
		    $table->timestampTz('created_at')->nullable();
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
	    Schema::dropIfExists('profile_pictures');
    }
}
