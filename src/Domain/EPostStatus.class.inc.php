<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 11/05/2017
 * Time: 16:23
 */

namespace writerBlog\Domain;

class EPostStatus
{
    const NOT_PUBLISHED = 0;
    const PUBLISHED     = 1;
    const DISABLED      = 3;

    static public function exist($a_status)
    {
        $btest = (self::NOT_PUBLISHED === $a_status) ||
                 (self::PUBLISHED === $a_status) ||
                 (self::DISABLED === $a_status);

        return $btest;
    }
}