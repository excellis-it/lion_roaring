<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignupRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signup_rules', function (Blueprint $table) {
            $table->id();
            $table->string('field_name'); // e.g., 'email', 'phone', 'first_name'
            $table->string('rule_type'); // e.g., 'regex', 'min_length', 'max_length', 'required', 'domain', 'numeric'
            $table->text('rule_value')->nullable(); // The value for the rule (e.g., regex pattern, min value, domain name)
            $table->text('error_message')->nullable(); // Custom error message
            $table->text('description')->nullable(); // Description of what this rule does
            $table->boolean('is_active')->default(true);
            $table->boolean('is_critical')->default(false); // If true, failing this rule makes user inactive
            $table->integer('priority')->default(0); // Order of validation
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
        Schema::dropIfExists('signup_rules');
    }
}
