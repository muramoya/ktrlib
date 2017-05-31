<?php
/**
 * PostgresSQL接続設定
 * Date: 2017/05/22
 * @author takuya
 * @version: 1.0
 */

return [
    'host' => env('DATABASE_HOST'),
    'username' => env('DATABASE_USER'),
    'password' => env('DATABASE_PASSWORD'),
    'dbname' => env('DATABASE_DBNAME'),
    'schema' => env('POSTGRES_OPT_SCHEMA', 'public')
];