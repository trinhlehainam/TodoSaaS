<?php

namespace Database\Seeders;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        // Create some sample users
        $users = User::factory(10)->create();

        // Create a personal team for the first user
        $firstUser = $users->first();
        $personalTeam = Team::factory()
            ->personal()
            ->create(['owner_id' => $firstUser->id]);

        // Set as current team
        $firstUser->update(['current_team_id' => $personalTeam->id]);

        // Create a few organizational teams
        $orgTeam1 = Team::factory()->create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'description' => 'A global technology leader',
            'owner_id' => $firstUser->id,
        ]);

        $orgTeam2 = Team::factory()->create([
            'name' => 'StartupHub',
            'slug' => 'startuphub',
            'description' => 'Innovation accelerator',
            'owner_id' => $users->skip(1)->first()->id,
        ]);

        $orgTeam3 = Team::factory()->create([
            'name' => 'DevOps Masters',
            'slug' => 'devops-masters',
            'description' => 'Infrastructure and automation experts',
            'owner_id' => $users->skip(2)->first()->id,
        ]);

        // Add team members with different roles
        $orgTeam1->addMember($users->skip(1)->first(), TeamRole::Admin);
        $orgTeam1->addMember($users->skip(2)->first(), TeamRole::Member);
        $orgTeam1->addMember($users->skip(3)->first(), TeamRole::Member);
        $orgTeam1->addMember($users->skip(4)->first(), TeamRole::Viewer);

        $orgTeam2->addMember($users->skip(5)->first(), TeamRole::Admin);
        $orgTeam2->addMember($users->skip(6)->first(), TeamRole::Member);

        $orgTeam3->addMember($users->skip(7)->first(), TeamRole::Member);
        $orgTeam3->addMember($users->skip(8)->first(), TeamRole::Viewer);

        // Create some pending invitations
        TeamInvitation::factory()->create([
            'team_id' => $orgTeam1->id,
            'email' => 'newmember@example.com',
            'role' => TeamRole::Member->value,
            'invited_by' => $firstUser->id,
        ]);

        TeamInvitation::factory()->create([
            'team_id' => $orgTeam2->id,
            'email' => 'admin@example.com',
            'role' => TeamRole::Admin->value,
            'invited_by' => $orgTeam2->owner_id,
        ]);

        // Create an expired invitation
        TeamInvitation::factory()->expired()->create([
            'team_id' => $orgTeam3->id,
            'email' => 'expired@example.com',
            'role' => TeamRole::Member->value,
            'invited_by' => $orgTeam3->owner_id,
        ]);
    }
}
