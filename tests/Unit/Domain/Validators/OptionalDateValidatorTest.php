<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use Frete\Core\Domain\Validators\OptionalDateValidator;
use Tests\TestCase;

class OptionalDateValidatorTest extends TestCase
{
    public function testShouldValidateDate()
    {
        $validator = new OptionalDateValidator();
        $this->assertTrue($validator->validate('2020-05-05 15:54:00'));
    }

    public function testShouldValidateDateIsNull()
    {
        $validator = new OptionalDateValidator();
        $this->assertTrue($validator->validate(null));
    }

    public function testShouldNotValidateDate()
    {
        $validator = new OptionalDateValidator();
        $this->assertFalse($validator->validate('2020/14/01 24:33:12'));
        $this->assertEquals('Invalid date format', $validator->getErrorMessage());
    }
}
