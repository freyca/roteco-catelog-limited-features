<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $shippingAddress = Address::factory()
            ->for($user)
            ->create(['address_type' => AddressType::Shipping]);

        return [
            'id' => Str::ulid(),
            'user_id' => $user->id,
            'shipping_address_id' => $shippingAddress->id,
            'purchase_cost' => fake()->randomFloat(2, 10, 3000),
            'payment_method' => fake()->randomElement(PaymentMethod::cases())->value,
            'status' => fake()->randomElement(OrderStatus::cases()),
        ];
    }
}
