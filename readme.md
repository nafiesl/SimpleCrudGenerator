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

## How to install
On `composer.json` file, Add this :

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
$ composer require luthfi/simple-crud-generator dev-master --dev
```

> **Note:** this package still in development

## How to use
The package will **auto-discovered** in **Laravel 5.5**. Just type in terminal:

```bash
$ php artisan
```

We will find the `make:crud` command, it will `Create simple Laravel CRUD files of given model name`.
**Note: It also creates the model class file**.

```bash
$ php artisan make:crud Item

Item model generated.
Item table migration generated.
ItemsController generated.
Item view files generated.
ManageItemsTest generated.
ItemTest (model) generated.
CRUD files generated successfully!
```