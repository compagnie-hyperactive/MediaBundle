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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'lch.media_bundle.image.name',
                'required' => false,
            ])
            ->add('alt', TextType::class, [
                'label' => 'lch.media_bundle.image.alt',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'label' => 'lch.media_bundle.image.file',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'lch.media_bundle.image.modal.save',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'modal_title' => '',
        ]);
    }

    public function getName()
    {
        return 'lchmedia_bundle_image_type';
    }

    public function getBlockPrefix()
    {
        return 'lch_media_bundle_image';
    }
}
