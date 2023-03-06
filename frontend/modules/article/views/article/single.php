<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta
        name="viewport"
        content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <title>Честь Професії - <?php echo $entity->label; ?></title>
    <meta
        name="description"
        content="Конкурс професійної журналістики «Честь Професії»."
    />
    <link href="/css/vendor.css" rel="stylesheet" />
    <link href="/css/main.css" rel="stylesheet" />
    <link href="/css/news.css" rel="stylesheet" />
</head>
<body>
<div class="preloader">
    <div class="preloader__row">
        <img src="/img/preloader.svg" alt="preloader" />
    </div>
</div>
<div class="container-fuild single-news">
    <div class="block_header">
        <div class="container header_container_participants clearfix">
            <div class="navbar clearfix">
                <div class="brand">
                    <a href="/"><img src="/img/brand.svg" alt="brand" /></a>
                </div>
                <div class="header-nav-menu">
                    <ul>
                        <li><a href="/">Головна</a></li>
                        <li><a href="/news">Новини</a></li>
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
                        <li><a href="/news">Новини</a></li>
                        <li><a href="/juri">Журі</a></li>
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
            <div class="breadcrumbs">
                <ul>
                    <li><a href="/">Головна</a></li>
                    <li><a href="/news">Новини</a></li>
                    <li><span><?php echo $entity->label; ?></span></li>
                </ul>
            </div>
            <div
                class="participants_block_header single-news_block_header clearfix"
            >
                <div class="block_left">
                    <div class="block_title">
                        <?php echo $entity->label; ?>
                    </div>

                    <div class="block_description">
                        <div class="block_publication_date">
                            <span>Дата публiкацiї:</span>
                            <div class="publication_date"><?php echo $entity->fields()['date']($entity) ?></div>
                        </div>
                    </div>
                </div>

                <?php $banner = $model->getMeta()['pageOptions']['banner']['originalSrc'] ?? null;
                if ($banner) : ?>
                    <div class="block_right">
                        <div class="block_img">
                            <img src="<?php echo $banner; ?>" alt="block-1" class="block_1_img" />
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="block_space" style="padding: 40px"></div>

    <?php $blocks = $entity->getBlock();
    if (!empty($blocks)) :
        foreach ($blocks as $block) :
            $attributes = $block['attributes']; ?>

            <?php if ($block['id'] == 'ButtonLink' && !empty($attributes['button']['label']) && !empty($attributes['button']['url'])) : ?>
                <div class="container">
                    <div class="block_text">
                        <div class="block_btn">
                            <a class="btn" href="<?php echo $attributes['button']['url']; ?>">
                                <span><?php echo $attributes['button']['label']; ?></span>
                                <span><i></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($block['id'] == 'HeadingEditor') : ?>
                <div class="container">
                    <div class="block_text">
                        <div class="title"><?php echo $attributes['title']; ?></div>
                        <div class="text">
                            <?php echo $attributes['content']; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($block['id'] == 'Editor') : ?>
                <div class="container">
                    <div class="block_text">
                        <div class="text">
                            <?php echo $attributes['content']; ?>
                        </div>
                        <div class="margin"></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($block['id'] == 'Images' && !empty($attributes['images'])) :?>
                <div class="container">
                    <div class="block_text">
                        <div class="text">
                            <?php foreach ($attributes['images'] as $image) : ?>
                                <div class="block_content_img">
                                    <img src="<?php echo $image['originalSrc']; ?>" alt="" />
                                </div>

                                <div class="margin"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="margin"></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach;
    endif; ?>


    <footer>
        <div class="container">
            <div class="footer_block">
                <div class="top_block clearfix">
                    <div class="left_block">
                        <a href="/"
                        ><img
                                src="/img/footer/footer_logo.svg"
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
                            <li>
                                <a href="/participants" class="active">Учасникам</a>
                            </li>
                            <li>
                                <a href="/criteria">Критерії оцінки номінацій</a>
                            </li>
                            <li><a href="/peremozhci">Переможці</a></li>
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
                                src="/img/footer/vintage.svg"
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
