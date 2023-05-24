<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Validators;

use Frete\Core\Domain\Validators\AttributeValidator;
use Frete\Core\Domain\Validators\FloatValidator;
use Frete\Core\Domain\Validators\NotNullValidator;
use Frete\Core\Domain\Validators\OneOfOptionsValidator;
use Frete\Core\Domain\Validators\StringValidator;
use Frete\Core\Domain\Validators\ValidatorComposite;
use GuzzleHttp\Promise\Is;
use stdClass;
use Tests\TestCase;

final class ValidatorsTest extends TestCase
{
    public function testAttributeValidatoComposite()
    {
        // $this->markTestSkipped('This test is not ready to run yet.');
        $notNullValidator = new NotNullValidator();
        $floatValidator = new FloatValidator();
        $stringValidator = new StringValidator();
        $oneOfOptionsValidator = new OneOfOptionsValidator();
        $oneOfOptionsValidator->setOptions(['test', 'test2']);

        $notNullStringValidator = new ValidatorComposite();
        $notNullStringValidator->addValidator($notNullValidator);
        $notNullStringValidator->addValidator($stringValidator);

        $notNullFloatValidator = new ValidatorComposite();
        $notNullFloatValidator->addValidator($notNullValidator);
        $notNullFloatValidator->addValidator($floatValidator);

        $notNullOneOfOptionsValidator = new ValidatorComposite();
        $notNullOneOfOptionsValidator->addValidator($notNullValidator);
        $notNullOneOfOptionsValidator->addValidator($oneOfOptionsValidator);

        $attributeValidatorOne = new AttributeValidator('attributeOne');
        $attributeValidatorOne->setValidator($notNullStringValidator);

        $attributeValidatorTwo = new AttributeValidator('attributeTwo');
        $attributeValidatorTwo->setValidator($notNullFloatValidator);

        $attributeValidatorThree = new AttributeValidator('attributeThree');
        $attributeValidatorThree->setValidator($notNullOneOfOptionsValidator);

        $sutComposite = new ValidatorComposite();
        $sutComposite->addValidator($attributeValidatorOne);
        $sutComposite->addValidator($attributeValidatorTwo);
        $sutComposite->addValidator($attributeValidatorThree);

        $sut = new AttributeValidator('attribute', $sutComposite);

        $inputAsArray = [
            'attribute' => [
                [
                    'attributeOne' => ['test', 'test2'],
                    'attributeTwo' => [1.1, 2.2],
                    'attributeThree' => ['test', 'test2'],
                ],
                [
                    'attributeOne' => ['test', 'test2'],
                    'attributeTwo' => [1.1, 2.2],
                    'attributeThree' => ['test', 'test2'],
                ]
            ]
        ];

        $inputObject = new stdClass();
        $inputObject->attributeOne = ['test', 'test2'];
        $inputObject->attributeTwo = [1.1, 2.2];
        $inputObject->attributeThree = ['test', 'test2'];

        $attributeObject = new stdClass();
        $attributeObject->attribute = [$inputObject, $inputObject];

        $this->assertTrue($sut->validate($inputAsArray));
        $this->assertTrue($sut->validate($attributeObject));
    }

    public function testAttributeValidatoCompositeFailWithMessages()
    {
        // $this->markTestSkipped('This test is not ready to run yet.');
        $notNullValidator = new NotNullValidator();
        $floatValidator = new FloatValidator();
        $stringValidator = new StringValidator();
        $oneOfOptionsValidator = new OneOfOptionsValidator();
        $oneOfOptionsValidator->setOptions(['test', 'test2']);

        $notNullStringValidator = new ValidatorComposite();
        $notNullStringValidator->addValidator($notNullValidator);
        $notNullStringValidator->addValidator($stringValidator);

        $notNullFloatValidator = new ValidatorComposite();
        $notNullFloatValidator->addValidator($notNullValidator);
        $notNullFloatValidator->addValidator($floatValidator);

        $notNullOneOfOptionsValidator = new ValidatorComposite();
        $notNullOneOfOptionsValidator->addValidator($notNullValidator);
        $notNullOneOfOptionsValidator->addValidator($oneOfOptionsValidator);

        $attributeValidatorOne = new AttributeValidator('attributeOne');
        $attributeValidatorOne->setValidator($notNullStringValidator);

        $attributeValidatorTwo = new AttributeValidator('attributeTwo');
        $attributeValidatorTwo->setValidator($notNullFloatValidator);

        $attributeValidatorThree = new AttributeValidator('attributeThree');
        $attributeValidatorThree->setValidator($notNullOneOfOptionsValidator);

        $sutComposite = new ValidatorComposite();
        $sutComposite->addValidator($attributeValidatorOne);
        $sutComposite->addValidator($attributeValidatorTwo);
        $sutComposite->addValidator($attributeValidatorThree);

        $sut = new AttributeValidator('attribute', $sutComposite);

        $inputAsArrayFirstIsValid = [
            'attribute' => [
                [
                    'attributeOne' => ['test', 1],
                    'attributeTwo' => [1.1, 1],
                    'attributeThree' => ['test', 'test3'],
                ],
                [
                    'attributeOne' => ['test', 1],
                    'attributeTwo' => [1.1, 1],
                    'attributeThree' => ['test', 'test3'],
                ]
            ]
        ];

        $inputAsArrayFirstIsInvalid = [
            'attribute' => [
                [
                    'attributeOne' => [1, 'test'],
                    'attributeTwo' => [1, 1.1],
                    'attributeThree' => ['test3', 'test'],
                ],
                [
                    'attributeOne' => [1, 'test'],
                    'attributeTwo' => [1, 1.1],
                    'attributeThree' => ['test3', 'test'],
                ]
            ]
        ];

        $resultWhenInputAsArrayFirstIsValid = $sut->validate($inputAsArrayFirstIsValid);
        $resultWhenInputAsArrayFirstIsInvalid = $sut->validate($inputAsArrayFirstIsInvalid);
        $this->assertFalse($resultWhenInputAsArrayFirstIsValid);
        $this->assertFalse($resultWhenInputAsArrayFirstIsInvalid);

        $inputObjectFirstIsValid = new stdClass();
        $inputObjectFirstIsValid->attributeOne = ['test', 1];
        $inputObjectFirstIsValid->attributeTwo = [1.1, 1];
        $inputObjectFirstIsValid->attributeThree = ['test', 'test3'];

        $inputObjectFirstIsInvalid = new stdClass();
        $inputObjectFirstIsInvalid->attributeOne = ['test', 1];
        $inputObjectFirstIsInvalid->attributeTwo = [1.1, 1];
        $inputObjectFirstIsInvalid->attributeThree = ['test', 'test3'];

        $attributeObjectFirstIsValid = new stdClass();
        $attributeObjectFirstIsValid->attribute = [$inputObjectFirstIsValid, $inputObjectFirstIsValid];

        $attributeObjectFirstIsInvalid = new stdClass();
        $attributeObjectFirstIsInvalid->attribute = [$inputObjectFirstIsInvalid, $inputObjectFirstIsInvalid];

        $resultWhenInputAsObjectFirstIsValid = $sut->validate($attributeObjectFirstIsValid);
        $resultWhenInputAsObjectFirstIsInvalid = $sut->validate($attributeObjectFirstIsInvalid);
        $this->assertFalse($resultWhenInputAsObjectFirstIsValid);
        $this->assertFalse($resultWhenInputAsObjectFirstIsInvalid);
    }
}
