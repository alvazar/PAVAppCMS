<?php
namespace App\Config;

final class Users
{
    //
    public const USERS = [
        'admin' => [
            'id' => 1,
            'groups' => ['admin']
        ]
    ];

    public const GROUPS = ['admin'];
}
