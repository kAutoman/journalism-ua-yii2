<?php

use common\models\User;

$adminVisible = user()->can(User::ROLE_ADMIN);
$juryAdminVisible = user()->can(User::ROLE_JURY_ADMIN);
$juryVisible = user()->can(User::ROLE_JURY);
$moderatorVisible = user()->can(User::ROLE_MODERATOR);

$items = [
    ['label' => 'MAIN NAVIGATION', 'options' => ['class' => 'header'], 'visible' => $adminVisible],
    [
        'label' => bt('Pages', 'menu'),
        'icon' => 'sitemap',
        'url' => ['/page/page/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Article', 'menu'),
        'icon' => 'book',
        'url' => ['/article/article/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Experts', 'menu'),
        'icon' => 'th',
        'url' => ['/expert/expert/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Jury', 'menu'),
        'icon' => 'th',
        'url' => ['/jury/jury/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Member', 'menu'),
        'icon' => 'th',
        'items' => [
            ['label' => bt('Icon', 'menu'), 'url' => ['/member/member-icon/index']],
            ['label' => bt('Item', 'menu'), 'url' => ['/member/member-item/index']],
            ['label' => bt('Timeline', 'menu'), 'url' => ['/member/member-timeline/index']],
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Home', 'menu'),
        'icon' => 'th',
        'items' => [
            ['label' => bt('Header slider', 'menu'), 'url' => ['/home/home-header-slider/index']],
            ['label' => bt('Council', 'menu'), 'url' => ['/home/home-council-item/index']],
            ['label' => bt('Partner', 'menu'), 'url' => ['/home/home-partner-item/index']],
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Winners list', 'menu'),
        'icon' => 'th',
        'url' => ['/winner/winner-list/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => 'Request',
        'options' => ['class' => 'header'],
        'visible' => $adminVisible || $juryAdminVisible || $moderatorVisible
    ],
    [
        'label' => bt('Request', 'menu'),
        'icon' => 'th',
        'items' => [
            [
                'label' => 'Config',
                'url' => ['/config/competition-request'],
                'visible' => $adminVisible
            ],
            [
                'label' => 'Request',
                'url' => ['/request/competition-request/index'],
                'visible' => $adminVisible || $juryAdminVisible || $moderatorVisible
            ],
            [
                'label' => 'Request rating',
                'url' => ['/request/competition-request-rating/index'],
                'visible' => $adminVisible || $juryAdminVisible
            ],
        ],
        'visible' => $adminVisible || $juryAdminVisible || $moderatorVisible
    ],
    [
        'label' => 'Rejected request',
        'url' => ['/request/rejected-competition-request/index'],
        'visible' => $adminVisible || $juryAdminVisible || $moderatorVisible
    ],
    [
        'label' => 'Accepted request',
        'url' => ['/request/accepted-competition-request/index'],
        'visible' => $adminVisible || $juryAdminVisible || $juryVisible || $moderatorVisible
    ],
    ['label' => 'OTHER', 'options' => ['class' => 'header'], 'visible' => $adminVisible],
    [
        'label' => bt('Layout', 'menu'),
        'icon' => 'th',
        'items' => [
            [
                'label' => bt('Menu', 'menu'),
                'url' => ['/menu/menu/index'],
            ],
            [
                'label' => bt('Footer', 'menu'),
                'url' => ['/config/footer'],
            ],
            [
                'label' => bt('Social', 'menu'),
                'url' => ['/layout/social/index'],
            ],
        ],
        'visible' => $adminVisible
    ],
    ['label' => 'SETTINGS', 'options' => ['class' => 'header'], 'visible' => $adminVisible],
    [
        'label' => bt('Users', 'menu'),
        'icon' => 'users',
        'items' => [
            ['label' => bt('User list', 'menu'), 'url' => ['/rbac/user/index']],
            ['label' => bt('Log', 'menu'), 'url' => ['/log/user-log/index']],
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => Yii::t('back/menu', 'SEO'),
        'icon' => 'line-chart',
        'items' => [
            [
                'label' => bt('Tag manager', 'menu'),
                'url' => ['/config/tag-manager'],
            ],
            [
                'label' => bt('robots.txt', 'menu'),
                'url' => ['/seo/robots/index'],
            ],
            [
                'label' => bt('Meta tags', 'menu'),
                'url' => ['/seo/meta-tags/index']
            ],
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => bt('Mailer', 'menu'),
        'icon' => 'paper-plane',
        'items' => [
            ['label' => bt('Letters', 'menu'), 'url' => ['/mailer/mailer/letters']],
            ['label' => bt('Settings', 'menu'), 'url' => ['/mailer/mailer/index']]
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => Yii::t('back/menu', 'I18N'),
        'icon' => 'globe',
        'items' => [
            [
                'label' => Yii::t('back/menu', 'Translations'),
                'url' => ['/language/translation/index'],
            ],
            [
                'label' => Yii::t('back/menu', 'Languages'),
                'url' => ['/language/language/index'],
            ],
        ],
        'visible' => $adminVisible
    ],
    [
        'label' => Yii::t('back/menu', 'Configuration'),
        'icon' => 'cog',
        'url' => ['/config/admin/index'],
        'visible' => $adminVisible
    ],
    [
        'label' => Yii::t('back/menu', 'Logout'),
        'icon' => 'power-off',
        'url' => ['/logout'],
        'linkOptions' => ['data' => ['method' => 'POST']]
    ]
];

if (YII_ENV_DEV) {
    $items = array_merge($items, [
        ['label' => 'DEV TOOLS', 'options' => ['class' => 'header']],
        ['label' => 'API Documentation', 'icon' => 'code', 'url' => ['/docs']],
        ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
        ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
    ]);
}
return $items;
