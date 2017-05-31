<?php
/**
 * memcached接続設定
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

return [
    'servers' => [
        'host' => env('MEMCACHED_HOST'),
        'port' => env('MEMCACHED_PORT'),
        'weight' => env('MEMCACHED_WEIGHT', 1)
    ],
    'client' => [
        Memcached::OPT_HASH => Memcached::HASH_MD5,
        Memcached::OPT_PREFIX_KEY => env('MEMCACHED_PREFIX_KEY')
    ],
    'lifetime' => env('MEMCACHED_LIFETIME', 3600),
    'prefix' => env('MEMCACHED_PREFIX')
];