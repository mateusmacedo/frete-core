<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Validators;

use Frete\Core\Domain\Validators\{Validator, ValidatorComposite};
use Tests\TestCase;

class ValidatorCompositeTest extends TestCase
{
    public function testAddValidator()
    {
        $validatorComposite = new ValidatorComposite();
        $validator1 = $this->createMock(Validator::class);
        $validator2 = $this->createMock(Validator::class);

        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);

        $this->assertCount(2, $validatorComposite->validators);
    }

    public function testValidateSingleValue()
    {
        $validatorComposite = new ValidatorComposite();

        $validator1 = $this->createMock(Validator::class);
        $validator1->method('validate')->willReturn(true);

        $validator2 = $this->createMock(Validator::class);
        $validator2->method('validate')->willReturn(true);

        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);

        $isValid = $validatorComposite->validate('some-value');
        $this->assertTrue($isValid);
    }

    public function testValidateMultipleValues()
    {
        $validatorComposite = new ValidatorComposite();

        $validator1 = $this->createMock(Validator::class);
        $validator1->method('validate')->willReturn(false);
        $validator1->method('getErrorMessage')->willReturn('validation error 1');

        $validator2 = $this->createMock(Validator::class);
        $validator2->method('validate')->willReturn(false);
        $validator2->method('getErrorMessage')->willReturn('validation error 2');

        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);

        $input = ['some-value-1', 'some-value-2'];
        $isValid = $validatorComposite->validate($input);
        $errors = $validatorComposite->getErrorMessage();

        $this->assertFalse($isValid);
        $this->assertCount(2, $errors);
        foreach ($errors as $error) {
            $this->assertCount(2, $error);
            $this->assertContains('validation error 1', $error);
            $this->assertContains('validation error 2', $error);
        }
    }
}
