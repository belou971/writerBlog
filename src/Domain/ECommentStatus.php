<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 03/06/2017
 * Time: 00:36
 */

namespace writerBlog\Domain;

use writerBlog\Domain\EPostStatus;

class ECommentStatus extends EPostStatus
{
    const MALICIOUS     = "malicious";

    static public function exist($a_status)
    {
        $btest = EPostStatus::exist($a_status)|| (self::MALICIOUS === $a_status);

        return $btest;
    }
}
