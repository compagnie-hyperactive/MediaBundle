<?php

namespace Lch\MediaBundle\Form;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Lch\MediaBundle\Form\DataTransformer\ImageToNumberTransformer;

class AddImageType extends AbstractType
{
    private $manager;
    private $eventDispatcher;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ImageToNumberTransformer(
            $this->manager,
            $this->eventDispatcher,
            $options['attr']['entity_reference'],
            $options['image_param']
        );
        $builder->addViewTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'media.add',
            'invalid_message' => 'The selected image does not exist',
            'image_param' => [],
        ));
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return 'lch_add_media_image';
    }
}
