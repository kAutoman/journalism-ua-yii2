<?php

return [
    // [GLOBAL DATA]
    'global-data' => 'global-data/data/global',

    // [SEO]
    'robots.txt' => 'seo/seo/robots',
    'sitemap.xml' => 'sitemap/default/index',

    // [USER]
    'POST,OPTIONS auth' => 'auth/login',
    'POST,OPTIONS refresh' => 'auth/refresh',

    // [FAQ]
    'GET,OPTIONS faq/index' => 'faq/faq/index',

    //[ NOMIATIONS ]
    'GET,OPTIONS nominations/index' => 'nominations/index',

    // [ARTICLE]
    'GET,OPTIONS article/list' => 'article/article/list',
    'GET,OPTIONS article/<alias>' => 'article/article/view',

    // [REQUESTS]
    'POST,OPTIONS question-request' => 'faq/faq/question-request',
    'POST,OPTIONS submit-request' => 'request/submit/submit-request',

    // [PAGES]
    'GET,OPTIONS pages/<alias>' => 'page/page/index',
    'GET,OPTIONS ' => 'page/page/home',
];
