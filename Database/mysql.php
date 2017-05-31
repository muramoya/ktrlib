<?php
/**
 * Mysql接続設定
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

return [
    'host' => env('DATABASE_HOST'),
    'port' => env('DATABASE_PORT'),
    'username' => env('DATABASE_USER'),
    'password' => env('DATABASE_PASSWORD'),
    'dbname' => env('DATABASE_DBNAME'),
    'persistent' => env('MYSQL_OPT_IS_PERSISTENT', false)
];