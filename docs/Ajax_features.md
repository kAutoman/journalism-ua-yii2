Часто, при клике по кнопке, ссылке нужно получить с сервера данные(форму, попап, заменить блок на странице) bkb без
перезагрузки страницы. Для этого используем ajax-обработчики, а в success событии ajax-запроса вызываем указанную ниже
функцию.

```javascript
function parseResponse(response) {
    if (response.replaces instanceof Array) {
        for (var i = 0, ilen = response.replaces.length; i < ilen; i++) {
            $(response.replaces[i].what).replaceWith(response.replaces[i].data);
        }
    }
    
    if (response.append instanceof Array) {
        for (i = 0, ilen = response.append.length; i < ilen; i++) {
            $(response.append[i].what).append(response.append[i].data);
        }
    }
    
    if (response.content instanceof Array) {
        for (i = 0, ilen = response.content.length; i < ilen; i++) {
            $(response.content[i].what).html(response.content[i].data);
        }
    }
    
    if (response.js) {
        $("body").append(response.js);
    }
    
    if (response.refresh) {
        window.location.reload(true);
    }
    
    if (response.redirect) {
        window.location.href = response.redirect;
    }
}
```

Возможности:

- replaces, заменить 1 и/или более блоков на странице. На сервере формируем в таком виде:

```php
$data = [
    'replaces' => [
        [
            'data' => $this->renderPartial('_cart_positions'),
            'what' => '.buscket-w .purchase'
        ],
        [
            'data' => StoreProductCartPosition::getShowCartButton(),
            'what' => 'a.btn-top-buscket.btn-round.btn-round__yell'
        ],
    ],
];

return Json::encode($data);
```

- append, добавить к элементу 1 и/или более блоков. На сервере формируем в таком виде:

```php
$data = [
    'append' => [
        [
            'data' => $this->renderPartial('_cart_positions'),
            'what' => '.buscket-w'
        ],
        [
            'data' => StoreProductCartPosition::getShowCartButton(),
            'what' => '.buscket-w'
        ],
    ],
];

return Json::encode($data);
```

- content, вставить контент в блок с указанным селектором. На сервере формируем в таком виде:

```php
$data = [
    'content' => [
        [
            'data' => $this->renderPartial('_cart_positions'),
            'what' => '.buscket-w'
        ],
        [
            'data' => StoreProductCartPosition::getShowCartButton(),
            'what' => '.buscket-w .button'
        ],
    ],
];

return Json::encode($data);
```

- js, добавить на страницу, и соответственно выполнить js-код. На сервере:

```php
$data = [
    'js' => Html::script('showPopup(); $(".cart-form").remove();')
];

return Json::encode($data);
```

- refresh, перезагрузить страницу. На сервере:

```php
$data = [
    'refresh' => true
];

return Json::encode($data);
```

- redirect, перенаправить на указанную страницу. На сервере:

```php
$data = [
    'redirect' => News::getViewLink(['alias' => $model->alias])
];

return Json::encode($data);
```

Все вышеуказанные параметры можна комбинировать на сервере в любом разумном кол-ве.
