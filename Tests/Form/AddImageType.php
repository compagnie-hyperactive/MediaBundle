<?php

namespace Lch\MediaBundle\Tests\Form;

use Lch\MediaBundle\Form\AddOrChooseMediaType;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class AddImageTypeTest extends TypeTestCase
{
    private $entityManager;
    private $eventDispatcher;

    protected function setUp()
    {
        // mock any dependencies
        $this->entityManager = $this->createMock(ObjectManager::class);
        $this->eventDispatcher = $this->createMock(EventDispatcher::class);

        parent::setUp();
    }

    protected function getExtensions()
    {
        // create a type instance with the mocked dependencies
        $type = new AddOrChooseMediaType($this->entityManager, $this->eventDispatcher);

        return array(
            new PreloadedExtension(array(
                $type
            ), array()),
        );
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(AddOrChooseMediaType::class);
    }
}