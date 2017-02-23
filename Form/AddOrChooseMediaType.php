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
            $options['entity_reference'],
            $options['media_parameters']
        );
        $builder->addViewTransformer($transformer);

    }


    /**
     * @inheritdoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {

        // Media listing
        $view->vars['list_media_route'] = $options['list_media_route'];
        // Media addition
        $view->vars['add_media_route'] = $options['add_media_route'];

        // Media type
        foreach($this->registeredMediaTypes as $mediaSlug => $registeredMediaType) {
            if($registeredMediaType[Configuration::ENTITY] === $options['entity_reference']) {
                $view->vars['media_type'] = $mediaSlug;
            }
        }
        if(!isset($view->vars['media_type'])) {
            // TODO throw exception, media type not found
        }
        $view->vars['modal_title'] = $options['modal_title'];

        parent::finishView($view, $form, $options);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'entity_reference' => '',
            'label' => 'lch.media.form.add',
            'modal_title' => 'lch.media.form.modal.title',
            'add_media_route' => 'lch_media_add',
            'list_media_route' => 'lch_media_list',
            'invalid_message' => 'The selected image does not exist',
            'media_parameters' => [],
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
