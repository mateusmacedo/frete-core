<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Validators;

use Frete\Core\Domain\Validators\AttributeValidator;
use Frete\Core\Domain\Validators\FloatValidator;
use Frete\Core\Domain\Validators\NotNullValidator;
use Frete\Core\Domain\Validators\OneOfOptionsValidator;
use Frete\Core\Domain\Validators\StringValidator;
use Frete\Core\Domain\Validators\Validator;
use Frete\Core\Domain\Validators\ValidatorComposite;
use GuzzleHttp\Promise\Is;
use stdClass;
use Tests\TestCase;

final class AttributeValidatorsTest extends TestCase
{
    protected AttributeValidator $sut;

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

    public function createNotNullOneOfOptionsCompositeValidator(array $options): ValidatorComposite
    {
        $notNullValidator = new NotNullValidator();
        $oneOfOptionsValidator = new OneOfOptionsValidator($options);
        $notNullOneOfOptionsValidator = new ValidatorComposite();
        $notNullOneOfOptionsValidator->addValidator($notNullValidator);
        $notNullOneOfOptionsValidator->addValidator($oneOfOptionsValidator);
        return $notNullOneOfOptionsValidator;
    }

    public function createSut(string $attribute): AttributeValidator
    {
        return new AttributeValidator($attribute);
    }

    public function createSutWithValidator(string $attribute, Validator $validator): AttributeValidator
    {
        $sut = $this->createSut($attribute);
        $sut->setValidator($validator);
        return $sut;
    }

    public function basicDataProvider(): array
    {
        $arrStringValue = ['attribute' => 'valid'];
        $arrValueWithString = ['attribute' => ['valid', 'valid2']];

        $arrNullValue = ['attribute' => null];
        $arrValueWithNulls = ['attribute' => [null, null]];


        $arrIntValue = ['attribute' => 1];
        $arrValueWithInts = ['attribute' => [1, 2]];


        $arrFloatValue = ['attribute' => 1.1];
        $arrValueWithFloats = ['attribute' => [1.1, 1.2]];


        $objStringValue = new stdClass();
        $objStringValue->attribute = 'valid';

        $objValueWithStrings = new stdClass();
        $objValueWithStrings->attribute = ['valid', 'valid2'];

        $objNullValue = new stdClass();
        $objNullValue->attribute = null;

        $objValueWithNulls = new stdClass();
        $objValueWithNulls->attribute = [null, null];

        $objIntValue = new stdClass();
        $objIntValue->attribute = 1;

        $objValueWithInts = new stdClass();
        $objValueWithInts->attribute = [1, 2];

        $objFloatValue = new stdClass();
        $objFloatValue->attribute = 1.1;

        $objValueWithFloats = new stdClass();
        $objValueWithFloats->attribute = [1.1, 1.2];

        return [
            'arrStringValue' => $arrStringValue,
            'arrValueWithString' => $arrValueWithString,
            'arrNullValue' => $arrNullValue,
            'arrValueWithNulls' => $arrValueWithNulls,
            'arrIntValue' => $arrIntValue,
            'arrValueWithInts' => $arrValueWithInts,
            'arrFloatValue' => $arrFloatValue,
            'arrValueWithFloats' => $arrValueWithFloats,
            'objStringValue' => $objStringValue,
            'objValueWithStrings' => $objValueWithStrings,
            'objNullValue' => $objNullValue,
            'objValueWithNulls' => $objValueWithNulls,
            'objIntValue' => $objIntValue,
            'objValueWithInts' => $objValueWithInts,
            'objFloatValue' => $objFloatValue,
            'objValueWithFloats' => $objValueWithFloats,
        ];
    }

    public function testAssertNotNullStringCompositeValidator(): void
    {
        $input = $this->basicDataProvider();

        $this->sut = $this->createSutWithValidator('attribute', $this->createNotNullStringCompositeValidator());

        $this->assertTrue($this->sut->validate($input['arrStringValue']));
        $this->assertTrue($this->sut->validate($input['arrValueWithString']));
        $this->assertTrue($this->sut->validate($input['objStringValue']));
        $this->assertTrue($this->sut->validate($input['objValueWithStrings']));

        $this->assertFalse($this->sut->validate($input['arrNullValue']));
        $this->assertFalse($this->sut->validate($input['arrIntValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithInts']));
        $this->assertFalse($this->sut->validate($input['arrFloatValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithFloats']));
        $this->assertFalse($this->sut->validate($input['arrValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['objNullValue']));
        $this->assertFalse($this->sut->validate($input['objIntValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithInts']));
        $this->assertFalse($this->sut->validate($input['objValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['objFloatValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithFloats']));
    }

    public function testAssertNotNullFloatCompositeValidator(): void
    {
        $input = $this->basicDataProvider();

        $this->sut = $this->createSutWithValidator('attribute', $this->createNotNullFloatCompositeValidator());

        $this->assertTrue($this->sut->validate($input['arrFloatValue']));
        $this->assertTrue($this->sut->validate($input['objFloatValue']));
        $this->assertTrue($this->sut->validate($input['arrValueWithFloats']));
        $this->assertTrue($this->sut->validate($input['objValueWithFloats']));

        $this->assertFalse($this->sut->validate($input['arrNullValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['arrIntValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithInts']));
        $this->assertFalse($this->sut->validate($input['arrStringValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithString']));
        $this->assertFalse($this->sut->validate($input['objStringValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithStrings']));
        $this->assertFalse($this->sut->validate($input['objNullValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['objIntValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithInts']));
    }

    public function testAssertNotNullOneOfOptionsCompositeValidator(): void
    {
        $input = $this->basicDataProvider();

        $validator = $this->createNotNullOneOfOptionsCompositeValidator(['valid', 'valid2']);

        $this->sut = $this->createSutWithValidator('attribute', $validator);

        $this->assertTrue($this->sut->validate($input['arrStringValue']));
        $this->assertTrue($this->sut->validate($input['arrValueWithString']));
        $this->assertTrue($this->sut->validate($input['objStringValue']));
        $this->assertTrue($this->sut->validate($input['objValueWithStrings']));

        $this->assertFalse($this->sut->validate($input['arrNullValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['arrIntValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithInts']));
        $this->assertFalse($this->sut->validate($input['arrFloatValue']));
        $this->assertFalse($this->sut->validate($input['arrValueWithFloats']));
        $this->assertFalse($this->sut->validate($input['objNullValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithNulls']));
        $this->assertFalse($this->sut->validate($input['objIntValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithInts']));
        $this->assertFalse($this->sut->validate($input['objFloatValue']));
        $this->assertFalse($this->sut->validate($input['objValueWithFloats']));
    }
}
