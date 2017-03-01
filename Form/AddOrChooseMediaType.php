<?php

namespace Lch\MediaBundle\Form;

use Lch\MediaBundle\DependencyInjection\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Lch\MediaBundle\Form\DataTransformer\MediaToNumberTransformer;

class AddOrChooseMediaType extends AbstractType
{
    const NAME = 'lch_add_choose_media';

    const HELPER = "helper";
    /**
     * The route for adding media to collection
     */
    const ADD_MEDIA_ROUTE = "add_media_route";

    /**
     * The route for listing media
     */
    const LIST_MEDIA_ROUTE = "list_media_route";

    /**
     * The entity reference for listing one media type
     */
    const ENTITY_REFERENCE = 'entity_reference';

    /**
     * other media parameters
     */
    const MEDIA_PARAMETERS = 'media_parameters';

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array $types media types registered
     */
    private $registeredMediaTypes;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, array $registeredMediaTypes)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->registeredMediaTypes = $registeredMediaTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO add check on required options and related exceptions
        $transformer = new MediaToNumberTransformer(
            $this->manager,
            $this->eventDispatcher,
            $options[self::ENTITY_REFERENCE],
            $options[self::MEDIA_PARAMETERS]
        );
        $builder->addViewTransformer($transformer);

    }


    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {

        // Media listing
        $view->vars[self::LIST_MEDIA_ROUTE] = $options[self::LIST_MEDIA_ROUTE];
        // Media addition
        $view->vars[self::ADD_MEDIA_ROUTE] = $options[self::ADD_MEDIA_ROUTE];

        // Media type
        foreach($this->registeredMediaTypes as $mediaSlug => $registeredMediaType) {
            if($registeredMediaType[Configuration::ENTITY] === $options[self::ENTITY_REFERENCE]) {
                $view->vars['media_type'] = $mediaSlug;
            }
        }
        if(!isset($view->vars['media_type'])) {
            // TODO throw exception, media type not found
        }

        $view->vars['modal_title'] = $options['modal_title'];

        // Media helper
        $view->vars[self::HELPER] = $options[self::HELPER];
        // Media parameters
        $view->vars[self::MEDIA_PARAMETERS] = $options[self::MEDIA_PARAMETERS];

        parent::finishView($view, $form, $options);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            self::ENTITY_REFERENCE => '',
            'label' => 'lch.media.form.add',
            'modal_title' => 'lch.media.form.modal.title',
            self::ADD_MEDIA_ROUTE => 'lch_media_add',
            self::LIST_MEDIA_ROUTE => 'lch_media_list',
            'invalid_message' => 'The selected image does not exist',
            self::MEDIA_PARAMETERS => [],
            self::HELPER => 'lch.media.helper'
        ));
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return self::NAME;
    }
}
