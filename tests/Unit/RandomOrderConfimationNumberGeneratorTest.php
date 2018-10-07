<?php 

namespace Tests\Unit;

use Tests\TestCase;
use App\RandomOrderConfirmationNumberGenerator;

class RandomOrderConfimationNumberGeneratorTest extends TestCase
{
    // Cannot contain ambiguis chracters
    // All order confirmation number must be unique

    /** @test */
    public function must_be_24_chracters_long()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();
        
        $this->assertEquals(24, strlen($confirmationNumber));
    }

    /** @test */
    public function must_contain_uppercase_letters_and_numbers()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    public function cannot_contain_ambiguis_characters()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;

        $confirmationNumber = $generator->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
    }

    /** @test */
    public function must_generate_unique_confirmaiton_numbers()
    {
        $generator = new RandomOrderConfirmationNumberGenerator;
        
        $confirmationNumbers = array_map(function ($i) use ($generator) {
            return $generator->generate();
        }, range(1, 100));

        $this->assertCount(100, array_unique($confirmationNumbers));
    }
}
