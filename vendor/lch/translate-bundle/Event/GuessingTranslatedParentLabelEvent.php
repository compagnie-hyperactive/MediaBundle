<?php

namespace Lch\TranslateBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GuessingTranslatedParentLabelEvent
 * @package Lch\TranslateBundle\Event
 */
class GuessingTranslatedParentLabelEvent extends Event
{
    public const NAME = 'translated_parent.guessing_label';

    /** @var string $label */
    protected $label;

    /** @var object $entity */
    protected $entity;

    /**
     * GuessingTranslatedParentLabelEvent constructor.
     * @param string $label
     * @param object $entity
     */
    public function __construct(string &$label, object $entity)
    {
        $this->label =& $label;
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return GuessingTranslatedParentLabelEvent
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return object
     */
    public function getEntity(): object
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return get_class($this->entity);
    }
}
