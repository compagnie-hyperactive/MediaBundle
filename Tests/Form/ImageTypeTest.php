<?php

namespace Lch\MediaBundle\Tests\Form;

use Lch\MediaBundle\Form\ImageType;
use Lch\MediaBundle\Model\Image;
use Lch\MediaBundle\Tests\TestImage;
use Symfony\Component\Form\Test\TypeTestCase;

class ImageTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $image = new TestImage();

        $form = $this->factory->create(ImageType::class, $image);
        $formData = [
            'name' => 'File Name',
            'alt' => 'File Alt',
        ];

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

    }

    /**
     * @return Image
     */
    protected function getImage()
    {
        return $this->getMockForAbstractClass('Lch\MediaBundle\Model\Image');
    }

}