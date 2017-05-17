# MediaBundle

This bundle brings to you a comprehensive and code-close way of handling media for Symfony 3

## Installation and pre-requisites

`composer require lch/media-bundle`

## Configuration and usage

Out of the box, MediaBundle defines 2 types : image and pdf. You can use those types as base for you custom ones.

Below is shown types and available fields :

![Media relations and fields](https://compagnie-hyperactive.github.io/MediaBundle/images/media-relations.png)


1. You need to define your **media types** in `config.yml`. You can define as many type you need, using the following syntax :

```yml  
    lch_media:
      types:
        image:
          name:             'your_project.image.name' # the translated name for front presentation
          entity:           'YourBundle\Entity\Media\Image' # the entity to be used
          form:             'YourBundle\Form\Media\ImageType' # the form to be used when adding media
          add_view:         'YourBundle/Media/Image/fragments:add.html.twig' # the add form view to be used when adding media
          thumbnail_view:   'YourBundle/Media/Resource/fragments:thumbnail.html.twig' # the view used for displaying thumbnail
          list_item_view:   'YourBundle/Media/Resource/fragments:list.item.html.twig' # the view used for displaying list item in selection lists
          extensions: ['jpg', 'jpeg', 'png', 'gif'] # allowed extensions
```

### Entity

Minimal class for above declared image could be :

```php
<?php
    namespace YourBundle\Bundle\Entity\Media;

    use Doctrine\ORM\Mapping as ORM;
    use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
    use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
    use Lch\MediaBundle\Entity\Image as BaseImage;

    /**
     * Image
     *
     * @ORM\Table(name="image")
     * @ORM\Entity(repositoryClass="YourBundle\Repository\Media\ImageRepository")
     */
    class Image extends BaseImage
    {
        use Blameable,
            Timestampable;
    }
```

It extends `Lch\MediaBundle\Entity\Image`. If you want to start from scratch, you have to extends from `Media` and use `Storable` behavior in order to trigger all stuff link to file storage in the bundle.

### Form

Minimal form class could be :


```php
<?php
    namespace YourBundle\Form\Media;

    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Lch\MediaBundle\Form\ImageType as BaseImageType;

    // Extends BaseImageType here for overriding constants
    class ImageType extends BaseImageType
    {
        /**
        * BaseImageType defines a NAME constant for generic image type. You override it here with your type name
        */
        const NAME = 'your_image_type';

        /**
        * Same for root translation path to be used in your particular type case
        */
        const ROOT_TRANSLATION_PATH = 'your.back.media.form.image';

        /**
        *
        */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            parent::buildForm($builder, $options);
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => 'YourBundle\Entity\Media\Image'
            ]);
        }


        public function getParent()
        {
            return BaseImageType::class;
        }
    }
```








Add media form theme
  form_themes:
  # Order is important here
    - 'LchMediaBundle:form:fields.html.twig'    


    resource:
      entity:     'IpcBundle\Entity\Media\Resource'
      form:       'IpcBundle\Form\Media\ResourceType'
      add_view:   'IpcBundle:back/Media/Resource/fragments:add.html.twig'
      thumbnail_view: 'IpcBundle:back/Media/Resource/fragments:thumbnail.html.twig'
      list_item_view: 'IpcBundle:back/Media/Resource/fragments:list.item.html.twig'
      extensions: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
      

Validators
 - Media
  - HasAllowedFileExtension : for extension check based on configuration
  - Weight : min/max, exact weight
 - Image
  - ImageSize : for image size check (width, height, min width, min height)

All validators work both on class and property level. So you need to define them on class once for all

## Download control
 1. Declare media (see above)
 2. Register a Listener/Subscriber to LchMediaEvents::STORAGE to change storage for your media (example : to add a "/private/" subfolder)
 3. Add matching route, to force Symfony to handle request : 
  ipc_media_download:
      path: /uploads/resources/{id}
      defaults: { _controller: LchMediaBundle:Media:download, class: '%lch_media.types.resource.entity%' }
      methods:  [ GET ]
      
 4. Add a Voter to restrain service   
 5. Option : Register a Listener/Subscriber to LchMediaEvents::DOWNLOAD to control what to serve (add watermark...)