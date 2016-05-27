# Yii2 модуль inblank/yii2-activeuser

[![Build Status](https://img.shields.io/travis/inblank/yii2-activeuser/master.svg?style=flat-square)](https://travis-ci.org/inblank/yii2-activeuser)
[![Packagist Version](https://img.shields.io/packagist/v/inblank/yii2-activeuser.svg?style=flat-square)](https://packagist.org/packages/inblank/yii2-activeuser)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/inblank/yii2-activeuser/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-activeuser/?branch=master)
[![Code Quality](https://img.shields.io/scrutinizer/g/inblank/yii2-activeuser/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/inblank/yii2-activeuser/?branch=master)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/inblank/yii2-activeuser/master/LICENSE)

> The **[English version](https://github.com/inblank/yii2-activeuser/blob/master/README.md)** of this document available [here](https://github.com/inblank/yii2-activeuser/blob/master/README.md).

Модуль `yii2-activeuser` для [Yii2](http://www.yiiframework.com/) позволяет

## Установка

Рекомендуется устанавливать модуль через [composer](http://getcomposer.org/download/).

Перейдите в папку проекта и выполните в консоли команду:

```bash
$ composer require inblank/yii2-activeuser
```

или добавьте:

```json
"inblank/yii2-activeuser": "~0.1"
```

в раздел `require` конфигурационного файла `composer.json`.

Добавьте следующий код в файл основной конфигурации приложения:
```php
'modules' => [
    'activeuser'=>[
        'class' => 'inblank\activeuser\Module',
    ],
],
```

## Настройка

## Использование