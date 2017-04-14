<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 10/04/17
 * Time: 15:28
 */

namespace Lch\MediaBundle\Behavior;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lch\MediaBundle\Entity\Tag;

trait MediaTaggable
{
    /**
     * @var ArrayCollection[Tag]
     * @ORM\ManyToMany(targetEntity="Lch\MediaBundle\Entity\Tag", cascade={"persist"}, orphanRemoval=true)
     */
    private $tags;

    /**
     * @return ArrayCollection
     */
    public function getTags(): ArrayCollection
    {
        if(!$this->tags instanceof ArrayCollection) {
            $this->tags = new ArrayCollection();
        }
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     * @return MediaTaggable
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;
        return $this;
    }
}