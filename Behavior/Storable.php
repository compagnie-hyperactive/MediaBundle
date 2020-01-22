<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:10
 */
namespace Lch\MediaBundle\Behavior;

use Symfony\Component\HttpFoundation\File\File;

trait Storable
{
    // TODO : improve to automatic serve File when requested, instead of path
    /**
     * @var File $file the file absolute path
     * @ORM\Column(name="file_path", type="string", length=512)
     */
    protected $file;

    /**
     * @var bool
     */
    protected $fallBack = false;

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @param bool $fallBack
     *
     * @return $this
     */
    public function setFallBack(bool $fallBack): self
    {
        $this->fallBack = $fallBack;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFallBack(): bool
    {
        return $this->fallBack;
    }

    /**
     * @return bool
     */
    public function isFallBack(): bool
    {
        return $this->getFallBack();
    }
}
