<?php

namespace Database\Factories;

use App\Enums\ReminderType;
use App\Models\Reminder;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'sent_to' => User::factory(),
            'reminder_type' => fake()->randomElement(ReminderType::cases()),
            'is_read' => false,
            'sent_at' => now(),
            'read_at' => null,
        ];
    }

    /**
     * Indicate that the reminder is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the reminder is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Set specific reminder type: Half Time Response.
     */
    public function halfTimeResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_type' => ReminderType::HalfTimeResponse,
        ]);
    }

    /**
     * Set specific reminder type: Day Before Response.
     */
    public function dayBeforeResponse(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_type' => ReminderType::DayBeforeResponse,
        ]);
    }

    /**
     * Set specific reminder type: Half Time Resolution.
     */
    public function halfTimeResolution(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_type' => ReminderType::HalfTimeResolution,
        ]);
    }

    /**
     * Set specific reminder type: Day Before Resolution.
     */
    public function dayBeforeResolution(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_type' => ReminderType::DayBeforeResolution,
        ]);
    }

    /**
     * Set sent_at to the past.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at' => now()->subHours(2),
        ]);
    }

    /**
     * Set sent_at to the future (scheduled).
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at' => now()->addHours(2),
        ]);
    }
}
