# MediaBundle

## Installation

## Configuration

lch_media:

  types:
    image:
      entity:     'IpcBundle\Entity\Media\Image'
      form:       'IpcBundle\Form\Media\ImageType'
      add_view:   'IpcBundle:back/Media/Image/fragments:add.html.twig'
      extensions: ['jpg', 'jpeg', 'png', 'gif']

    resource:
      entity:     'IpcBundle\Entity\Media\Resource'
      form:       'IpcBundle\Form\Media\ResourceType'
      add_view:   'IpcBundle:back/Media/Resource/fragments:add.html.twig'
      thumbnail_view: 'IpcBundle:back/Media/Resource/fragments:thumbnail.html.twig'
      list_item_view: 'IpcBundle:back/Media/Resource/fragments:list.item.html.twig'
      extensions: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']
      
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