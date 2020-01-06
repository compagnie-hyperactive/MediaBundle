<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 03/04/17
 * Time: 12:26
 */

namespace Lch\ComponentsBundle\Twig\Extension;


class LchComponentsToolsExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'class' => new \Twig_SimpleFunction('getClass', array($this, 'getClass'))
        );
    }

    public function getName()
    {
        return 'lch.components.tools.twig.extension';
    }

    public function getClass($object)
    {
        return (get_class($object));
    }
}