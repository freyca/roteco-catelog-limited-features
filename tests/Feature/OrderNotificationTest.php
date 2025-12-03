<?php

use App\Enums\Role;
use App\Models\Address;
use App\Models\Disassembly;
use App\Models\Order;
use App\Models\ProductSparePart;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\Notification;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('Order Notifications', function () {
    beforeEach(function () {
        Notification::fake();
        test()->admin = User::factory()->admin()->create();
    });

    describe('OrderCreated Event', function () {
        it('dispatches with order instance', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            expect($order)->toBeInstanceOf(Order::class);
        });

        it('triggers listeners when order is created', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            Notification::assertSentTo($user, OrderConfirmationNotification::class);
        });
    });

    describe('SendOrderConfirmationToUser Listener', function () {
        it('sends confirmation notification to user', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            Notification::assertSentTo($user, OrderConfirmationNotification::class);
        });

        it('notification is sent exactly once', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            expect(Notification::sent($user, OrderConfirmationNotification::class))->toHaveCount(1);
        });
    });

    describe('SendOrderNotificationToAdmin Listener', function () {
        it('sends admin notification when order is created', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            Notification::assertSentTo(test()->admin, AdminOrderNotification::class);
        });

        it('throws exception if no admin exists', function () {
            User::where('role', Role::Admin)->delete();
            $user = User::factory()->create();

            expect(function () use ($user) {
                Order::factory()->for($user)->create();
            })->toThrow(RuntimeException::class);
        });

        it('sends to admin exactly once', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            expect(Notification::sent(test()->admin, AdminOrderNotification::class))->toHaveCount(1);
        });
    });

    describe('OrderConfirmationNotification', function () {
        it('sends via mail channel', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            Notification::assertSentTo(
                $user,
                OrderConfirmationNotification::class,
                function ($notification) use ($user) {
                    return in_array('mail', $notification->via($user));
                }
            );
        });

        it('notification is sent exactly once', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            expect(Notification::sent($user, OrderConfirmationNotification::class))->toHaveCount(1);
        });

        it('notification contains order data', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            Notification::assertSentTo($user, OrderConfirmationNotification::class);

            $order->load('orderProducts.orderable', 'user', 'shippingAddress', 'billingAddress');
            $notification = new OrderConfirmationNotification($order);
            $mail = $notification->toMail($user);
            $rendered = $mail->render();

            expect(str_contains($rendered, (string) $order->id))->toBeTrue();
            expect(str_contains($rendered, $user->name))->toBeTrue();
            expect(str_contains($rendered, $order->shippingAddress->address))->toBeTrue();
            expect(str_contains($rendered, __('Order Confirmation')))->toBeTrue();
            expect(str_contains($rendered, __('Order Status')))->toBeTrue();
        });

        it('notification displays order products', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            // Create ProductSparePart with its dependencies
            $disassembly = Disassembly::factory()->create();
            $sparePart = ProductSparePart::factory()->published()->for($disassembly)->create();

            // Add product to order using spare part's actual price
            $order->orderProducts()->create([
                'orderable_id' => $sparePart->id,
                'orderable_type' => ProductSparePart::class,
                'quantity' => 2,
                'unit_price' => $sparePart->price,
                'assembly_price' => 0,
            ]);

            Notification::assertSentTo($user, OrderConfirmationNotification::class);

            $order->load('orderProducts.orderable', 'user', 'shippingAddress', 'billingAddress');
            $notification = new OrderConfirmationNotification($order);
            $mail = $notification->toMail($user);
            $rendered = $mail->render();

            // Verify notification includes product details
            expect(str_contains($rendered, (string) $order->id))->toBeTrue();
            expect(str_contains($rendered, __('Thank you for your order!')))->toBeTrue();
            expect(str_contains($rendered, $sparePart->name))->toBeTrue();
            expect(str_contains($rendered, '2'))->toBeTrue(); // quantity
            expect(str_contains($rendered, number_format($sparePart->price / 100, 2)))->toBeTrue(); // price
        });
    });

    describe('AdminOrderNotification', function () {
        it('sends via mail channel', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            Notification::assertSentTo(
                test()->admin,
                AdminOrderNotification::class,
                function ($notification) {
                    return in_array('mail', $notification->via(test()->admin));
                }
            );
        });

        it('notification is sent exactly once to admin', function () {
            $user = User::factory()->create();
            Order::factory()->for($user)->create();

            expect(Notification::sent(test()->admin, AdminOrderNotification::class))->toHaveCount(1);
        });

        it('notification contains order and customer data', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            Notification::assertSentTo(test()->admin, AdminOrderNotification::class);

            $order->load('orderProducts.orderable', 'user', 'shippingAddress', 'billingAddress');
            $notification = new AdminOrderNotification($order);
            $mail = $notification->toMail(test()->admin);
            $rendered = $mail->render();

            expect(str_contains($rendered, (string) $order->id))->toBeTrue();
            expect(str_contains($rendered, $user->name))->toBeTrue();
            expect(str_contains($rendered, $user->email))->toBeTrue();
            expect(str_contains($rendered, $order->shippingAddress->address))->toBeTrue();
            expect(str_contains($rendered, __('New Order Created')))->toBeTrue();
            expect(str_contains($rendered, __('Customer')))->toBeTrue();
        });

        it('notification displays admin order products and pricing', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            // Create ProductSparePart with its dependencies
            $disassembly = Disassembly::factory()->create();
            $sparePart = ProductSparePart::factory()->published()->for($disassembly)->create();

            // Add product to order using spare part's actual price
            $order->orderProducts()->create([
                'orderable_id' => $sparePart->id,
                'orderable_type' => ProductSparePart::class,
                'quantity' => 3,
                'unit_price' => $sparePart->price,
                'assembly_price' => 0,
            ]);

            Notification::assertSentTo(test()->admin, AdminOrderNotification::class);

            $order->load('orderProducts.orderable', 'user', 'shippingAddress', 'billingAddress');
            $notification = new AdminOrderNotification($order);
            $mail = $notification->toMail(test()->admin);
            $rendered = $mail->render();

            // Verify admin notification includes product and pricing details
            expect(str_contains($rendered, (string) $order->id))->toBeTrue();
            expect(str_contains($rendered, __('Payment Method')))->toBeTrue();
            expect(str_contains($rendered, __('Total Amount')))->toBeTrue();
            expect(str_contains($rendered, $sparePart->name))->toBeTrue();
            expect(str_contains($rendered, '3'))->toBeTrue(); // quantity
            expect(str_contains($rendered, __('Products')))->toBeTrue();
            expect(str_contains($rendered, number_format($sparePart->price / 100, 2)))->toBeTrue(); // price
        });
    });

    describe('Order with Addresses', function () {
        it('sends notification with shipping address', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            expect($order->shippingAddress)->not->toBeNull();
        });

        it('handles billing address different from shipping', function () {
            $user = User::factory()->create();
            $shippingAddress = Address::factory()->for($user)->create();
            $billingAddress = Address::factory()->for($user)->create();
            $order = Order::factory()
                ->for($user)
                ->for($shippingAddress, 'shippingAddress')
                ->for($billingAddress, 'billingAddress')
                ->create();

            expect($order->shippingAddress->id)->not->toBe($order->billingAddress->id);
        });
    });

    describe('Multiple Orders', function () {
        it('sends independent notifications for each order', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();

            Order::factory()->for($user1)->create();
            Order::factory()->for($user2)->create();

            Notification::assertSentTo($user1, OrderConfirmationNotification::class);
            Notification::assertSentTo($user2, OrderConfirmationNotification::class);
            expect(Notification::sent(test()->admin, AdminOrderNotification::class))->toHaveCount(2);
        });
    });

    describe('Order Products Validation', function () {
        it('order must have at least one product', function () {
            $user = User::factory()->create();
            $order = Order::factory()->for($user)->create();

            // Verify order was created but has no products
            expect($order->orderProducts()->count())->toBe(0);

            // Add a product to the order
            $disassembly = Disassembly::factory()->create();
            $sparePart = ProductSparePart::factory()->published()->for($disassembly)->create();

            $order->orderProducts()->create([
                'orderable_id' => $sparePart->id,
                'orderable_type' => ProductSparePart::class,
                'quantity' => 1,
                'unit_price' => $sparePart->price,
                'assembly_price' => 0,
            ]);

            // Verify product was added
            expect($order->orderProducts()->count())->toBe(1);
        });
    });
});
