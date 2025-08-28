<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Enums\TeamRole;
use App\Models\Task;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test team creation with all fields.
     */
    public function test_can_create_team_with_all_fields(): void
    {
        $owner = User::factory()->create();

        $team = Team::create([
            'name' => 'Test Team',
            'slug' => 'test-team',
            'description' => 'Test Description',
            'owner_id' => $owner->id,
            'personal_team' => false,
            'settings' => [
                'allow_invitations' => true,
                'visibility' => 'public',
            ],
        ]);

        $this->assertInstanceOf(Team::class, $team);
        $this->assertEquals('Test Team', $team->name);
        $this->assertEquals('test-team', $team->slug);
        $this->assertEquals('Test Description', $team->description);
        $this->assertFalse($team->personal_team);
        $this->assertTrue($team->settings['allow_invitations']);
        $this->assertEquals('public', $team->settings['visibility']);
    }

    /**
     * Test team owner relationship.
     */
    public function test_team_belongs_to_owner(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['owner_id' => $owner->id]);

        $this->assertInstanceOf(User::class, $team->owner);
        $this->assertEquals($owner->id, $team->owner->id);
    }

    /**
     * Test team members relationship.
     */
    public function test_team_has_many_members(): void
    {
        $team = Team::factory()->create();
        $users = User::factory()->count(3)->create();

        foreach ($users as $user) {
            $team->addMember($user, TeamRole::Member);
        }

        $this->assertCount(3, $team->members);
        $this->assertTrue($team->members->contains($users->first()));
    }

    /**
     * Test team invitations relationship.
     */
    public function test_team_has_many_invitations(): void
    {
        $team = Team::factory()->create();
        TeamInvitation::factory()->count(3)->create(['team_id' => $team->id]);

        $this->assertCount(3, $team->invitations);
        $this->assertInstanceOf(TeamInvitation::class, $team->invitations->first());
    }

    /**
     * Test hasUser method.
     */
    public function test_has_user_method(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $outsider = User::factory()->create();
        
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->addMember($member);

        $this->assertTrue($team->hasUser($owner));
        $this->assertTrue($team->hasUser($member));
        $this->assertFalse($team->hasUser($outsider));
    }

    /**
     * Test userRole method.
     */
    public function test_user_role_method(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $outsider = User::factory()->create();
        
        $team = Team::factory()->create(['owner_id' => $owner->id]);
        $team->addMember($admin, TeamRole::Admin);
        $team->addMember($member, TeamRole::Member);

        $this->assertEquals(TeamRole::Owner, $team->userRole($owner));
        $this->assertEquals(TeamRole::Admin, $team->userRole($admin));
        $this->assertEquals(TeamRole::Member, $team->userRole($member));
        $this->assertNull($team->userRole($outsider));
    }

    /**
     * Test addMember method.
     */
    public function test_add_member_method(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $this->assertFalse($team->hasUser($user));

        $team->addMember($user, TeamRole::Admin);

        $this->assertTrue($team->fresh()->hasUser($user));
        $this->assertEquals(TeamRole::Admin, $team->fresh()->userRole($user));
    }

    /**
     * Test addMember with default role.
     */
    public function test_add_member_with_default_role(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();

        $team->addMember($user);

        $this->assertTrue($team->fresh()->hasUser($user));
        $this->assertEquals(TeamRole::Member, $team->fresh()->userRole($user));
    }

    /**
     * Test removeMember method.
     */
    public function test_remove_member_method(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $team->addMember($user);

        $this->assertTrue($team->fresh()->hasUser($user));

        $team->removeMember($user);

        $this->assertFalse($team->fresh()->hasUser($user));
    }

    /**
     * Test updateMemberRole method.
     */
    public function test_update_member_role_method(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $team->addMember($user, TeamRole::Member);

        $this->assertEquals(TeamRole::Member, $team->fresh()->userRole($user));

        $team->updateMemberRole($user, TeamRole::Admin);

        $this->assertEquals(TeamRole::Admin, $team->fresh()->userRole($user));
    }

    /**
     * Test personal team factory state.
     */
    public function test_personal_team_factory_state(): void
    {
        $owner = User::factory()->create(['name' => 'John Doe']);
        $team = Team::factory()->personal()->for($owner, 'owner')->create();

        $this->assertEquals("John Doe's Team", $team->name);
        $this->assertEquals('john-doe-team', $team->slug);
        $this->assertTrue($team->personal_team);
        $this->assertFalse($team->settings['allow_invitations']);
        $this->assertEquals('private', $team->settings['visibility']);
    }

    /**
     * Test withSettings factory method.
     */
    public function test_with_settings_factory_method(): void
    {
        $team = Team::factory()->withSettings([
            'custom_setting' => 'custom_value',
            'allow_invitations' => false,
        ])->create();

        $this->assertEquals('custom_value', $team->settings['custom_setting']);
        $this->assertFalse($team->settings['allow_invitations']);
    }

    /**
     * Test team has many tasks relationship.
     */
    public function test_team_has_many_tasks(): void
    {
        $team = Team::factory()->create();
        Task::factory()->count(3)->forTeam($team)->create();

        $this->assertCount(3, $team->tasks);
        $this->assertInstanceOf(Task::class, $team->tasks->first());
    }

    /**
     * Test pendingTasks relationship.
     */
    public function test_pending_tasks_relationship(): void
    {
        $team = Team::factory()->create();
        Task::factory()->pending()->forTeam($team)->create();
        Task::factory()->inProgress()->forTeam($team)->create();
        Task::factory()->completed()->forTeam($team)->create();

        $this->assertCount(1, $team->pendingTasks);
        $this->assertEquals(TaskStatus::Pending, $team->pendingTasks->first()->status);
    }

    /**
     * Test inProgressTasks relationship.
     */
    public function test_in_progress_tasks_relationship(): void
    {
        $team = Team::factory()->create();
        Task::factory()->pending()->forTeam($team)->create();
        Task::factory()->inProgress()->forTeam($team)->create();
        Task::factory()->completed()->forTeam($team)->create();

        $this->assertCount(1, $team->inProgressTasks);
        $this->assertEquals(TaskStatus::InProgress, $team->inProgressTasks->first()->status);
    }

    /**
     * Test completedTasks relationship.
     */
    public function test_completed_tasks_relationship(): void
    {
        $team = Team::factory()->create();
        Task::factory()->pending()->forTeam($team)->create();
        Task::factory()->inProgress()->forTeam($team)->create();
        Task::factory()->completed()->forTeam($team)->create();

        $this->assertCount(1, $team->completedTasks);
        $this->assertEquals(TaskStatus::Completed, $team->completedTasks->first()->status);
    }

    /**
     * Test overdueTasks relationship.
     */
    public function test_overdue_tasks_relationship(): void
    {
        $team = Team::factory()->create();
        
        Task::factory()->overdue()->forTeam($team)->create();
        
        Task::factory()->pending()->forTeam($team)->create([
            'due_date' => now()->addDays(7),
        ]);
        
        Task::factory()->completed()->forTeam($team)->create([
            'due_date' => now()->subDays(7),
        ]);

        $overdueTasks = $team->overdueTasks()->get();
        
        $this->assertCount(1, $overdueTasks);
        $this->assertNull($overdueTasks->first()->completed_at);
        $this->assertTrue($overdueTasks->first()->due_date->isPast());
    }

    /**
     * Test settings array casting.
     */
    public function test_settings_are_cast_to_array(): void
    {
        $team = Team::factory()->create([
            'settings' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ]);

        $this->assertIsArray($team->settings);
        $this->assertEquals('value1', $team->settings['key1']);
        $this->assertEquals('value2', $team->settings['key2']);
    }

    /**
     * Test personal_team boolean casting.
     */
    public function test_personal_team_is_cast_to_boolean(): void
    {
        $personalTeam = Team::factory()->personal()->create();
        $regularTeam = Team::factory()->create(['personal_team' => false]);

        $this->assertIsBool($personalTeam->personal_team);
        $this->assertTrue($personalTeam->personal_team);
        $this->assertFalse($regularTeam->personal_team);
    }

    /**
     * Test team members using pivot model.
     */
    public function test_team_members_use_pivot_model(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $team->addMember($user, TeamRole::Admin);

        $pivot = $team->members()->first()->pivot;

        $this->assertInstanceOf(TeamMember::class, $pivot);
        $this->assertEquals(TeamRole::Admin, $pivot->role);
    }
}