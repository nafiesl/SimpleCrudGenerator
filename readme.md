# Laravel Simple CRUD Generator
[![Build Status](https://travis-ci.org/nafiesl/SimpleCrudGenerator.svg?branch=master)](https://travis-ci.org/nafiesl/SimpleCrudGenerator)

An artisan `make:crud` command to create a simple CRUD feature on your Laravel 5.5 application.

> **Development in progress**

## About this package
With this package installed on local environment, we can use (e.g.) `php artisan make:crud Item` command to generate some files :
- **App\Item.php** eloquent model
- **ItemsController.php** in `app/Http/Controllers` directory
- **create_items_table.php** migration file
- **index.blade.php** view file in `resources/views/items` directory
- **forms.blade.php** view file in `resources/views/items` directory
- **ManageItemsTest.php** feature test class in `tests/Feature` directory
- **ItemTest.php** unit test class in `tests/Unit/Models` directory

The model will only have 2 pre-definded attributes or columns : `name` and `description` on each model and database table.

## Attention
- This package still in development
- Use this package on new Laravel project for simulation.
- It will creates the **Model** class file, don't use it to generate files for existing **Model** class.
- You need a `resources/views/layouts/app.blade.php` view file, simply create one with `php artisan make:auth` command.

## How to install
This package has not been submitted to packagist, so we can use github repo as additional repository. To have the additional repository, we do some configiration on `composer.json` file, add this :

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/nafiesl/SimpleCrudGenerator.git"
        }
    ],
```
then

```bash
# Bootstrap Form Field generator
$ composer require luthfi/formfield

# Get the package
$ composer require luthfi/simple-crud-generator dev-master --dev
```

## How to use
The package will **auto-discovered** in **Laravel 5.5**. Just type in terminal:

```bash
$ php artisan
```

We will find the `make:crud` command, it will `Create simple Laravel CRUD files of given model name`.

```bash
$ php artisan make:crud Item

Item resource route generated on routes/web.php.
Item model generated.
Item table migration generated.
ItemsController generated.
Item view files generated.
item lang files generated.
item model factory generated.
BrowserKitTest generated.
ManageItemsTest generated.
ItemTest (model) generated.
CRUD files generated successfully!
```

Create mysql database, set your database credential on `.env` file. Then :

```bash
$ php artisan migrate
```

Visit your application in new resource route : `http://127.0.0.1:8000/items`

The CRUD function should work.

Next, to use the generated testing classes, we can set the database environment using in-memory database SQLite. Open `phpunit.xml`. Add two lines below on the `env` :

```xml
<phpunit>
    <!-- ..... -->
    <php>
        <!-- ..... -->
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

Try out the generated testing classes via terminal.

```bash
$ vendor/bin/phpunit
```

All tests should be passed.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
