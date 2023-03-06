<?php


return [
    // [ARTICLE]
    'GET news' => 'article/article/list',
    'GET news/<alias>' => 'article/article/view',

    'GET <alias>' => 'page/page/view',
    'GET /' => 'page/page/view',
];
