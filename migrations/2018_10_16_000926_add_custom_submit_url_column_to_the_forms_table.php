<?php
/*--------------------
https://github.com/abd-abdullah/laravel-formbuilder
Licensed under the GNU General Public License v3.0
Author: Md. Abdullah
Last Updated: 17/11/2021
----------------------*/
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomSubmitUrlColumnToTheFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->string('custom_submit_url')->nullable()->after('form_builder_json');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('custom_submit_url');
        });
    }
}
