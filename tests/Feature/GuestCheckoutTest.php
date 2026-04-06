<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_place_order_and_open_thanks_via_signed_url(): void
    {
        Mail::fake();

        $product = Product::factory()->create(['slug' => 'guest-set', 'price' => 40, 'stock' => 5]);

        $this->post(route('cart.add', $product));

        $response = $this->post(route('checkout.store'), [
            'guest_email' => 'guest@example.com',
            'full_name' => 'Guest User',
            'phone' => '+380501112233',
            'delivery_type' => 'nova',
            'nova_city' => 'Kyiv',
            'nova_branch' => 'Branch 1',
            'payment_type' => 'cash',
            'note' => null,
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('signature=', $response->headers->get('Location') ?? '');

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertSame('guest@example.com', $order->guest_email);

        Mail::assertSent(OrderPlacedMail::class);

        $signed = URL::signedRoute('checkout.thanks', ['order' => $order]);
        $this->get($signed)->assertStatus(200)->assertSee((string) $order->id, false);
    }

    public function test_authenticated_user_checkout_still_works_without_signature_on_thanks(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['slug' => 'auth-set', 'price' => 30, 'stock' => 10]);

        $this->actingAs($user)->post(route('cart.add', $product));

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'full_name' => 'Auth User',
                'phone' => '+380501112244',
                'delivery_type' => 'nova',
                'nova_city' => 'Kyiv',
                'nova_branch' => 'Branch 1',
                'payment_type' => 'cash',
                'note' => null,
            ])
            ->assertRedirect(route('checkout.thanks', Order::first()));

        $this->actingAs($user)->get(route('checkout.thanks', Order::first()))->assertOk();
    }
}
