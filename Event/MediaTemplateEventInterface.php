<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 28/02/17
 * Time: 14:02
 */

namespace Lch\MediaBundle\Event;


interface MediaTemplateEventInterface
{
    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template);
}