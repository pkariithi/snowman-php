# Snowman PHP

#### **NOTE: Still very early in the dev stage. Proof of concept as of now.**

A modular PHP framework where modules can be installed or removed programmatically.

The idea is that each 'feature' should be compartmentalized and managed as one unit - disabled, enabled, installed or removed.

By 'compartmentalize', we mean the configs, routes, migrations, controllers and models of that module should all be in one place.

You want to add a blog to your Snowman powered PHP application, just download the module online or use composer and it will automatically add it in ready for use. Want to disable it for sometime, just do so in its config.

## Why the name 'Snowman'

We wanted to showcase the modular bit of this framework and how best to do it other than a snowman.

You can replace the arms, the nose, the eyes all in real time and the snow man still just works.


# Setup

1. Download or clone the repo.
2. Make the `var` directory writable to the web user.
3. Run `composer install`
4. Serve the public folder at port 8080 `php -S localhost:8080`
5. Visit the URL `localhost:8080` on the browser
6. The homepage should display the text `Module\Site\Controller\SiteController::index` to show that the module `Site` has been called. This route is configured at `src/modules/Site/routes/routes.php` and the cntroller at `src/modules/Site/src/Controller/SiteController.php`.
7. If you visit a URL that doesn't exist, let's say `localhost:8080/fhjkhw` it should display a 404 page with the text `Module\Error\Controller\ErrorController::e404`. This page is being served by the Error module. The route is defined in `src/modules/Error/routes/routes.php` and the controller at `src/modules/Error/src/Controller/ErrorController.php`.
8. You can also try and disable the Error module in the `src/modules/Error/module.php` required file by setting `enabled` to `false`. When you visit a non-existent URL, the error displayed on the page is `Internal Server Error. Kindly try again later` since the Error module is now disabled.

# DONE
1. Dynamic routing system
2. Logging with various configurable levels
3. Base configs that can be overriden per environment
4. Modules can add to or override base configs
5. Services are managed via a container
6. Working module system

# TODO
1. Configure migrations in the modules
2. Configure views.
3. Configure controllers to return a view or JSON data, therefore behaving like a REST API.
4. Do a demo project using this framework.