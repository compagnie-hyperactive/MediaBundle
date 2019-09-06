<?php

namespace Lch\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    const NAME = 'lch_media_image_type';
    const ROOT_TRANSLATION_PATH = 'lch.media.image';
    const FILE_TYPE_NAME = 'file';

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => static::ROOT_TRANSLATION_PATH . '.name.label',
                'required' => false,
                'attr' => [
                    'helper' => static::ROOT_TRANSLATION_PATH . '.name.helper',
                ]
            ])
            ->add('alt', TextType::class, [
                'label' => static::ROOT_TRANSLATION_PATH . '.alt.label',
                'required' => false,
                'attr' => [
                    'helper' => static::ROOT_TRANSLATION_PATH . '.alt.helper',
                ]
            ])
            ->add(static::FILE_TYPE_NAME, FileType::class, [
                'label' => static::ROOT_TRANSLATION_PATH . '.file.label',
                'required' => true,
                'attr' => [
                    'helper' => static::ROOT_TRANSLATION_PATH . '.file.helper',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => static::ROOT_TRANSLATION_PATH . '.modal.save.label',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Replace ID with something unique to ensure uniqueness on form with multiple images
        $view->children[static::FILE_TYPE_NAME]->vars['id'] = random_int(0, 100000);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Lch\MediaBundle\Entity\Image'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return static::NAME;
    }
}
