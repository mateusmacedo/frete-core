<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use Frete\Core\Domain\Validators\{AttributeValidator, Validator};
use Frete\Core\Domain\Validators\ValidatorComposite;
use stdClass;
use Tests\TestCase;

class AttributeValidatorTest extends TestCase
{
    public function testConstructor(): void
    {
        $validator = $this->createMock(Validator::class);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertInstanceOf(AttributeValidator::class, $attributeValidator);
    }

    public function testGetAttribute(): void
    {
        $validator = $this->createMock(Validator::class);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertEquals('test', $attributeValidator->attribute);
    }

    public function testGetValidator(): void
    {
        $validator = $this->createMock(Validator::class);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertInstanceOf(Validator::class, $attributeValidator->getValidator());
    }

    public function testValidate(): void
    {
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(true);
        $attributeValidator = new AttributeValidator('test', $validator);

        $this->assertTrue($attributeValidator->validate(['test' => 'valid']));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate(['test' => ['valid', 'valid']]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate([]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate(['test' => null]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass = new stdClass();
        $inputClass->test = 'valid';

        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->test = ['valid', 'valid'];
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->test = [];
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->test = null;
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass = new stdClass();
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());
    }

    public function testValidateFail(): void
    {
        $input = ['test' => 'invalid'];
        $errorMesssage = 'error';
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input = ['test' => ['valid', 'invalid']];
        $errorMesssage = [1 => 'error'];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input = new stdClass();
        $input->test = 'invalid';
        $errorMesssage = 'error';
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input->test = ['valid', 'invalid'];
        $errorMesssage = [1 => 'error'];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());
    }

    public function testValidateFailWithValidatorGetMessageAsArray(): void
    {
        $input = ['test' => 'invalid'];
        $errorMesssage = ['error1', 'error2'];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input = ['test' => ['valid', 'invalid']];
        $errorMesssage = [1 => ['error1', 'error2']];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input = new stdClass();
        $input->test = 'invalid';
        $errorMesssage = ['error1', 'error2'];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());

        $input->test = ['valid', 'invalid'];
        $errorMesssage = [1 => ['error1', 'error2']];
        $validator = $this->createMock(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(false, true);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('test', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($errorMesssage, $attributeValidator->getErrorMessage());
    }
}
