Сжатие и объедение стилей и скриптов дла production окружения
==============

Преимущество использовать именно этот подход в том, что все это позитивно воспринимается Google PageSpeed Insights и корректно сжимает css, не убирая лишние пробелы (например, при `top: calc(50% + 2px);`), не ломая при этом верстку.

###Перед установкой

0. Установить `npm`, если его еще нету в системе. Если есть - можно пропустить.
```bash
sudo apt-get install npm
```

###Установка

1. Инициализировать gulp на проекте: `cd frontend/tools/gulp && npm install`
2. Отредактировать `frontend/tools/gulp/assets-config.php` под свои нужды
3. Запустить сжатие коммандой `./yii asset frontend/tools/gulp/assets-config.php frontend/config/assets-prod.php` или `/bin/bash pack.sh` с корня приложения.

###Результат

В `frontend/web` у нас появится еще одна папка `compress`, в которой будут находиться js и css-файлики, которые буду объеденять в себе все минифицированные скрипты и стили.
Так же по-умолчанию в `frontend/config/main.php` в компонение `assetManager` есть проверка на текущее окружение, что бы понимать какой bundle использовать: `'bundles' => YII_ENV_DEV ? [] : require(__DIR__ . '/' . 'assets-prod.php'),`


##Работа с изображениями

Оптимизация картинок в папке `uploads`

**Пока не проверено на живом сервере (да и не уверен нужно ли) и как реагирует GooglePageSpeed**

```bash
cd frontend/tools/gulp
gulp compress-image --gulpfile gulpimage.js
```
