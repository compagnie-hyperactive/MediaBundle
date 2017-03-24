<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 23/02/17
 * Time: 13:38
 */

namespace Lch\MediaBundle\Service;


class MediaTools
{
    /**
     * @param $fullPath
     * @return string
     */
    public function getRealRelativeUrl($fullPath) {
        // CHeck path is full, with "web" inside
        if(strpos($fullPath, 'web') !== false) {
            return explode('/web', $fullPath)[1];
        }
        // If not, already relative path
        else {
            return $fullPath;
        }
    }
}