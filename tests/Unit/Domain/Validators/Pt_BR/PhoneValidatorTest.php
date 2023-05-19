<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators\Pt_BR;

use Frete\Core\Domain\Validators\Pt_BR\PhoneValidator;
use Tests\TestCase;

class PhoneValidatorTest extends TestCase
{
    public function testShouldValidatePhone()
    {
        $validator = new PhoneValidator();
        $this->assertTrue($validator->validate('+55(21)98765-5678'));
    }

    public function testShouldNotValidatePhone()
    {
        $validator = new PhoneValidator();
        $this->assertFalse($validator->validate('+55(211)8765-5678'));
        $this->assertEquals('Invalid phone number', $validator->getErrorMessage());
    }
}
