# Подписка на снижение цены товара

> Модуль для 1С-Битрикс: Управление сайтом

![screenshot](/screenshot.png)

## Описание
Модуль позволяет подписаться на снижение цены товара. При снижении стоимости на указанный e-mail будет отправлено уведомление.


## Подключение

Для работы скопировать шаблон `catalog.element` из

```sh
bitrix\components\bitrix\catalog.element\templates\.default
```
в
```sh
local\templates\.default\components\bitrix\catalog.element\.default
```

Добавить вызов компонента в `template.php`:

```php
<?
    $APPLICATION->IncludeComponent(
        "madsoft:pricereduction",
        ".default",
        Array(
            "BUTTON_CLASS" => "btn btn-default"
        ),
        false
    );
?>
```

Лицензия
----

[MIT](http://www.opensource.org/licenses/mit-license.php)

[//]: #
