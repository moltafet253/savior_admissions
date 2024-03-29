<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '5', '6', '7', '8', '9'],
    'default' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => false,
        'expire' => 120,
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#795548'],
        'encrypt' => false,
    ],
//    'math' => [
//        'length' => 9,
//        'width' => 120,
//        'height' => 36,
//        'quality' => 90,
//        'math' => true,
//    ],
//
//    'flat' => [
//        'length' => 6,
//        'width' => 160,
//        'height' => 46,
//        'quality' => 90,
//        'lines' => 1,
//        'bgImage' => false,
//        'bgColor' => '#ecf2f4',
////        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#795548'],
//        'contrast' => 10,
//    ],
//    'mini' => [
//        'length' => 3,
//        'width' => 60,
//        'height' => 32,
//    ],
//    'inverse' => [
//        'length' => 5,
//        'width' => 120,
//        'height' => 36,
//        'quality' => 100,
////        'sensitive' => true,
//        'angle' => 12,
//        'sharpen' => 10,
//        'blur' => 2,
//        'invert' => true,
//        'contrast' => 10,
//    ],
//    'encrypt' => false,
];
