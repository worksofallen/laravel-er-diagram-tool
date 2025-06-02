# Entity Relationship Diagram Tool for Laravel 

# [![Coverage Status](https://coveralls.io/repos/github/worksofallen/laravel-er-diagram-tool/badge.svg)](https://coveralls.io/github/worksofallen/laravel-er-diagram-tool) 
# [![Downloads](https://img.shields.io/packagist/dt/worksofallen/laravel-er-diagram-tool.svg?maxAge=2592000)](https://packagist.org/packages/worksofallen/laravel-er-diagram-tool)

Automatically generate interactive entity relationship diagram for models & their relationships in Laravel and emit a static HTML file for use in a VuePress site.

This package is a heavily-customized fork from [AlvarezAllen99/laravel-erd](https://github.com/AlvarezAllen99/laravel-erd) meant for use in some very specific circumstances. You should probably check out the original package instead!

## Installation
You can install the package via composer.

```bash
composer require worksofallen/laravel-er-diagram-tool --dev
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="AlvarezAllen99\LaravelERD\LaravelERDServiceProvider"
```

## Usage
You can generate a static HTML file with the artisan command:

```php
php artisan erd:generate
```

This will be placed in `storage/app/public/erd`, or whatever path you have configured in `config/laravel-erd.php`.
