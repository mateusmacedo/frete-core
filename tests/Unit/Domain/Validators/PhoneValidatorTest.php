<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Validators;

use Frete\Core\Domain\Validators\PhoneValidator;
use Tests\TestCase;

class PhoneValidatorTest extends TestCase
{
    public function testShouldValidatePhone()
    {
        $validator = new PhoneValidator();
        $this->assertTrue($validator->validate('2198765567'));
    }

    public function testShouldNotValidatePhone()
    {
        $validator = new PhoneValidator();
        $this->assertFalse($validator->validate('2198765544321'));
        $this->assertEquals('Invalid phone number', $validator->getErrorMessage());
    }
}
