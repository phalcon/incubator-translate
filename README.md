# Phalcon\Incubator\Translate

[![Discord](https://img.shields.io/discord/310910488152375297?label=Discord)](http://phalcon.io/discord)
[![Packagist Version](https://img.shields.io/packagist/v/phalcon/incubator-translate)](https://packagist.org/packages/phalcon/incubator-translate)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/phalcon/incubator-translate)](https://packagist.org/packages/phalcon/incubator-translate)
[![codecov](https://codecov.io/gh/phalcon/incubator-translate/branch/master/graph/badge.svg)](https://codecov.io/gh/phalcon/incubator-translate)
[![Packagist](https://img.shields.io/packagist/dd/phalcon/incubator-translate)](https://packagist.org/packages/phalcon/incubator-translate/stats)

## Issues tracker

https://github.com/phalcon/incubator/issues

## Database

You can use your database to store the translations, too.

First of all, you need to up your database. To do this, use [DI][1] (in `/public/index.php`). Take a look:

```php
use Phalcon\Db\Adapter\Pdo\Mysql;

$di->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => 123456,
                'dbname'   => 'application',
            ]
        );
    }
);
```

Then, you should get the translation through your `controller`. Put this on it:

```php
use Phalcon\Translate\Adapter\Database;

class IndexController extends \Phalcon\Mvc\Controller
{
    protected function _getTranslation()
    {
        return new Database(
            [
                'db'       => $this->di->get('db'), // Here we're getting the database from DI
                'table'    => 'translations', // The table that is storing the translations
                'language' => $this->request->getBestLanguage(), // Now we're getting the best language for the user
            ]
        );
    }

    // ...
}
```

To store the translations, the following table is recommended:
```sql
CREATE TABLE `translations` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `language` VARCHAR(5) NOT NULL COLLATE 'utf8_bin',
    `key_name` VARCHAR(48) NOT NULL COLLATE 'utf8_bin',
    `value` TEXT NOT NULL COLLATE 'utf8_bin',
    PRIMARY KEY (`id`)
)
```

Optional create unique index
```sql
CREATE UNIQUE INDEX translations_language_key_name_unique_index ON translations (language, key_name);
```

The columns are self-described, but pay attention to `language` — it's a column that stores the language
that the user is using, that can be `en`, `en-us` or `en-US`.
Now it's your responsibility to decide which pattern you want to use.

To display for your users the translated words you need to set up a variable to store the `expressions/translations`
from your database. *This step happens in your controller.* Follow the example:

```php
class IndexController extends \Phalcon\Mvc\Controller
{
    protected function _getTranslation()
    {
        // ...
    }

    public function indexAction()
    {
        $this->view->setVar(
            'expression',
            $this->_getTranslation()
        );
    }
}
```

Then, just output the`phrase/sentence/word` in your view:

```php
<html>
    <head>
        <!-- ... -->
    </head>
    <body>
        <h1><?php echo $expression->_("IndexPage_Hello_World"); ?></h1>
    </body>
</html>
```

Or, if you wish you can use [Volt][2]:
```php
<h1>{{ expression._("IndexPage_Hello_World") }}</h1>
```

## Mongo

Implements a Mongo adapter for translations.

A generic collection with a source to store the translations must be created and passed as a parameter.

```php
use MessageFormatter;
use Phalcon\Translate\Adapter\Mongo;
use My\Application\Collections\Translate;

$translate = new Mongo(
    [
        'collection' => Translate::class,
        'language'   => 'en',
    ]
);

echo $translate->t('application.title');
```

## MultiCsv

This adapter extends *Phalcon\Translate\Adapter\Csv* by allowing one csv file for several languages.

* CSV example (blank spaces added for readability):
```csv
#ignored;     en_US;  fr_FR;   es_ES
label_street; street; rue;     calle
label_car;    car;    voiture; coche
label_home;   home;   maison;  casa
```
* PHP example :
```php
// the constructor is inherited from Phalcon\Incubator\Translate\Adapter\CsvMulti
$titles_translater = new Phalcon\Translate\Adapter\MultiCsv(
    "{$config->langDir}/titles.csv",
    "es_ES",
    new InterpolatorFactory(),
    []
);

echo $titles_translater->query('label_home'); // string 'casa'


## Interpolator Intl

It needs the extension [intl](php.net/manual/book.intl.php) to be installed in PHP, and it uses [MessageFormatter](http://php.net/manual/en/class.messageformatter.php) objects in an interpolator interface.
More about the syntax convention can be read on this [formating guide](https://www.sitepoint.com/localization-demystified-understanding-php-intl/) and on the [ICU documentation](http://userguide.icu-project.org/formatparse/messages).

```php
<?php

use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\Interpolator\Intl;

$translate = new NativeArray(
    [
        'interpolator' => new Intl('en_US'), // this interpolator must be locale aware
        'content' => [
            'hi-name' => 'Hello {name}, it\'s {time, number, integer} o\'clock',
        ],
    ]
);

$name = 'Henry';

// Hello Henry, it's 8 o'clock
$translate->_(
    'hi-name',
    [
        'name' => $name,
        'time' => 8,
    ]
);
```

```php
<?php

use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\Interpolator\Intl;

$translate = new NativeArray(
    [
        'interpolator' => new Intl('fr_FR'), // this interpolator must be locale aware
        'content' => [
            'apples' => "{count, plural, =0{Je n'ai aucune pomme} =1{J'ai une pomme} other{J'ai # pommes}}.",
        ],
    ]
);

// thousands separator is " " (blank space) for fr_FR
echo $translate->_(
    'apples',
    [
        'count' => 1000,
    ]
); // J'ai 1 000 pommes
```
