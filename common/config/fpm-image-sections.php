<?php

use metalguardian\fileProcessor\helpers\FPM;

return [
    'admin' => [
        'preview' => [
            'action' => FPM::ACTION_ADAPTIVE_THUMBNAIL,
            'width' => 100,
            'height' => 100,
        ],
    ],
];
