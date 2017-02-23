<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 08:10
 */
namespace Lch\MediaBundle\Behavior;

trait Storable
{
    /**
     * @var string $filePath the file absolute path
     *
     * @ORM\Column(name="file_path", type="string", length=512)
     */
    protected $filePath;


    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return Storable
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }
}