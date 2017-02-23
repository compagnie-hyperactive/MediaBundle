<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:10
 */
namespace Lch\MediaBundle\Behavior;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait Storable
{
    /**
     * @var UploadedFile $file the file absolute path
     * @ORM\Column(name="file_path", type="string", length=512)
     */
    protected $file;


    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}