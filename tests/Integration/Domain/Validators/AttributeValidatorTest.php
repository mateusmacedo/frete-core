<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Validators;

use ArrayObject;
use Frete\Core\Domain\Validators\ValidatorComposite;
use Frete\Core\Domain\Validators\{AttributeValidator, FloatValidator, NotNullValidator, OneOfOptionsValidator, StringValidator};
use Tests\TestCase;

final class AttributeValidatorTest extends TestCase
{
    private AttributeValidator $sut;

    public function testValidateSingle(): void
    {
        $input = [
            'attributeRoot' => [
                'attributeOne' => 'string',
                'attributeTwo' => 1.1,
                'attributeThree' => 'option1',
            ]
        ];

        $expected = [
            'isValid' => true,
            'errorMessage' => [
                'attributeRoot' => []
            ]
        ];

        $notNullValidator = new NotNullValidator();
        $stringValidator = new StringValidator();
        $floatValidator = new FloatValidator();
        $oneOfOptionsValidator = new OneOfOptionsValidator(['option1', 'option2']);

        $notNullStringCompositeValidator = new ValidatorComposite(new ArrayObject([$notNullValidator, $stringValidator]));
        $notNullFloatCompositeValidator = new ValidatorComposite(new ArrayObject([$notNullValidator, $floatValidator]));
        $notNullOneOfOptionsCompositeValidator = new ValidatorComposite(new ArrayObject([$notNullValidator, $oneOfOptionsValidator]));

        $attributeOneValidator = new AttributeValidator('attributeOne', $notNullStringCompositeValidator);
        $attributeTwoValidator = new AttributeValidator('attributeTwo', $notNullFloatCompositeValidator);
        $attributeThreeValidator = new AttributeValidator('attributeThree', $notNullOneOfOptionsCompositeValidator);

        $attributeRootValidator = new ValidatorComposite(new ArrayObject([
            $attributeOneValidator,
            $attributeTwoValidator,
            $attributeThreeValidator
        ]));

        $this->sut = new AttributeValidator('attributeRoot', $attributeRootValidator);

        $actual = [
            'isValid' => $this->sut->validate($input),
            'errorMessage' => $this->sut->getErrorMessage()
        ];

        $this->assertEquals($expected, $actual);
    }
}
