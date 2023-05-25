<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Validators;

use Frete\Core\Domain\Validators\FloatValidator;
use Frete\Core\Domain\Validators\{NotNullValidator, StringValidator, ValidatorComposite};
use Tests\TestCase;

final class ValidatorCompositeTest extends TestCase
{
    protected ValidatorComposite $sut;

    public function createSut(): void
    {
        $this->sut = new ValidatorComposite();
    }

    public function createSutWithValidator(array $validators): void
    {
        $this->createSut();
        foreach ($validators as $validator) {
            $this->sut->addValidator($validator);
        }
    }

    public function createNotNullStringCompositeValidator(): ValidatorComposite
    {
        $notNullValidator = new NotNullValidator();
        $stringValidator = new StringValidator();
        $notNullStringValidator = new ValidatorComposite();
        $notNullStringValidator->addValidator($notNullValidator);
        $notNullStringValidator->addValidator($stringValidator);
        return $notNullStringValidator;
    }

    public function createNotNullFloatCompositeValidator(): ValidatorComposite
    {
        $notNullValidator = new NotNullValidator();
        $floatValidator = new FloatValidator();
        $notNullFloatValidator = new ValidatorComposite();
        $notNullFloatValidator->addValidator($notNullValidator);
        $notNullFloatValidator->addValidator($floatValidator);
        return $notNullFloatValidator;
    }

    public function testValidateWithEmptyValidators(): void
    {
        $this->createSut();
        $this->assertTrue($this->sut->validate(''));
    }

    public function testShouldValidateNotNullString(): void
    {
        $this->createSutWithValidator([new NotNullValidator(), new StringValidator()]);
        $this->assertTrue($this->sut->validate('test'));
    }

    public function testShouldValidateNotNullStringWithNull(): void
    {
        $expected = [
            'isValid' => false,
            'errorMessage' => [
                'Cannot be null',
                'Invalid string',
            ],
        ];
        $this->createSutWithValidator([new NotNullValidator(), new StringValidator()]);
        $result = [
            'isValid' => $this->sut->validate(null),
            'errorMessage' => $this->sut->getErrorMessage(),
        ];
        $this->assertEquals($expected, $result);
    }

    public function testShouldValidateNotNullStringWithThreeArrayEntries(): void
    {
        $validator = $this->createNotNullStringCompositeValidator();
        $this->createSutWithValidator([$validator]);
        $this->assertTrue($this->sut->validate(['test', 'test2', 'test3']));
    }

    public function testShoulNotValidateNotNullStringWithThreeArrayEntries(): void
    {
        $input = [null, null, null];
        $expected = [
            'isValid' => false,
            'errorMessage' => [
                [
                    [
                        'Cannot be null',
                        'Invalid string',
                    ]
                ],
                [
                    [
                        'Cannot be null',
                        'Invalid string',
                    ]
                ],
                [
                    [
                        'Cannot be null',
                        'Invalid string',
                    ]
                ]
            ],
        ];

        $validator = $this->createNotNullStringCompositeValidator();
        $this->createSutWithValidator([$validator]);
        $actual = [
            'isValid' => $this->sut->validate($input),
            'errorMessage' => $this->sut->getErrorMessage(),
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testShouldValidateNotNullFloatWithThreeArrayEntries(): void
    {
        $validator = $this->createNotNullFloatCompositeValidator();
        $this->createSutWithValidator([$validator]);
        $this->assertTrue($this->sut->validate([1.1, 2.2, 3.3]));
    }

    public function testShoulNotValidateNotNullFloatWithThreeArrayEntries(): void
    {
        $input = [null, null, null];
        $expected = [
            'isValid' => false,
            'errorMessage' => [
                [
                    [
                        'Cannot be null',
                        'Invalid float number',
                    ]
                ],
                [
                    [
                        'Cannot be null',
                        'Invalid float number',
                    ]
                ],
                [
                    [
                        'Cannot be null',
                        'Invalid float number',
                    ]
                ]
            ],
        ];

        $validator = $this->createNotNullFloatCompositeValidator();
        $this->createSutWithValidator([$validator]);
        $actual = [
            'isValid' => $this->sut->validate($input),
            'errorMessage' => $this->sut->getErrorMessage(),
        ];
        $this->assertEquals($expected, $actual);
    }
}
