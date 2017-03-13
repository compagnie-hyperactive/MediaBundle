<?php

namespace Lch\MediaBundle\Form;

use Lch\MediaBundle\Form\DataTransformer\MediaCollectionToArrayNumberTransformer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AddOrChooseMultipleMediasType extends AbstractType
{
    const NAME = 'lch_add_choose_multiple_medias';
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array $types media types registered
     */
    protected $registeredMediaTypes;

    /**
     * AddOrChooseMediaType constructor.
     * @param ObjectManager $manager
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $registeredMediaTypes
     */
    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, array $registeredMediaTypes)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registeredMediaTypes = $registeredMediaTypes;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO add check on required options and related exceptions
        $transformer = new MediaCollectionToArrayNumberTransformer(
            $this->manager,
            $this->eventDispatcher,
            $options['entry_options'][AddOrChooseMediaType::ENTITY_REFERENCE],
            (isset($options['entry_options'][AddOrChooseMediaType::MEDIA_PARAMETERS]) ? $options['entry_options'][AddOrChooseMediaType::MEDIA_PARAMETERS]  : [])
        );
        $builder->addViewTransformer($transformer);

    }


    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return static::NAME;
    }
}
