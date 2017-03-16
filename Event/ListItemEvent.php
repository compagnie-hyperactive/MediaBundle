<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 16:35
 */

namespace Lch\MediaBundle\Event;


use Lch\MediaBundle\Behavior\Mediable;
use Symfony\Component\EventDispatcher\Event;

class ListItemEvent extends Event implements MediaTemplateEventInterface
{
    use Mediable;

    /**
     * @var string
     */
    private $template;

    /**
     * @return string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template) {
        $this->template = $template;
        return $this;
    }
}