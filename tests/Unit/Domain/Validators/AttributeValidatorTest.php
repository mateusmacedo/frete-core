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
        $validator = $this->createStub(Validator::class);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertInstanceOf(AttributeValidator::class, $attributeValidator);
    }

    public function testGetAttribute(): void
    {
        $validator = $this->createStub(Validator::class);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertEquals('attribute', $attributeValidator->attribute);
    }

    public function testSetValidator(): void
    {
        $validator = $this->createStub(Validator::class);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $attributeValidator->setValidator($validator);
        $this->assertInstanceOf(Validator::class, $attributeValidator->getValidator());
    }

    public function testGetValidator(): void
    {
        $validator = $this->createStub(Validator::class);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertInstanceOf(Validator::class, $attributeValidator->getValidator());
    }

    public function testValidate(): void
    {
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturn(true);
        $attributeValidator = new AttributeValidator('attribute', $validator);

        $this->assertTrue($attributeValidator->validate(['attribute' => 'validInput']));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate(['attribute' => ['validInput', 'validInput']]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate([]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $this->assertTrue($attributeValidator->validate(['attribute' => null]));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass = new stdClass();
        $inputClass->attribute = 'validInput';

        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->attribute = ['validInput', 'validInput'];
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->attribute = [];
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass->attribute = null;
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());

        $inputClass = new stdClass();
        $this->assertTrue($attributeValidator->validate($inputClass));
        $this->assertNull($attributeValidator->getErrorMessage());
    }

    public function testValidateFail(): void
    {
        $input = ['attribute' => 'invalidInput'];
        $errorMesssage = 'error';
        $expectedErrorMessage = ['attribute' => $errorMesssage];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input = ['attribute' => ['valid', 'invalidInput']];
        $expectedErrorMessage = ['attribute' => [
            1 => $errorMesssage
        ]];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(true, false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));        $errorMesssage = 'error';

        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input = new stdClass();
        $input->attribute = 'invalidInput';
        $expectedErrorMessage = ['attribute' => $errorMesssage];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input->attribute = ['valid', 'invalidInput'];
        $expectedErrorMessage = ['attribute' => [1 => $errorMesssage]];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(true, false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());
    }

    public function testValidateFailWithValidatorGetMessageAsArray(): void
    {
        $input = ['attribute' => 'invalidInput'];
        $errorMesssage = ['error1', 'error2'];
        $expectedErrorMessage = ['attribute' => $errorMesssage];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input = ['attribute' => ['valid', 'invalidInput']];
        $errorMesssage = ['error1', 'error2'];
        $expectedErrorMessage = ['attribute' => [1 => $errorMesssage]];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(true, false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input = new stdClass();
        $input->attribute = 'invalidInput';
        $errorMesssage = ['error1', 'error2'];
        $expectedErrorMessage = ['attribute' => $errorMesssage];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturn(false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());

        $input->attribute = ['valid', 'invalidInput'];
        $errorMesssage = ['error1', 'error2'];
        $expectedErrorMessage = ['attribute' => [1 => $errorMesssage]];
        $validator = $this->createStub(Validator::class);
        $validator->method('validate')->willReturnOnConsecutiveCalls(true, false);
        $validator->method('getErrorMessage')->willReturn($errorMesssage);
        $attributeValidator = new AttributeValidator('attribute', $validator);
        $this->assertFalse($attributeValidator->validate($input));
        $this->assertEquals($expectedErrorMessage, $attributeValidator->getErrorMessage());
    }
}
