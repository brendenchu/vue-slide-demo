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
        // Add index on users.email for faster lookups (login, password reset, etc.)
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
        });

        // Add index on teams.key for faster team lookups by key
        Schema::table('teams', function (Blueprint $table) {
            $table->index('key');
        });

        // Add index on projects.status for filtering projects by status
        Schema::table('projects', function (Blueprint $table) {
            $table->index('status');
        });

        // Add composite index on tokens for faster user token lookups
        Schema::table('tokens', function (Blueprint $table) {
            $table->index(['project_id', 'user_id'], 'tokens_project_user_index');
            $table->index('expires_at'); // Also index expires_at for token expiration queries
        });

        // Add index on profile.public_id for faster slug-based user lookups
        Schema::table('profiles', function (Blueprint $table) {
            $table->index('public_id');
        });

        // Add index on responses.project_id for faster response lookups by project
        Schema::table('responses', function (Blueprint $table) {
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['key']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('tokens', function (Blueprint $table) {
            $table->dropIndex('tokens_project_user_index');
            $table->dropIndex(['expires_at']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex(['public_id']);
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
        });
    }
};
