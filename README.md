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
To create new numerator template, you need to call Numerator::createNumerator($config) just once.
Argument config is an array:
Numerator::createNumerator([
  'name' => 'doc_numerator1', 
  'model_class' => 'common\models\Doc', 
  'field' => 'number', 
  'type_field' => 'type', 
  'type_value' => 1, 	
  'mask' => '{УК-}999', 
  'init_val' => 55, //'начальный номер, с него продолжится нумерация'
]);
### Fields description        
* name - unique template name.
* model_class - Class of ActiveRecord model that need to be numerated.
* field - field of model_class instance that contains number.
* type_field - field of model_class instance that contains model type.
* type_value - I suppose that is clear
* mask - numerator's mask
  ** {} - text between curly brackets will display as it is.
  ** 9 - means that this position is reserved as a number.
  ** y - will be replaced with current year.
  ** m - will be replaced with current month.
  ** q - will be replaced with current quarter.
* init_val - initial value for number. Numeration will be proceed after this value.
Current version of code allows only one block with curly brackets, only one sequence of symbol 9 (9999) and this sequence must be placed last.

  
