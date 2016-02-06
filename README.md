Yii 2 Numerator extension
============================

rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-basic/v/stable.png)](https://packagist.org/packages/novikas/yii2-numerator)


REQUIREMENTS
------------

The minimum requirement by this extension that your Web server supports PHP 5.4.0.

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

To create new simple numerator template, you need to call Numerator::createNumerator($config) just once.
Argument config is an array:
```php
Numerator::createNumerator([
  'name' => 'doc_numerator1',
  'model_class' => 'common\models\Doc',
  'field' => 'number',
  'type_field' => 'type',
  'type_value' => 1,
  'mask' => '{УК-}999',
  'init_val' => 55
]);
```
If your models differs from each other by more than on field (property), you can use the following template:
```php
Numerator::createNumerator([
  'name' => 'doc_numerator2',
  'model_class' => 'common\models\Doc',
  'field' => 'number',
  'type_field' => 'type1&type2',
  'type_value' => "1&2",
  'mask' => '{UK-}999',
  'init_val' => 2
]);
```
So, according this template numerator will numerate your models by following condition

```MySQL
WHERE type1 = 1 AND type2 = 2
```
**NOTE**
Note that values of the properties Numerator::type_value and Numerator::type_field must corresponds.

If your models differs in properties of their relations, you can use the following template:

```php
Numerator::createNumerator([
  'name' => 'doc_numerator2',
  'model_class' => 'common\models\Doc',
  'field' => 'number',
  'type_field' => 'doc.type1&store.type2',
  'type_value' => "1&2",
  'mask' => '{UK-}999',
  'init_val' => 2,
  'join_table' => 'store',
  'join_on_condition' => 'doc.FK_store=store.id'
]);
```

**NOTE**
Note that you need to set properties Numerator::join_table and Numerator::join_on_condition. Also it's recommended to add table prefix in property type_field to avoid ambiguity.

### Fields description        
* name - unique template name.
* model_class - Class of ActiveRecord model that need to be numerated.
* field - field of model_class instance that contains number.
* type_field - field of model_class instance that contains model type.
* type_value - I suppose that is clear
* mask - numerator's mask
* init_val - initial value for number. Numeration will be proceed after this value.

### Mask description
* {} - text between curly brackets will display as it is.
* 9 - means that this position is reserved as a number.
* y - will be replaced with current year.
* m - will be replaced with current month.
* q - will be replaced with current quarter.
**NOTE**
- Current version of code allows only one block with curly brackets, only one sequence of symbol 9 (9999) and this sequence must be placed last.

### Usage
After you created all templates for your models, you can use Numerator as the following code:
```php
$numerator = Numerator::getNumerator($templateName);
$doc->number = $numerator->nextNumber();
```

This code could be place in rules of models as a default value for number property for example.
