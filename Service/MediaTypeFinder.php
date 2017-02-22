<?php

/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 21/02/17
 * Time: 18:56
 */
class MediaTypeFinder
{
    public function find() {
        // TODO use reflection to find classes heritating from media
        return 'Lch\MediaBundle\Entity\Image';
    }
}