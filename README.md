# MediaBundle

This bundle brings to you a comprehensive and code-close way of handling media for Symfony 3.
Features:
- Defines [media types](#media-types-declaration) in `config.yml`
- Provides [events](#events) to hook on all process (CRUD)
- Provide [form types](#form-types) to interact easily in you admin (pick an existing media/creates a new one)
- Provides Twig extension to ease media display and manipulation in Twig files (get URLs, thumbnails...)
- Provides [validators](#validators) to restrain media to boundaries (size, resolution for images, extensions...)

## Installation and pre-requisites

`composer require lch/media-bundle`

## Configuration and usage

1. [General explanations](#general-explanations)
2. [Media types declaration](#media-types-declaration)
3. [Twig extensions and tools](#twig-extension--tools)
4. [Events](#events)
5. [Form types](#form-types)
6. [Validators](#validators)
7. [Image sizes](#image-sizes)
8. [Practical use cases](#practical-use-cases)
    1. [Download control](#download-control)

### General explanations

Out of the box, MediaBundle defines 2 types : **image** and **pdf**. You can use those types as base for you custom ones.

Below is shown types and available fields :

![Media relations and fields](https://compagnie-hyperactive.github.io/MediaBundle/images/media-relations.png)


### Media types declaration

You need to define your **media types** in `config.yml`. You can define as many types as you need, using the following syntax :

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
          extensions:       ['jpg', 'jpeg', 'png', 'gif'] # allowed extensions
```

Let's review an example for each given key :

#### Entity

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

#### Form

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
        * @inheritdoc
        */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            // Explicit parent call is required for constant overriding
            parent::buildForm($builder, $options);
        }

        /**
        * @inheritdoc
        */
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => 'YourBundle\Entity\Media\Image'
            ]);
        }

        /**
        * @inheritdoc
        */
        public function getParent()
        {
            return BaseImageType::class;
        }
    }
```

#### Add view

You can find below the add view for generic Image defined by the bundle :

```twig
    {% form_theme mediaForm 'bootstrap_3_layout.html.twig' %}

    {{ form_errors(mediaForm) }}
    {{ form_start(mediaForm) }}
        {{ form_row(mediaForm.file) }}
        {{ form_row(mediaForm.name) }}
        {{ form_row(mediaForm.alt) }}
        <div class="text-right col-xs-12">
            {{ form_row(mediaForm.submit) }}
        </div>
        {{ form_rest(mediaForm) }}
    {{ form_end(mediaForm) }}
```

If you define your own, you have to use `mediaForm` as the form variable.

#### Thumbnail view

You can find below the thumbnail view for generic Image defined by the bundle :

```twig
<img src="{{ thumbnailEvent.thumbnailPath }}" alt="{{ thumbnailEvent.media.alt }}" />
```

_Note : as indicated [below](#events), most of logic is **event related**. Thumbnail generation is one those things, so access to thumbnail data goes through event object_

#### List item view

You can find below the list item view for generic Image defined by the bundle. You will find further explanations on [twig methods below](#twig-extension--tools)

```twig
{% set attrs = "" %}
{% for key, attr in attributes %}
    {% set attrs = attrs ~ " " ~ key ~ '=' ~ attr %}
{% endfor %}
{% set size = listItemEvent.media.file.getSize()/1000 %}

<div {{ attrs }} class="{% if attributes.fullSize is defined and attributes.fullSize == true %}col-xs-12{% else %}col-xs-6 col-sm-4 col-md-3{% endif %} media"
     data-id="{{ listItemEvent.media.id }}"
     data-type="{{ getClass(listItemEvent.media) }}"
     data-url="{{ getUrl(listItemEvent.media) }}"
     data-name="{{ listItemEvent.media.name }}"
     data-width="{{ listItemEvent.media.width }}"
     data-height="{{ listItemEvent.media.height }}"
     data-size="{{ size }}"
>
    <div class="col-xs-4">
        {{ getThumbnail(listItemEvent.media) }}
    </div>
    <div class="col-xs-8">
        <p>{{ listItemEvent.media.name }}</p>
        <p><strong>{{ listItemEvent.media.width }}</strong>px x <strong>{{ listItemEvent.media.height }}</strong>px</p>
        <p><strong>{{ size }} Ko</strong></p>
    </div>
</div>
```

_Note : as indicated [below](#events), most of logic is **event related**. Thumbnail generation is one those things, so access to thumbnail data goes through event object_

### Twig extension & tools

- `{{ getListItem(media, attributes) }}` : display a list item
- `{{ getThumbnail(media, attributes) }}` : display the media thumbnail (called in getListItem)
- `{{ getThumbnailUrl(media, attributes) }}` : return the thumbnail URL only (where getThumbnail will return an HTML <img> tag)
- `{{ getUrl(media, attributes) }}` : returns the URL for the given media. direct media URL by default, but you can easily hook on DOWNLOAD event to return something more complicated (such as downloader for private resources)


You can find below a graphical render for several methods listed above :

![Media Twig extension methods](https://compagnie-hyperactive.github.io/MediaBundle/images/extension.png)

### Events

You can find below the complete event list thrown by the bundle (listed in `Lch\Media\LchMediaEvents`) :
 - `LchMediaEvents::DOWNLOAD` : fired by `MediaController` **before** preparing response to deliver file, but **after** security check
 - `LchMediaEvents::LIST_ITEM` : fired by `MediaManager` when Twig method `{{ getListItem(media, attributes) }}` is called
 - `LchMediaEvents::PRE_DELETE` : fired by `MediaController` **before** media deletion
 - `LchMediaEvents::PRE_PERSIST` : fired by `MediaController` **before** media persistance
 - `LchMediaEvents::PRE_SEARCH` : fired by `MediaManager` on every list calls
 - `LchMediaEvents::POST_DELETE` : fired by `MediaController` **after** media deletion
 - `LchMediaEvents::POST_PERSIST` : fired by `MediaController` **after** media persists
 - `LchMediaEvents::POST_SEARCH` : fired by `MediaManager` on every list calls
 - `LchMediaEvents::REVERSE_TRANSFORM` : used by `AddOrChooseMediaType` form type
 - `LchMediaEvents::SEARCH_FORM` : fired by `MediaManager` on list call to get specific input type in order to contextualize search form to media type
 - `LchMediaEvents::PRE_STORAGE` : fired by `MediaController` **before** media file storage
 - `LchMediaEvents::POST_STORAGE` : fired by `MediaController` **after** media file storage
 - `LchMediaEvents::THUMBNAIL` : fired by `MediaManager` when Twig method `{{ getThumbnail(media, attributes) }}` is called
 - `LchMediaEvents::TRANSFORM` : used by `AddOrChooseMediaType` form type
 - `LchMediaEvents::URL` : fired by `MediaManager` when Twig method `{{ getUrl(media, attributes) }}` is called


### Form types
The bundle provides 2 form types for easing media selection/creation. First of all, be assured to add the correct form theme file in your admin twig files, which is `LchMediaBundle:form:fields.html.twig`


#### AddOrChooseMediaType

Here is a classical use :

```php
    $builder
        ->add('myImage', AddOrChooseMediaType::class, [
                'entity_reference' => Image::class,
                'label' => static::ROOT_TRANSLATION_PATH . ".my_image.label",
                'modal_title' => static::ROOT_TRANSLATION_PATH . '.my_image.modal.title',
                'image_width' => 1499,
                'image_height' => 415,
                'required' => false,
                'attr'  => [
                    'helper' => static::ROOT_TRANSLATION_PATH . '.my_image.helper'
                ]
            ])
        ;
```

Although it's quite clear, note that `entity_reference` is the media class you want to link here

#### AddOrChooseMultipleMediasType



    resource:
      entity:     'IpcBundle\Entity\Media\Resource'
      form:       'IpcBundle\Form\Media\ResourceType'
      add_view:   'IpcBundle:back/Media/Resource/fragments:add.html.twig'
      thumbnail_view: 'IpcBundle:back/Media/Resource/fragments:thumbnail.html.twig'
      list_item_view: 'IpcBundle:back/Media/Resource/fragments:list.item.html.twig'
      extensions: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
      

### Validators
 - Media
  - HasAllowedFileExtension : for extension check based on configuration
  - Weight : min/max, exact weight
 - Image
  - ImageSize : for image size check (width, height, min width, min height)

All validators work both on class and property level. So you need to define them on class once for all


### Image sizes

### Practical use cases

#### Download control
 1. Declare media (see above)
 2. Register a Listener/Subscriber to LchMediaEvents::STORAGE to change storage for your media (example : to add a "/private/" subfolder)
 3. Add matching route, to force Symfony to handle request : 
  ipc_media_download:
      path: /uploads/resources/{id}
      defaults: { _controller: LchMediaBundle:Media:download, class: '%lch_media.types.resource.entity%' }
      methods:  [ GET ]
      
 4. Add a Voter to restrain service   
 5. Option : Register a Listener/Subscriber to LchMediaEvents::DOWNLOAD to control what to serve (add watermark...)