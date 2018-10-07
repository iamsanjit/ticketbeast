<?php 

namespace Tests\Unit\Mail;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Mail\OrderConfirmationEmail;
use App\Order;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_the_link_to_order_confirmation_page()
    {
        $order = factory(Order::class)->make(['confirmation_number' => 'ORDERCONFIRMATION123']);
        $email = new OrderConfirmationEmail($order);

        $rendered = $email->render($email);

        $this->assertContains(url('/orders/ORDERCONFIRMATION123'), $rendered);
    }

    /** @test */
    public function email_has_a_subject()
    {
        $order = factory(Order::class)->make(['confirmation_number' => 'ORDERCONFIRMATION123']);
        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your Order Confirmation', $email->build()->subject);
    }
}
