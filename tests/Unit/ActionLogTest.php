<?php

namespace Tests\Unit;

use App\Models\ActionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActionLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_action_log_entry()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an action log entry
        $actionLog = ActionLog::create([
            'user_id' => $user->id,
            'action' => 'User logged in',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'changes' => ['name' => 'Updated name'], // Array; Laravel will cast it
            'ip_address' => '127.0.0.1',
        ]);

        // Assert main columns exist in the database
        $this->assertDatabaseHas('action_logs', [
            'user_id' => $user->id,
            'action' => 'User logged in',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'ip_address' => '127.0.0.1',
        ]);

        // Assert JSON column value using path syntax (works cross-env)
        $this->assertDatabaseHas('action_logs', [
            'changes->name' => 'Updated name',
        ]);

        // Optional: Confirm the model was saved
        $this->assertTrue($actionLog->exists);
    }

    #[Test] public function it_casts_changes_to_array()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an action log entry with changes stored as an array
        $actionLog = ActionLog::create([
            'user_id' => $user->id,
            'action' => 'User updated profile',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'changes' => ['name' => 'Updated name'],
            'ip_address' => '127.0.0.1',
        ]);

        // Assert the changes attribute is cast to array
        $this->assertIsArray($actionLog->changes);
        $this->assertEquals(['name' => 'Updated name'], $actionLog->changes);
    }

    #[Test] public function it_belongs_to_a_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an action log for this user
        $actionLog = ActionLog::create([
            'user_id' => $user->id,
            'action' => 'User logged in',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'changes' => ['name' => 'Updated name'],
            'ip_address' => '127.0.0.1',
        ]);

        // Assert the action log belongs to the correct user
        $this->assertEquals($user->id, $actionLog->user->id);
    }
}
