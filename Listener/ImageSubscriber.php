<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 16:29
 */

namespace Lch\MediaBundle\Listener;


use Knp\DoctrineBehaviors\Reflection\ClassAnalyzer;
use Lch\MediaBundle\DependencyInjection\Configuration;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Event\ListItemEvent;
use Lch\MediaBundle\Event\PostStorageEvent;
use Lch\MediaBundle\Event\PrePersistEvent;
use Lch\MediaBundle\Event\PreSearchEvent;
use Lch\MediaBundle\Event\ThumbnailEvent;
use Lch\MediaBundle\Event\UrlEvent;
use Lch\MediaBundle\LchMediaEvents;
use Lch\MediaBundle\Manager\ImageManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ImageSubscriber implements EventSubscriberInterface
{

    /**
     * @var ImageManager
     */
    private $imageManager;
    

    /**
     * ImagePrePersistListener constructor.
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager) {
        $this->imageManager = $imageManager;
    }


    public static function getSubscribedEvents() {
        return [
            LchMediaEvents::PRE_PERSIST => 'onImagePrePersist',
            LchMediaEvents::THUMBNAIL => 'onImageThumbnail',
            LchMediaEvents::LIST_ITEM => 'onImageListItem',
            LchMediaEvents::URL => 'onImageUrl',
            LchMediaEvents::POST_STORAGE => 'onImagePostStorage',
            LchMediaEvents::PRE_SEARCH => 'onImagePreSearch'
        ];
    }

    /**
     * @param PrePersistEvent $event
     */
    public function onImagePrePersist(PrePersistEvent $event) {

        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }

        if (null !== $image->getFile()) {

            $imageInfos = getimagesize($image->getFile());

            // TODO add checks, see how to pass constraints to event?
            $image->setWidth($imageInfos[0]);
            $image->setHeight($imageInfos[1]);

            if(empty($image->getName())) {
                $image->setName(pathinfo($image->getFile()->getClientOriginalName(), PATHINFO_BASENAME));
            }
            if(empty($image->getAlt())) {
                $image->setAlt(pathinfo($image->getFile(), PATHINFO_BASENAME));
            }

            $fileName = $this->imageManager->getMediaManager()->upload($image);
            $image->setFile($fileName);

            $event->setMedia($image);
            $event->setData([
                'name'      => $image->getName(),
                'url'       => $this->imageManager->getMediaManager()->getRealRelativeUrl($image->getFile()),
                // TODO find a way to trigger thumbnail generation from here
//                'thumbnail' => '<img width="50" src="' . $this->getRelativeUrl($image->getFile()) . '" />'
            ]);
        }
    }

    /**
     * @param ThumbnailEvent $event
     */
    public function onImageThumbnail(ThumbnailEvent $event) {
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }
        // TODO elaborate
        $event->setThumbnailPath($this->imageManager->getThumbnailUrl($image, 'list_thumbnail'));
    }

    /**
     * @param ListItemEvent $event
     */
    public function onImageListItem(ListItemEvent $event) {
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }
    }

    /**
     * @param UrlEvent $event
     */
    public function onImageUrl(UrlEvent $event)
    {
        $image = $event->getMedia();

        // Only for images
        if (!$image instanceof Image) {
            return;
        }

        $mediaParameters = $event->getMediaParameters();


        // Specific size is asked
        if (isset($mediaParameters[Image::SIZE])) {
            if ("" !== ($thumbnailUrl = $this->imageManager->getThumbnailUrl($image, $mediaParameters[Image::SIZE]))) {
                $event->setUrl($thumbnailUrl);
            }
        }
    }

    /**
     * @param PostStorageEvent $event
     */
    public function onImagePostStorage(PostStorageEvent $event) {
        $image = $event->getMedia();

        // Only for images
        if(!$image instanceof Image) {
            return;
        }

        $this->imageManager->generateThumbnails($image);
    }

    /**
     * @param PreSearchEvent $event
     */
    public function onImagePreSearch(PreSearchEvent $event) {
        $mediaType = $event->getMediaType();

        // Only for Image based classed
        if(!is_subclass_of($mediaType[Configuration::ENTITY], Image::class)) {
            return;
        }

        $resourceQueryBuilder = $event->getQueryBuilder();

        // Search on common media properties
        if(isset($event->getParameters()[Media::NAME])) {
            $resourceQueryBuilder
                ->leftJoin("{$event->getAlias()}.tags", 't')
                ->orWhere($resourceQueryBuilder->expr()->like("t.name", ':name'))
                ->setParameter('name', "%{$event->getParameters()[Media::NAME]}%")
            ;
        }
    }
}