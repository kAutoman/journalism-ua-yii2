<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8"/>
    <meta
            name="viewport"
            content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <title>Честь Професії - Шорт-лист переможців</title>
    <meta
            name="description"
            content="Конкурс професійної журналістики «Честь Професії»."
    />
    <link href="/css/vendor.css" rel="stylesheet"/>
    <link href="/css/main.css" rel="stylesheet"/>
    <link href="/css/news.css" rel="stylesheet"/>
</head>
<body>
<div class="preloader">
    <div class="preloader__row">
        <img src="/img/preloader.svg" alt="preloader"/>
    </div>
</div>
<div class="container-fuild container_news">
    <div class="block_header">
        <div class="container header_container_news clearfix">
            <div class="navbar clearfix">
                <div class="brand">
                    <a href="/"><img src="/img/brand.svg" alt="brand"/></a>
                </div>
                <div class="header-nav-menu">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li>
                            <a href="/news" class="active">Новини</a>
                        </li>
                        <!--<li><a href="/juri">Журі</a></li>-->
                        <li>
                            <a href="/participants">Учасникам</a>
                        </li>
                        <li>
                            <a href="/criteria"
                            >Критерії оцінки номінацій</a
                            >
                        </li>
                        <li>
                            <a href="/peremozhci">Переможці</a>
                        </li>
                    </ul>
                </div>
                <div class="mobile_toggle"><i></i> <i></i> <i></i></div>
                <div class="header-nav-menu-mobile">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li>
                            <a href="/news" class="active">Новини</a>
                        </li>
                        <!--<li><a href="/juri">Журі</a></li>-->
                        <li>
                            <a href="/participants">Учасникам</a>
                        </li>
                        <li>
                            <a href="/criteria"
                            >Критерії оцінки номінацій</a
                            >
                        </li>
                        <li>
                            <a href="/peremozhci">Переможці</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="block_header">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li><span>Новини</span></li>
                    </ul>
                </div>
                <div class="title">Новини</div>
            </div>
        </div>
    </div>

    <?php use frontend\components\CustomPagination;
    use yii\widgets\LinkPager;

    if (!empty($models)) : ?>
        <div class="news">
            <div class="container">
                <div class="news-inner">
                    <div class="news-list">
                        <?php foreach ($models as $model) : ?>
                            <a href="<?php echo $model->link; ?>" class="news-item">
                                <?php $preview = $model->fields()['preview']($model); ?>

                                <?php if ($preview) : ?>
                                    <div class="news-item__photo">
                                        <img src="<?php echo $preview['originalSrc']; ?>" alt=""/>
                                    </div>
                                <?php endif; ?>

                                <div class="news-item__content">
                                    <div class="news-item__icon"></div>
                                    <div class="news-item__date"><?php echo $model->fields()['date']($model) ?></div>
                                    <h2 class="news-item__title"><?php echo $model->label; ?></h2>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php echo CustomPagination::widget([
                    'pagination' => $pages,
                    'hideOnSinglePage' => true,
                    'prevPageLabel' => '<svg width="8" height="14" viewBox="0 0 8 14" xmlns="http://www.w3.org/2000/svg">
                          <path clip-rule="evenodd" d="M6.66667 -9.53674e-07L0 7L6.66667 14L8 12.6L2.66667 7L8 1.4L6.66667 -9.53674e-07Z"/>
                      </svg>',

                    'nextPageLabel' => '<svg width="8" height="14" viewBox="0 0 8 14" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.33333 -9.53674e-07L8 7L1.33333 14L0 12.6L5.33333 7L0 1.4L1.33333 -9.53674e-07Z" />
                            </svg>',

                    'maxButtonCount' => 5,

                    'options' => [
                        'tag' => 'div',
                        'class' => 'pagination',
                    ],

                    'linkOptions' => ['class' => 'pagination__item'],
                    'activePageCssClass' => 'active',
                    'disabledPageCssClass' => '',
                    'disabledListItemSubTagOptions' => [
                        'class' => 'pagination__arrow pagination__item'
                    ],

                    'prevPageCssClass' => 'pagination__arrow pagination__item',
                    'nextPageCssClass' => 'pagination__item pagination__arrow',
                ]); ?>
            </div>
        </div>
    <?php endif; ?>

    <footer>
        <div class="container">
            <div class="footer_block">
                <div class="top_block clearfix">
                    <div class="left_block">
                        <a href="/"
                        ><img
                                src="img/footer/footer_logo.svg"
                                alt="footer_logo"
                                class="footer_logo"
                        /></a>
                        <ul class="footer_social">
                            <li><a href="https://www.facebook.com/honorofprofession" target="_blank" rel="nofollow" class="social_facebook"></a></li>
                            <!--<li><a href="#" target="_blank" rel="nofollow" class="social_instagram"></a></li>-->
                        </ul>
                    </div>
                    <div class="center_block">
                        <ul>
                            <li><a href="/">Головна</a></li>
                            <li><a href="/participants">Учасникам</a></li>
                            <li>
                                <a href="/criteria">Критерії оцінки номінацій</a>
                            </li>
                            <li>
                                <a href="/peremozhci" class="active">Переможці</a>
                            </li>
                        </ul>
                    </div>
                    <div class="right_block">
                        <ul>
                            <li><a href="tel:+380442892599">+38 (044) 254-55-56</a></li>
                            <li>
                                <a href="mailto:project@nam.com.ua">project@nam.com.ua</a>
                            </li>
                            <li><a href="http://www.nam.org.ua">www.nam.org.ua</a></li>
                        </ul>
                    </div>
                </div>
                <div class="bottom_block clearfix">
                    <div class="copy">© 2020 Честь професії. All right reserved.</div>
                    <div class="vintage_logo">
                        <a href="https://vintage.com.ua/" target="_blank" rel="nofollow"
                        ><img
                                src="img/footer/vintage.svg"
                                alt="Vintage WEB Production"
                                class="vintage_logo"
                        /></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="/js/vendor.bundle.js"></script>
<script src="/js/app.bundle.js"></script>
</body>
</html>
