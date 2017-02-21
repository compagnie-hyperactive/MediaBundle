<?php

namespace Lch\MediaBundle\Tests;

use Lch\MediaBundle\Model\Image;

class TestImage extends Image
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}