<?php

namespace Lch\MediaBundle\Model;

interface ImageInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $alt
     * @return self
     */
    public function setAlt($alt);

    /**
     * @return string
     */
    public function getAlt();

    public function getFile();

    public function setFile($file);

    /**
     * @param integer $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return integer
     */
    public function getWidth();

    /**
     * @param integer $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return integer
     */
    public function getHeight();
}