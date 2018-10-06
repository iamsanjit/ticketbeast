<?php 

use Tests\TestCase;
use App\HashidsTicketCodeGenerator;
use App\Ticket;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    public function ticket_code_must_be_atleast_6_characters_long()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    public function ticket_code_can_only_contain_uppercase_letters()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertRegEXp('/^[A-Z]+$/', $code);
    }

    /** @test */
    public function ticket_code_for_the_same_ticket_id_are_same()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt');
    
        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }


    /** @test */
    public function ticket_code_for_the_different_ticket_id_are_different()
    {
        $ticketCodeGenerator = new HashidsTicketCodeGenerator('salt');

        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function ticket_code_generated_with_different_salts_are_different()
    {
        $ticketCodeGenerator1 = new HashidsTicketCodeGenerator('salt1');
        $ticketCodeGenerator2 = new HashidsTicketCodeGenerator('salt2');

        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}
