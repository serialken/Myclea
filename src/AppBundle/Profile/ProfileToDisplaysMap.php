<?php

namespace AppBundle\Profile;

use AppBundle\Profile\Exception\ProfileNumberNotFound;

final class ProfileToDisplaysMap
{
    private static $map = [
        1 => [
            'Profil1a',
            'Profil2a',
            'Profil2b',
            'Profil2c',
        ],
        2 => [
            'Profil1a',
            'Profil2a',
            'Profil2c',
        ],
        3 => [
            'Profil1a',
            'Profil2a',
            'Profil2b',
        ],
        4 => [
            'Profil1a',
            'Profil2a',
        ],
        5 => [
            'Profil1a',
            'Profil2b',
        ],
        6 => [
            'Profil1b',
            'Profil2a',
            'Profil2b',
            'Profil2c',
        ],
        7 => [
            'Profil1b',
            'Profil2a',
            'Profil2c',
        ],
        8 => [
            'Profil1b',
            'Profil2a',
            'Profil2b',
        ],
        9 => [
            'Profil1b',
            'Profil2b',
        ],
        10 => [
            'Profil1b',
            'Profil2a',
        ],
        11 => [
            'Profil2',
            'Profil2a',
            'Profil2b',
            'Profil2c',
        ],
        12 => [
            'Profil2',
            'Profil2a',
            'Profil2c',
        ],
        13 => [
            'Profil2',
            'Profil2a',
            'Profil2b',
        ],
        14 => [
            'Profil2',
            'Profil2a',
        ],
        15 => [
            'Profil2',
            'Profil2b',
        ],
        16 => [
            'Profil2',
        ],
        17 => [
            'Profil3',
            'Profil3a',
        ],
        18 => [
            'Profil3',
            'Profil3d',
        ],
        19 => [
            'Profil3',
            'Profil3b',
        ],
        20 => [
            'Profil3',
            'Profil3c',
        ],
        21 => [
            'Profil3',
            'Profil3e',
        ],
        22 => [
            'Profil3',
        ],
        23 => [
            'Profil4',
            'Profil4a',
            'Profil4b',
            'Profil4c',
        ],
        24 => [
            'Profil4',
            'Profil4a',
            'Profil4b',
        ],
        25 => [
            'Profil4',
            'Profil4a',
        ],
        26 => [
            'Profil4',
            'Profil4d',
        ],
        27 => [
            'Profil4',
        ],
        28 => [
            'Profil5',
            'Profil5a',
        ],
        29 => [
            'Profil5',
            'Profil5b',
        ],
        30 => [
            'Profil6',
            'Profil6a',
        ],
        31 => [
            'Profil6',
        ],
    ];

    public static function findProfile(array $displays)
    {
        $key = array_search($displays, self::$map, true);

        if ($key === false) {
            throw new ProfileNumberNotFound('Can\'t find a matching profile', 0, null, $displays);
        }

        return $key;
    }
}
