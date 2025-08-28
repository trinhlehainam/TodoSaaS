<?php

namespace Tests\Unit;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test team member creation with all fields.
     */
    public function test_can_create_team_member_with_all_fields(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $teamMember = TeamMember::query()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => TeamRole::Admin->value,
        ]);

        $this->assertInstanceOf(TeamMember::class, $teamMember);
        $this->assertEquals($team->id, $teamMember->team_id);
        $this->assertEquals($user->id, $teamMember->user_id);
        $this->assertEquals(TeamRole::Admin, $teamMember->role);
    }

    /**
     * Test team member relationships.
     */
    public function test_team_member_belongs_to_team_and_user(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $teamMember = TeamMember::factory()->create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Team::class, $teamMember->team);
        $this->assertEquals($team->id, $teamMember->team->id);

        $this->assertInstanceOf(User::class, $teamMember->user);
        $this->assertEquals($user->id, $teamMember->user->id);
    }

    /**
     * Test isOwner method.
     */
    public function test_is_owner_method(): void
    {
        $ownerMember = TeamMember::factory()->asOwner()->create();
        $adminMember = TeamMember::factory()->asAdmin()->create();
        $regularMember = TeamMember::factory()->asMember()->create();
        $viewerMember = TeamMember::factory()->asViewer()->create();

        $this->assertTrue($ownerMember->isOwner());
        $this->assertFalse($adminMember->isOwner());
        $this->assertFalse($regularMember->isOwner());
        $this->assertFalse($viewerMember->isOwner());
    }

    /**
     * Test isAdmin method.
     */
    public function test_is_admin_method(): void
    {
        $ownerMember = TeamMember::factory()->asOwner()->create();
        $adminMember = TeamMember::factory()->asAdmin()->create();
        $regularMember = TeamMember::factory()->asMember()->create();
        $viewerMember = TeamMember::factory()->asViewer()->create();

        $this->assertTrue($ownerMember->isAdmin());
        $this->assertTrue($adminMember->isAdmin());
        $this->assertFalse($regularMember->isAdmin());
        $this->assertFalse($viewerMember->isAdmin());
    }

    /**
     * Test canManageTeam method.
     */
    public function test_can_manage_team_method(): void
    {
        $ownerMember = TeamMember::factory()->asOwner()->create();
        $adminMember = TeamMember::factory()->asAdmin()->create();
        $regularMember = TeamMember::factory()->asMember()->create();
        $viewerMember = TeamMember::factory()->asViewer()->create();

        $this->assertTrue($ownerMember->canManageTeam());
        $this->assertTrue($adminMember->canManageTeam());
        $this->assertFalse($regularMember->canManageTeam());
        $this->assertFalse($viewerMember->canManageTeam());
    }

    /**
     * Test hasPermission method for owner.
     */
    public function test_owner_has_all_permissions(): void
    {
        $ownerMember = TeamMember::factory()->asOwner()->create();

        $this->assertTrue($ownerMember->hasPermission('any-permission'));
        $this->assertTrue($ownerMember->hasPermission('manage-team'));
        $this->assertTrue($ownerMember->hasPermission('delete-everything'));
    }

    /**
     * Test hasPermission method for admin.
     */
    public function test_admin_has_specific_permissions(): void
    {
        $adminMember = TeamMember::factory()->asAdmin()->create();

        $this->assertTrue($adminMember->hasPermission('manage-team'));
        $this->assertTrue($adminMember->hasPermission('manage-members'));
        $this->assertTrue($adminMember->hasPermission('view-team'));
        $this->assertFalse($adminMember->hasPermission('delete-team'));
        $this->assertFalse($adminMember->hasPermission('unknown-permission'));
    }

    /**
     * Test hasPermission method for member.
     */
    public function test_member_has_limited_permissions(): void
    {
        $member = TeamMember::factory()->asMember()->create();

        $this->assertTrue($member->hasPermission('view-team'));
        $this->assertTrue($member->hasPermission('contribute'));
        $this->assertFalse($member->hasPermission('manage-team'));
        $this->assertFalse($member->hasPermission('manage-members'));
    }

    /**
     * Test hasPermission method for viewer.
     */
    public function test_viewer_has_minimal_permissions(): void
    {
        $viewerMember = TeamMember::factory()->asViewer()->create();

        $this->assertTrue($viewerMember->hasPermission('view-team'));
        $this->assertFalse($viewerMember->hasPermission('contribute'));
        $this->assertFalse($viewerMember->hasPermission('manage-team'));
        $this->assertFalse($viewerMember->hasPermission('manage-members'));
    }

    /**
     * Test role casting to TeamRole enum.
     */
    public function test_role_is_cast_to_team_role_enum(): void
    {
        $teamMember = TeamMember::factory()->asAdmin()->create();

        $this->assertInstanceOf(TeamRole::class, $teamMember->role);
        $this->assertEquals(TeamRole::Admin, $teamMember->role);
    }

    /**
     * Test different role factory states.
     */
    public function test_factory_role_states(): void
    {
        $owner = TeamMember::factory()->asOwner()->create();
        $admin = TeamMember::factory()->asAdmin()->create();
        $member = TeamMember::factory()->asMember()->create();
        $viewer = TeamMember::factory()->asViewer()->create();

        $this->assertEquals(TeamRole::Owner, $owner->role);
        $this->assertEquals(TeamRole::Admin, $admin->role);
        $this->assertEquals(TeamRole::Member, $member->role);
        $this->assertEquals(TeamRole::Viewer, $viewer->role);
    }

    /**
     * Test TeamMember is a pivot model with incrementing ID.
     */
    public function test_team_member_is_pivot_with_incrementing_id(): void
    {
        $teamMember = TeamMember::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\Pivot::class, $teamMember);
        $this->assertTrue($teamMember->incrementing);
        $this->assertNotNull($teamMember->id);
    }

    /**
     * Test multiple users can be members of the same team.
     */
    public function test_multiple_users_can_be_members_of_same_team(): void
    {
        $team = Team::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            TeamMember::factory()->create([
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]);
        }

        $this->assertCount(3, TeamMember::query()->where('team_id', $team->id)->get());
    }

    /**
     * Test a user can be member of multiple teams.
     */
    public function test_user_can_be_member_of_multiple_teams(): void
    {
        $user = User::factory()->create();
        $teams = Team::factory()->count(3)->create();

        foreach ($teams as $team) {
            TeamMember::factory()->create([
                'team_id' => $team->id,
                'user_id' => $user->id,
            ]);
        }

        $this->assertCount(3, TeamMember::query()->where('user_id', $user->id)->get());
    }
}