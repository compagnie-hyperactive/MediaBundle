<?php

namespace Lch\MediaBundle\Tests\Model;

use Lch\MediaBundle\Model\Image;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $image = $this->getImage();
        $this->assertNull($image->getName());

        $image->setName('Image title');
        $this->assertSame('Image title', $image->getName());
    }

    public function testAlt()
    {
        $image = $this->getImage();
        $this->assertNull($image->getAlt());

        $image->setAlt('Image alternative text');
        $this->assertSame('Image alternative text', $image->getAlt());
    }

    public function testFile()
    {
        $image = $this->getImage();
        $this->assertNull($image->getFile());

        $image->setFile('File path');
        $this->assertSame('File path', $image->getFile());
    }

    public function testWidth()
    {
        $image = $this->getImage();
        $this->assertNull($image->getWidth());

        $image->setWidth(1024);
        $this->assertSame(1024, $image->getWidth());
    }

    public function testHeight()
    {
        $image = $this->getImage();
        $this->assertNull($image->getHeight());

        $image->setHeight(1024);
        $this->assertSame(1024, $image->getHeight());
    }

    /**
     * @return Image
     */
    protected function getImage()
    {
        return $this->getMockForAbstractClass('Lch\MediaBundle\Model\Image');
    }
}