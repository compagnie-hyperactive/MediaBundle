<?php

namespace Lch\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lch\MediaBundle\Behavior\Storable;

/**
 * Pdf
 *
 * @ORM\MappedSuperclass()
 */
abstract class Pdf extends Media
{
    use Storable;
}