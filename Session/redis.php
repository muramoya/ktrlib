<?php
/**
 * Redis接続設定
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

return [
    'uniqueId' => env('REDIS_UNIQUE_ID'),
    'host' => env('REDIS_HOST'),
    'port' => env('REDIS_PORT'),
    'auth' => env('REDIS_AUTH'),
    'persistent' => env('REDIS_IS_PERSISTENT', false),
    'lifetime' => env('REDIS_LIFETIME', 3600),
    'prefix' => env('REDIS_PREFIX'),
    'index' => env('REDIS_INDEX',1),
];