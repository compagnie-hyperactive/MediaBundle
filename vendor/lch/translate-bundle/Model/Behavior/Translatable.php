<?php

namespace Lch\TranslateBundle\Model\Behavior;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Trait Translatable
 * @package Lch\TranslateBundle\Model\Behavior
 */
trait Translatable
{
    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Groups({"lch_translate"})
     */
    protected $language;

    /**
     * @var object $translatedParent
     */
    protected $translatedParent;

    /**
     * @var Collection $translatedChildren
     */
    protected $translatedChildren;

    public function __construct()
    {
        $this->translatedChildren = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string|null $language
     *
     * @return self
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getTranslatedChildren(): ?Collection
    {
        return $this->translatedChildren;
    }

    /**
     * @param Collection|null $translatedChildren
     *
     * @return self
     */
    public function setTranslatedChildren(?Collection $translatedChildren): self
    {
        $this->translatedChildren = $translatedChildren;

        return $this;
    }

    /**
     * @param self $translatedChild
     *
     * @return self
     */
    public function addTranslatedChild(self $translatedChild): self
    {
        $this->translatedChildren->add($translatedChild);
        $translatedChild->setTranslatedParent($this);

        return $this;
    }

    /**
     * @return self|null
     */
    public function getTranslatedParent(): ?self
    {
        return $this->translatedParent;
    }

    /**
     * @param self $translatedParent
     *
     * @return self
     */
    public function setTranslatedParent(?self $translatedParent): self
    {
        $this->translatedParent = $translatedParent;

        return $this;
    }
}