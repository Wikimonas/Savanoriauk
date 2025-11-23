<?php
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventCreation extends TestCase
{
    use RefreshDatabase;

    #[Test] public function organiser_can_create_event_successfully()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $validData = [
            'name' => 'Volunteer Day',
            'description' => 'A great opportunity to help the community.',
            'address' => 'City Center, Vilnius',
            'event_date' => now()->addDays(7)->toDateString(),
        ];

        $response = $this->actingAs($organiser)
            ->post(route('events.store'), $validData);

        $response->assertRedirect(route('events.manage'));
        $this->assertDatabaseHas('events', [
            'name' => 'Volunteer Day',
            'description' => 'A great opportunity to help the community.',
            'address' => 'City Center, Vilnius',
            'organiser_id' => $organiser->id,
        ]);
    }

    #[Test] public function organiser_cannot_create_event_with_too_short_data_or_past_date()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $invalidData = [
            'name' => 'Hi',
            'description' => 'No',
            'address' => 'LT',
            'event_date' => now()->subDay()->toDateString(),
        ];

        $response = $this->actingAs($organiser)
            ->from(route('events.create'))
            ->post(route('events.store'), $invalidData);

        $response->assertRedirect(route('events.create'));
        $response->assertSessionHasErrors([
            'name',
            'description',
            'address',
            'event_date',
        ]);

        $this->assertDatabaseMissing('events', [
            'name' => 'Hi',
        ]);
    }

    #[Test] public function organiser_cannot_create_event_with_empty_fields()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $response = $this->actingAs($organiser)
            ->from(route('events.create'))
            ->post(route('events.store'), [
                'name' => '',
                'description' => '',
                'address' => '',
                'event_date' => '',
            ]);

        $response->assertRedirect(route('events.create'));

        $response->assertSessionHasErrors([
            'name',
            'description',
            'address',
            'event_date',
        ]);

        $this->assertDatabaseCount('events', 0); // Ensure nothing was saved
    }

    #[Test] public function organiser_can_create_event_with_non_latin_characters_and_special_symbols()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        $data = [
            'name' => 'Renginys žiemos šventei! 🎉',
            'description' => 'Aprašymas su lietuviškomis raidėmis: ąčęėįšųūž. Taip pat ir emoji 😄.',
            'address' => 'Šventės g. 5, Vilnius! 💫',
            'event_date' => now()->addWeek()->toDateString(),
        ];

        $response = $this->actingAs($organiser)
            ->post(route('events.store'), $data);

        $response->assertRedirect(route('events.manage'));

        $this->assertDatabaseHas('events', [
            'name' => $data['name'],
            'description' => $data['description'],
            'address' => $data['address'],
        ]);
    }

}

