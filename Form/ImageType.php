<?php

namespace Lch\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    const NAME = 'lch_media_image_type';
    const ROOT_TRANSLATION_PATH = 'lch.media.image';

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
            ->add('file', FileType::class, [
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
