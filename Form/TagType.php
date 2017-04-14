<?php

namespace Lch\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    const NAME = 'lch.media.tag';
    const ROOT_TRANSLATION_PATH = 'lch.media.tag.form.fields';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => static::ROOT_TRANSLATION_PATH . '.name.label',
                'attr' => [
                    'helper' => static::ROOT_TRANSLATION_PATH . '.name.helper',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => 'Lch\MediaBundle\Entity\Tag'
        ]);
    }

    public function getName()
    {
        return static::NAME;
    }
}
