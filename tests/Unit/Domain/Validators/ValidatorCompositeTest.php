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
        $validator1 = $this->createStub(Validator::class);
        $validator2 = $this->createStub(Validator::class);
        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);

        $this->assertCount(2, $validatorComposite->getValidators());
    }

    public function testValidateSingleValue()
    {
        $validatorComposite = new ValidatorComposite();

        $validatorComposite = new ValidatorComposite();
        $validator1 = $this->createStub(Validator::class);
        $validator1->method('validate')->willReturn(true);
        $validator2 = $this->createStub(Validator::class);
        $validator2->method('validate')->willReturn(true);
        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);

        $isValid = $validatorComposite->validate('some-value');
        $this->assertTrue($isValid);
    }

    public function testValidateMultipleValues()
    {
        $input = ['some-value-1', ['some-value-2', 'some-value-3']];

        $expected = [
            'isValid' => false,
            'errorMessage' => [
                [
                    'validation error 2',
                    'validation error 3',
                ],
                [
                    ['validation error 2', 'validation error 2'],
                    ['validation error 3', 'validation error 3']
                ],
            ],
        ];

        $validator1 = $this->createStub(Validator::class);
        $validator1->method('validate')->willReturn(true);

        $validator2 = $this->createStub(Validator::class);
        $validator2->method('validate')->willReturn(false);
        $validator2->method('getErrorMessage')->willReturnOnConsecutiveCalls(
            'validation error 2',['validation error 2', 'validation error 2']
        );

        $validator3 = $this->createStub(Validator::class);
        $validator3->method('validate')->willReturn(false);
        $validator3->method('getErrorMessage')->willReturnOnConsecutiveCalls(
            'validation error 3',['validation error 3', 'validation error 3']
        );

        $validatorComposite = new ValidatorComposite();
        $validatorComposite->addValidator($validator1);
        $validatorComposite->addValidator($validator2);
        $validatorComposite->addValidator($validator3);

        $actual = [
            'isValid' => $validatorComposite->validate($input),
            'errorMessage' => $validatorComposite->getErrorMessage(),
        ];
        $this->assertEquals($expected, $actual);
    }
}
