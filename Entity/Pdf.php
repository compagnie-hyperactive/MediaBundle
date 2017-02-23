<?php

namespace Lch\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lch\MediaBundle\Behavior\Storable;

/**
 * Pdf
 *
 * @ORM\Table(name="pdf")
 * @ORM\Entity(repositoryClass="Lch\MediaBundle\Repository\PdfRepository")
 */
class Pdf extends Media
{
    use Storable;

}

