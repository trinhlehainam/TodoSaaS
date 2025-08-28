<?php

namespace Tests\Unit;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamInvitationModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test team invitation creation with all fields.
     */
    public function test_can_create_team_invitation_with_all_fields(): void
    {
        $team = Team::factory()->create();
        $inviter = User::factory()->create();

        $invitation = TeamInvitation::query()->create([
            'team_id' => $team->id,
            'email' => 'test@example.com',
            'role' => TeamRole::Member->value,
            'invited_by' => $inviter->id,
        ]);

        $this->assertInstanceOf(TeamInvitation::class, $invitation);
        $this->assertEquals('test@example.com', $invitation->email);
        $this->assertEquals(TeamRole::Member, $invitation->role);
        $this->assertNotNull($invitation->token);
        $this->assertEquals(32, strlen($invitation->token));
    }

    /**
     * Test automatic token generation and expiration date on creation.
     */
    public function test_automatically_generates_token_and_expiration_on_creation(): void
    {
        $team = Team::factory()->create();
        $inviter = User::factory()->create();

        $invitation = TeamInvitation::query()->create([
            'team_id' => $team->id,
            'email' => 'test@example.com',
            'role' => TeamRole::Member->value,
            'invited_by' => $inviter->id,
        ]);

        $this->assertNotNull($invitation->token);
        $this->assertEquals(32, strlen($invitation->token));
        $this->assertNotNull($invitation->expires_at);
        $this->assertTrue($invitation->expires_at->isFuture());
        $this->assertEquals(7, round(abs($invitation->expires_at->diffInHours(now()) / 24)));
    }

    /**
     * Test team invitation relationships.
     */
    public function test_team_invitation_belongs_to_team_and_inviter(): void
    {
        $team = Team::factory()->create();
        $inviter = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by' => $inviter->id,
        ]);

        $this->assertInstanceOf(Team::class, $invitation->team);
        $this->assertEquals($team->id, $invitation->team->id);

        $this->assertInstanceOf(User::class, $invitation->inviter);
        $this->assertEquals($inviter->id, $invitation->inviter->id);
    }

    /**
     * Test isExpired method.
     */
    public function test_is_expired_method(): void
    {
        $validInvitation = TeamInvitation::factory()->expiringIn(3)->create();

        $expiredInvitation = TeamInvitation::factory()->expired()->create();

        $this->assertFalse($validInvitation->isExpired());
        $this->assertTrue($expiredInvitation->expires_at->isPast());
        $this->assertTrue($expiredInvitation->isExpired());
    }

    /**
     * Test accept invitation successfully.
     */
    public function test_accept_invitation_adds_user_to_team(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'email' => $user->email,
            'role' => TeamRole::Admin->value,
        ]);

        $this->assertFalse($team->hasUser($user));
        $this->assertCount(1, TeamInvitation::query()->get());

        $invitation->accept($user);

        $this->assertTrue($team->fresh()->hasUser($user));
        $this->assertEquals(TeamRole::Admin, $team->fresh()->userRole($user));
        $this->assertCount(0, TeamInvitation::query()->get());
    }

    /**
     * Test accept expired invitation throws exception.
     */
    public function test_accept_expired_invitation_throws_exception(): void
    {
        $user = User::factory()->create();
        $invitation = TeamInvitation::factory()->expired()->create([
            'email' => $user->email,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This invitation has expired.');

        $invitation->accept($user);
    }

    /**
     * Test reject invitation deletes it.
     */
    public function test_reject_invitation_deletes_it(): void
    {
        $invitation = TeamInvitation::factory()->create();

        $this->assertCount(1, TeamInvitation::query()->get());

        $invitation->reject();

        $this->assertCount(0, TeamInvitation::query()->get());
    }

    /**
     * Test role casting to TeamRole enum.
     */
    public function test_role_is_cast_to_team_role_enum(): void
    {
        $invitation = TeamInvitation::factory()->forRole(TeamRole::Admin)->create();

        $this->assertInstanceOf(TeamRole::class, $invitation->role);
        $this->assertEquals(TeamRole::Admin, $invitation->role);
    }

    /**
     * Test expires_at casting to datetime.
     */
    public function test_expires_at_is_cast_to_datetime(): void
    {
        $invitation = TeamInvitation::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $invitation->expires_at);
    }

    /**
     * Test multiple invitations for same team.
     */
    public function test_team_can_have_multiple_invitations(): void
    {
        $team = Team::factory()->create();
        
        TeamInvitation::factory()->count(3)->create([
            'team_id' => $team->id,
        ]);

        $this->assertCount(3, $team->invitations);
        $this->assertCount(3, TeamInvitation::query()->where('team_id', $team->id)->get());
    }

    /**
     * Test invitation with different roles.
     */
    public function test_invitations_can_have_different_roles(): void
    {
        $adminInvite = TeamInvitation::factory()->forRole(TeamRole::Admin)->create();
        $memberInvite = TeamInvitation::factory()->forRole(TeamRole::Member)->create();
        $viewerInvite = TeamInvitation::factory()->forRole(TeamRole::Viewer)->create();

        $this->assertEquals(TeamRole::Admin, $adminInvite->role);
        $this->assertEquals(TeamRole::Member, $memberInvite->role);
        $this->assertEquals(TeamRole::Viewer, $viewerInvite->role);
    }
}