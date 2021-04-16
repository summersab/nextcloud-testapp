This is an extremely simple example app for Nextcloud for which I am hoping to get some help. Quite simply, I cannot figure out how to properly register a custom app middleware. I want to modify the `beforeOutput` data of the Dashboard app, but I cannot get the middleware to trigger.

- My test platform is v21.0.1 with the Dashboard app installed.
- You can see where I am wanting the middleware to trigger at `./lib/Middleware/TestMiddleware.php:13`.
- I am looking for help to know what to put at `./lib/AppInfo/Application.php:16` to properly load the middleware.
- Of course, I can force my middleware to load by adding the following to `OC::$SERVERROOT/lib/private/AppFramework/DependencyInjection/DIContainer.php:304`:
```
$dispatcher->registerMiddleware($c->get(OCA\TestApp\Middleware\TestMiddleware::class));
```
Shouldn't this be possible via a custom app? How can I make this work?
