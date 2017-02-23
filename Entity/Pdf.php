<?php

namespace Lch\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pdf
 *
 * @ORM\Table(name="pdf")
 * @ORM\Entity(repositoryClass="Lch\MediaBundle\Repository\PdfRepository")
 */
class Pdf extends Media
{

}

