<?php
/**
 * Created by PhpStorm.
 * User: Eve ODIN
 * Date: 16/05/2017
 * Time: 11:06
 */

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
    'host'     => 'majolieplume.blog',
    'port'     => '3306',
    'dbname'   => 'lightcms',
    'user'     => 'lightcms_admin',
    'password' => 'lightMyTutoWeb9',
);