<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 21/02/17
 * Time: 17:39
 */

namespace Lch\MediaBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

interface MediaControllerInterface
{
    public function addAction();
    public function editAction();
    public function removeAction();
    public function listAction(Request $request);
}