<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE team_members DROP CONSTRAINT team_members_role_check");
        DB::statement("ALTER TABLE team_members ADD CONSTRAINT team_members_role_check CHECK (role::text = ANY(ARRAY['owner', 'admin', 'member', 'viewer']))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE team_members DROP CONSTRAINT team_members_role_check");
        DB::statement("ALTER TABLE team_members ADD CONSTRAINT team_members_role_check CHECK (role::text = ANY(ARRAY['admin', 'member', 'viewer']))");
    }
};