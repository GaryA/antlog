AntLog 
======

AntLog is a web application for running AWS-style double-elimination contests. It is based on the Yii 2 
Basic Application Template, plus the user database table from the Yii 2 Advanced Application Template.



DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages (from Yii2)
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement for this application is that your Web server supports PHP 5.4.0.

AntLog requires the Yii 2 framework which can be downloaded from [yiiframework.com]
(http://www.yiiframework.com/download/). Alternatively Yii 2 can be installed via
[Composer](http://getcomposer.org/) - see [yiiframework.com](http://www.yiiframework.com/download/)

INSTALLATION
------------

### Install from an Archive File

Extract the Yii 2 archive file downloaded from [yiiframework.com](http://www.yiiframework.com/download/) 
then copy the `vendor` directory into the AntLog project directory.


### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install Yii 2 using the following command:

~~~
php composer.phar global require "fxp/composer-asset-plugin:1.0.0"
php composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-basic basic
~~~

This will install Yii 2 in a directory called `basic`. Copy the `vendor` directory into the AntLog project 
directory.


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=antlog',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTE:** Run `antlog.sql` to create the database and tables.

Also check and edit the other files in the `config/` directory to customize your application.
