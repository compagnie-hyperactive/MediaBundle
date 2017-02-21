<?php

namespace Lch\MediaBundle\Tests\Validator\Constraints;


use Lch\MediaBundle\Tests\TestImage;
use Lch\MediaBundle\Validator\Constraints\ImageFormat;
use Lch\MediaBundle\Validator\Constraints\ImageFormatValidator;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\ValidatorException;

class ImageFormatValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testIsValid()
    {
        $validator = $this->configureValidator();

        // Invalid minWidth option
        try{
            $constraint = new ImageFormat(['minWidth'=>'InvalidInteger']);
        } catch(\Exception $e) {
            $this->assertInstanceOf(InvalidOptionsException::class, $e);
        }

        // Invalid maxWidth option
        try{
            $constraint = new ImageFormat(['maxWidth'=>'InvalidInteger']);
        } catch(\Exception $e) {
            $this->assertInstanceOf(InvalidOptionsException::class, $e);
        }

        // Invalid minHeight option
        try{
            $constraint = new ImageFormat(['minHeight'=>'InvalidInteger']);
        } catch(\Exception $e) {
            $this->assertInstanceOf(InvalidOptionsException::class, $e);
        }

        // Invalid maxHeight option
        try{
            $constraint = new ImageFormat(['maxHeight'=>'InvalidInteger']);
        } catch(\Exception $e) {
            $this->assertInstanceOf(InvalidOptionsException::class, $e);
        }

        $constraint = new ImageFormat();

        $this->assertFalse($validator->validate(null, $constraint));

        // Invalid Image
        try{
            $validator->validate('Invalid Image', new ImageFormat());
        } catch(\Exception $e) {
            $this->assertInstanceOf(ValidatorException::class, $e);
        }

        $image = new TestImage();
        $image->setFile('/../../File/Fixtures/symfony.png');
        $image->setWidth(1000);
        $image->setHeight(1000);

        $validator->validate($image, $constraint);

        // Valid Options
        $constraint = new ImageFormat(['minWidth' => 200, 'maxWidth' => 200, 'minHeight' => 200, 'maxHeight'=> 200]);

        // Invalid Minwidth
        $constraint = new ImageFormat(['minWidth' => 2200]);
        $validator = $this->configureValidator('lch.media_bundle.image.width.min_message');
        $validator->validate($image, $constraint);

        // Invalid maxWidth
        $constraint = new ImageFormat(['maxWidth' => 100]);
        $validator = $this->configureValidator('lch.media_bundle.image.width.max_message');
        $validator->validate($image, $constraint);

        // Invalid minHeight
        $constraint = new ImageFormat(['minHeight' => 2200]);
        $validator = $this->configureValidator('lch.media_bundle.image.height.min_message');
        $validator->validate($image, $constraint);

        // Invalid maxHeight
        $constraint = new ImageFormat(['maxHeight' => 200]);
        $validator = $this->configureValidator('lch.media_bundle.image.height.max_message');
        $validator->validate($image, $constraint);

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
        $validator = new ImageFormatValidator();
        $validator->initialize($context);

        // return the SomeConstraintValidator
        return $validator;
    }
}