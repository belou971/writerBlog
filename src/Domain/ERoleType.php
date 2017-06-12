<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 12/06/2017
 * Time: 15:53
 */

namespace writerBlog\Domain;

class ERoleType
{
    const ROLE_ADMIN             = "ROLE_ADMIN";
    const ROLE_USER              = "ROLE_USER";
    const ROLE_ALLOWED_TO_SWITCH = "ROLE_ALLOWED_TO_SWITCH";

    static public function exist($a_status)
    {
        $btest = (self::NOT_PUBLISHED === $a_status) ||
                 (self::PUBLISHED === $a_status) ||
                 (self::DISABLED === $a_status);

        return $btest;
    }
}