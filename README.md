# ConfigServiceProvider

A JSON-based config ServiceProvider for [Silex](http://silex.sensiolabs.org).

## Autoloader

If your application is defined in `/web/index.php` and the extension is in
`/vendor/service-provider/config`, do:

    $app['autoloader']->registerNamespace('Igorw', __DIR__.'/../vendor/service-provider/config/src');

## Usage

Pass the config file's path to the service provider's constructor. This is the
recommended way of doing it, allowing you to define multiple environments.

    $env = getenv('APP_ENV') ?: 'prod';
    $app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../config/$env.json"));

Now you can specify a `prod` and a `dev` environment.

**/config/prod.json**

    {
        "debug": false
    }

**/config/dev.json**

    {
        "debug": true
    }

To switch between them, just set the `APP_ENV` environment variable. In apache
that would be:

    SetEnv APP_ENV dev

Or in nginx with fcgi:

    fastcgi_param APP_ENV dev

Also, you can pass an array of replacement patterns as second argument.

    $app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../config/services.json", array(
        'dbalpath' => __DIR__.'/vendor/doctrine-dbal/lib',
    ));

Now you can use the pattern in your configuration file.

**/config/services.json**

    {
        "db.dbal.class_path": "%dbalpath%",
        "db.common.class_path": "%dbalpath%/vendor/doctrine-common/lib"
    }
