<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use Exception;
use Frete\Core\Application\{ActionFactory, IActionFactory};
use Frete\Core\Application\Action;
use Tests\TestCase;
use Tests\Unit\Application\Stubs\ActionsEnumStub;
use Tests\Unit\Application\Stubs\ActionStub;

class ActionFactoryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testConstructorSuccessWithCorrectlyData()
    {
        $actionFactory = $this->instanceSuccess();
        $this->assertInstanceOf(IActionFactory::class, $actionFactory);
    }

    public function testConstructorErrorWithIncorrectlyData()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('an enum instance is expected for the action record.');
        new ActionFactory(stdClass::class);
    }

    public function testCheckExistsAction()
    {
        $actionFactory = $this->instanceSuccess();
        $this->assertEquals(true, $actionFactory->exists('stubed'));
        $this->assertEquals(false, $actionFactory->exists('NOT stubed'));
    }

    public function testCreateActionSuccessWithExistsAction()
    {
        $actionFactory = $this->instanceSuccess();
        $action = $actionFactory->create('stubed');
        $this->assertInstanceOf(ActionStub::class, $action);
        $this->assertInstanceOf(Action::class, $action);
    }

    public function testCreateActionErrorWithNotExistsAction()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('there is no NOT stubed action on the enum');
        $actionFactory = $this->instanceSuccess();
        $actionFactory->create('NOT stubed');
    }

    private function instanceSuccess()
    {
        return new ActionFactory(ActionsEnumStub::class);
    }
}
