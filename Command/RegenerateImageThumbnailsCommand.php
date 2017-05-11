<?php

namespace Lch\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Lch\MediaBundle\Entity\Image;
use Lch\MediaBundle\Manager\ImageManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lch\MediaBundle\DependencyInjection\Configuration;

class RegenerateImageThumbnailsCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var array $mediaTypes
     */
    private $mediaTypes;

    public function __construct(EntityManager $entityManager, ImageManager $imageManager, array $mediaTypes)
    {
        $this->entityManager = $entityManager;
        $this->imageManager = $imageManager;
        $this->mediaTypes = $mediaTypes;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lch:media:image:regenerate_thumbnails')
            ->setDescription('This command allow you to regenerate all thumbnails for all images types in your project');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Loop on all media types
        foreach($this->mediaTypes as $mediaSlug => $mediaTypeConfiguration) {
            $output->write("Check {$mediaSlug}... ");
            // If the media type get thumbnail sizes
            if(isset($mediaTypeConfiguration[Configuration::THUMBNAIL_SIZES]) && count($mediaTypeConfiguration[Configuration::THUMBNAIL_SIZES]) > 0) {

                // Check media extends image
                $mediaReflection = new \ReflectionClass($mediaTypeConfiguration[Configuration::ENTITY]);
                if($mediaReflection->getParentClass()->getName() != Image::class) {
                    $output->writeln("<error>The media class {$mediaTypeConfiguration[Configuration::ENTITY]} does not extends Image class and therefore cannot have thumbnails generation</error>");
                }

                $output->write("<info>" . count($mediaTypeConfiguration[Configuration::THUMBNAIL_SIZES]) . " thumbnails sizes found.</info>" . PHP_EOL);

                // Get all media
                $medias = $this->entityManager->getRepository($mediaTypeConfiguration[Configuration::ENTITY])->findAll();
                $output->writeln(" - " . count($medias) . " medias found.");
                foreach($medias as $media) {
                    $output->writeln("<fg=black;bg=yellow>   - #{$media->getId()} {$media->getName()}</>");
                    foreach($mediaTypeConfiguration[Configuration::THUMBNAIL_SIZES] as $sizeSlug => $size) {
                        $output->write("     - Generate \"{$sizeSlug}\" thumbnail and saving it at " . $this->imageManager->getThumbnailUrl($media, $sizeSlug) . "... ");
                        $this->imageManager->generateThumbnails($media);
                        $output->writeln("<info>DONE</info>");
                    }
                }
            } else {
                $output->write("<comment>no thumbnail sizes set.</comment>" . PHP_EOL);
            }
        }
    }
}
