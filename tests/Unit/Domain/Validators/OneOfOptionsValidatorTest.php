<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Validators;

use Frete\Core\Domain\Validators\OneOfOptionsValidator;
use Tests\TestCase;

class OneOfOptionsValidatorTest extends TestCase
{
    public function testShouldValidateOneOfOptions()
    {
        $validator = new OneOfOptionsValidator();
        $options = array('option', 'select', 'checkbox', 'textinput', 'label');
        $validator->setValidOptions($options);
        $this->assertTrue($validator->validate('option'));
    }

    public function testShouldNotValidateOneOfOptions()
    {
        $validator = new OneOfOptionsValidator();
        $options = array('option', 'select', 'checkbox', 'textinput', 'label');
        $validator->setValidOptions($options);
        $this->assertFalse($validator->validate($options, 'textarea'));
        $this->assertEquals('Invalid option', $validator->getErrorMessage());
    }
}
