## About TWGroup Starter Pack for Laravel

TWGroup Starter Pack, provide a bunch of packages and first config steps to develop new web project on a record time, we belive developers must focus on business logic and CRUDs and basically stuff must be an automatic task. At this time the Starter Pack contains this packages:

- [Laravel](https://laravel.com/).
- [Backpack](https://backpackforlaravel.com/).
- [Laravel UI](https://styde.net/paquete-laravel-ui-en-laravel-6/).
- [Package: settings](https://backpackforlaravel.com/docs/4.1/install-optionals#settings).
- [Package: permission manager](https://github.com/Laravel-Backpack/PermissionManager#install).
- [Package: Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar).
- [Package: Inverse seed generator (iSeed)](https://github.com/orangehill/iseed).

Starter Pack is a powerful compilation of packages to develop awesome projects.

## Installation & First steps

1. Clone or Fork "TWGroup Starter Pack for Laravel".
```bash
git clone git@github.com:twgroupcl/twg-starter-pack.git
```
2. Config .env params
3. Install packages
```bash
composer install
npm install
npm run dev
```
3. Generate key & create symlink
```bash
php artisan key:generate
php artisan storage:link
```
4. Run migrations & seeders
```bash
php artisan migrate:fresh --seed
```
5. Small fix: Add "images" folder to route "/storage/app/public/" and set CHMOD to write.

All done, now you can put your hands to create some code.

## Create Modules

With "TWGroup Starter Pack for Laravel" is very easy start a new module. Here are the simple steps you need to run:

1. Create a migration.
```bash
php artisan make:migration create_customers_table
```
2. Let Backpack take care of duty.
```bash
php artisan backpack:crud customer #use singular, not plural
```
The "backpack:crud" would generate:

- a migration file
- a model (app\Models\Tag.php)
- a request file, for form validation (app\Http\Requests\TagCrudRequest.php)
- a controller file, where you can customize how the CrudPanel looks and feels (app\Http\Controllers\Admin\TagCrudController.php)
- a route, as a line inside routes/backpack/custom.php

It will also add:

- a route inside routes/backpack/custom.php, pointing to that controller;
- a sidebar item inside resources/views/vendor/backpack/base/inc/sidebar_content.blade.php;

There are also secret functions that only the creator of this pack knows, you must discover them for yourself or work at TWGroup.

## Child repos

TWGroup team members can use this repository as a template, to make this work follow this instructions.

### Sync with parent repository

1. Make sure you have push al important changes.
2. Sync remote. [git-upstream-template](https://github.com/rioam2/git-upstream-template)
```bash
npx git-upstream-template git@github.com:twgroupcl/twg-starter-pack.git
```
Note: if sync fail, you can rollback with: git stash pop

### Push important changes to parent repository

1. 

## Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Jorge Castro via [jorge.castro@twgroup.cl](mailto:jorge.castro@twgroup.cl). All security vulnerabilities will be promptly addressed.

## Licence

This repository is private. At this time is forbbiden make public forks.