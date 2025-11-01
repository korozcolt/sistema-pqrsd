<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\StatusTicket;
use App\Enums\TicketType;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'type' => fake()->randomElement(TicketType::cases()),
            'status' => StatusTicket::Pending,
            'priority' => fake()->randomElement(Priority::cases()),
            'response_due_date' => now()->addHours(24),
            'resolution_due_date' => now()->addDays(15),
        ];
    }

    /**
     * Indicate that the ticket is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTicket::In_Progress,
        ]);
    }

    /**
     * Indicate that the ticket is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTicket::Resolved,
            'resolution_at' => now(),
        ]);
    }

    /**
     * Indicate that the ticket is closed.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTicket::Closed,
            'resolution_at' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the ticket is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTicket::Rejected,
        ]);
    }

    /**
     * Indicate that the ticket is reopened.
     */
    public function reopened(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTicket::Reopened,
        ]);
    }

    /**
     * Indicate that the ticket is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Priority::High,
        ]);
    }

    /**
     * Indicate that the ticket is urgent priority.
     */
    public function urgentPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => Priority::Urgent,
        ]);
    }
}
