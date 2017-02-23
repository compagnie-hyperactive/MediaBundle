<?php

namespace Lch\MediaBundle\Tests\Validator\Constraints;

use Lch\MediaBundle\Tests\TestImage;
use Lch\MediaBundle\Validator\Constraints\ImageExtension;
use Lch\MediaBundle\Validator\Constraints\MediaFileExtensionValidator;
use Symfony\Component\Validator\Exception\InvalidOptionsException;


class ImageExtensionValidatorTest extends \PHPUnit_Framework_TestCase
{


    public function testIsValid()
    {
        $validator = $this->configureValidator('Le fichier doit être au format toto');

        $image = new TestImage();
        $image->setFile('/../../File/Fixtures/symfony.png');
        $this->assertTrue($validator->validate($image, new ImageExtension()));
        $this->assertTrue($validator->validate($image, new ImageExtension(['extensions'=>['png']])));
        try{
            $validator->validate($image, new ImageExtension(['extensions'=>'png']));
        } catch(\Exception $e) {
            $this->assertInstanceOf(InvalidOptionsException::class, $e);
        }

        $validator->validate($image, new ImageExtension(['extensions'=>['toto']]));

        $validator->validate(null, new ImageExtension(['extensions'=>['toto']]));
    }

    public function configureValidator($expectedMessage = null)
    {
        // mock the violation builder
        $builder = $this->getMockBuilder('Symfony\Component\Validator\Violation\ConstraintViolationBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('addViolation'))
            ->getMock()
        ;

        // mock the validator context
        $context = $this->getMockBuilder('Symfony\Component\Validator\Context\ExecutionContext')
            ->disableOriginalConstructor()
            ->setMethods(array('buildViolation'))
            ->getMock()
        ;

        if ($expectedMessage) {
            $builder->expects($this->once())
                ->method('addViolation')
            ;

            $context->expects($this->once())
                ->method('buildViolation')
                ->with($this->equalTo($expectedMessage))
                ->will($this->returnValue($builder))
            ;
        } else {
            $context->expects($this->never())
                ->method('buildViolation')
            ;
        }

        // initialize the validator with the mocked context
        $validator = new MediaFileExtensionValidator(__DIR__);
        $validator->initialize($context);

        // return the SomeConstraintValidator
        return $validator;
    }
}