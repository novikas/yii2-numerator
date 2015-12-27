Yii 2 Numerator extension
============================

rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-basic/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-basic)


REQUIREMENTS
------------

The minimum requirement by this extansion that your Web server supports PHP 5.4.0.

INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this extension using the following command:

~~~
php composer.phar require novikas/yii2-numerator: "@dev"
~~~

### Database

First of all you need to execute migrations that is located in extensions directory:

```php
yii mirate --migrationsPath = @novikas/numerator/migrations
```

**NOTE:** 
