<?php

namespace Lch\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Lch\MediaBundle\Entity\Media;
use Lch\MediaBundle\Manager\PdfManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lch\MediaBundle\DependencyInjection\Configuration;

class RegeneratePdfThumbnailsCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var PdfManager
     */
    private $pdfManager;

    /**
     * @var array $mediaTypes
     */
    private $mediaTypes;

    public function __construct(EntityManager $entityManager, PdfManager $pdfManager, array $mediaTypes)
    {
        $this->entityManager = $entityManager;
        $this->pdfManager = $pdfManager;
        $this->mediaTypes = $mediaTypes;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('lch:media:pdf:regenerate_thumbnails')
            ->setDescription('This command allow you to regenerate all thumbnails for all PDF in your project');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//         Loop on all media types
        foreach($this->mediaTypes as $mediaSlug => $mediaTypeConfiguration) {
            $output->write("Check {$mediaSlug}... ");
            // If the media type get pdf inside
            if(in_array('pdf', $mediaTypeConfiguration[Configuration::EXTENSIONS])) {

                // Check media extends image
                $mediaReflection = new \ReflectionClass($mediaTypeConfiguration[Configuration::ENTITY]);
                if($mediaReflection->getParentClass()->getName() != Media::class) {
                    $output->writeln("<error>The media class {$mediaTypeConfiguration[Configuration::ENTITY]} does not extends Image class and therefore cannot have thumbnails generation</error>");
                }

                $output->write("<info>PDF extensions is allowed.</info>" . PHP_EOL);

                // Get all media
                $medias = $this->entityManager->getRepository($mediaTypeConfiguration[Configuration::ENTITY])->findAll();
                $output->writeln(" - " . count($medias) . " medias found.");
                foreach($medias as $media) {
                    $output->writeln("<fg=black;bg=yellow>   - #{$media->getId()} {$media->getName()} </>");

                    if($media->getFile()->getExtension() === 'pdf') {
                        $output->write("<info>    - PDF file. Generating thumbnail...</info>");
                        $this->pdfManager->generateThumbnail($media);
                        $output->write("<info>DONE</info>" . PHP_EOL);
                    } else {
                        $output->write("    - Not a PDF file." . PHP_EOL);
                    }
                }
            } else {
                $output->write("<comment>no PDF extension found.</comment>" . PHP_EOL);
            }
        }
    }
}
