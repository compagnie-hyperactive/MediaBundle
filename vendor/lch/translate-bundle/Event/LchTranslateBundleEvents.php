<?php

namespace Lch\TranslateBundle\Event;

/**
 * Class LchTranslateBundleEvents
 * @package Lch\TranslateBundle\Event
 */
class LchTranslateBundleEvents
{
    public const QUERYING_TRANSLATED_PARENT = QueryingTranslatedParentEvent::NAME;
    public const GUESSING_TRANSLATED_PARENT_LABEL = GuessingTranslatedParentLabelEvent::NAME;
}
