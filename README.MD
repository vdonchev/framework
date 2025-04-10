
# Tiny Framework

A lightweight and expressive PHP framework built for clarity, structure, and extensibility. Ideal for small to mid-sized web applications.

---

## Features

### Built-in functionalities

- Dependency Injection Container (PHP-DI)
- Routing (FastRoute)
- Middleware support
- Templating with Twig
- Flash messages
- Database support via MeekroDB
- SMTP mailer (Nette Mail)
- Console commands (Symfony Console)
- Caching (Symfony Cache)
- Logging (donchev/simple-logger)
- Data validation and sanitization

Includes **Bootstrap 5** and **Bootstrap Icons** for UI development.

---

## Requirements

- PHP 8.2 or higher

---

## Installation

You can install the framework using Composer.

```bash
composer create-project donchev/framework myapp
```

---

## Configuration

All application configuration is located in `config/settings.yaml`. Example:

```yaml
app:
  env: dev
  name: Tiny Framework
  base_url: https://framework.test
  use_sessions: true
  log_file: '%var_dir%/log/framework.log'

api:
  username:
  password:

middleware:
  dir: '%base_dir%/src/Middleware'
  namespace: Donchev\Framework\Middleware

db:
  host: 
  name: 
  username: 
  password: 
  port: 3306
  charset: utf8mb4

mail:
  host: smtp.mailtrap.io
  username: 
  password: 
  secure: null
  port: 2525
  from_name: 'Tiny Framework Mailer'
  from_email: 
```

Override values via `settings.local.yaml`.

---

## Usage

### Routing

Define routes in `config/routes.php`:

```php
return static function (RouteCollector $router) {
    $router->get('/', [HomeController::class, 'index']);
    $router->post('/submit', [ContactController::class, 'submit']);
    $router->get('/blog/{slug}', [BlogController::class, 'show']);
};
```

---

### Controllers

Controllers extend `AbstractController`:

```php
class HomeController extends AbstractController
{
    public function index(): void
    {
        $this->renderTemplate('home/index.html.twig', [
            'title' => 'Welcome!'
        ]);
    }
}
```

---

### Sending Emails

```php
use Nette\Mail\Message;

$message = new Message();
$message->setFrom('you@site.com')
        ->addTo('user@example.com')
        ->setSubject('Hello')
        ->setBody('This is a test email.');

$mailer = $container->get(Mailer::class);
$mailer->send($message);
```

---

### Database (MeekroDB)

```php
/** @var MeekroDB $db */
$db = $container->get(MeekroDB::class);

$users = $db->query("SELECT * FROM users WHERE active = %i", 1);
```

---

### Using Cache

```php
use Symfony\Contracts\Cache\CacheInterface;

$cache = $container->get(CacheInterface::class);

$data = $cache->get('some_key', function () {
    return 'cached value';
});
```

---

### Flash Messages

Set flash messages in controller:

```php
$flash = new FlashService();
$flash->add('success', 'Saved successfully!');
```

Render them in Twig:

```twig
{% include '_flash.html.twig' with {'flashes': flashes} %}
```

---

### Middleware

Implement `MiddlewareInterface` and drop your class into `src/Middleware`. It will be auto-loaded.

```php
class RequestLoggerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // logging logic...
        return $handler->handle($request);
    }
}
```

---

### Twig Template Globals

Twig templates automatically have access to:

- `http_get`, `http_post`, `http_cookie`, `http_session`
- `settings`
- `get_flash()` function

---

### Data Validator & Sanitizer

**Validator:**

```php
$validator = new DataValidator();

$data = [
    'name' => 'John Doe',
    'age' => 32
];

$rules = [
    'name' => 'string',
    'age' => 'min:30'
];

$result = $validator->validate($data, $rules);

// Output:
[
  'age' => [
    '"age" must have at least 30 characters'
  ]
]
```
**Validator & Flash Messages:**

```php

class AuthController
{
    public function login(Post $post, DataValidator $validator, FlashService $flashService): void
    {
        $data = [
            'email' => $post->get('email'),
            'password' => $post->get('password'),
        ];

        $rules = [
            'email' => 'required|email',
            'password' => 'required|secure',
        ];

        $errors = $validator->validate($data, $rules);

        if (!empty($errors)) {
            $flashService->addError($errors);
            // return or redirect with errors
        }

        //...
    }
}
```

**Sanitizer:**

```php
$sanitizer = new DataSanitizer();

$data = ['asd123', '1.5ds'];
$sanitized = $sanitizer->sanitize($data, 'float');

// Output: [123.0, 1.5]
```

---

### Console Commands

Define commands in `bin/console`. Example:

```php
class HelloCommand extends Command
{
    protected string $signature = 'hello';
    protected string $description = 'Says hello';

    public function handle(): void
    {
        $this->info('Hello from the Tiny Framework!');
    }
}
```

Run them:

```bash
php bin/console hello
```

---

## Code Style

This project uses **php-cs-fixer**. You can format your code using:

```bash
composer cs-fix
```

Check for issues without modifying files:

```bash
composer cs-check
```

---

## Testing

The framework includes PHPUnit 12+ test coverage out of the box.

Run tests:

```bash
vendor/bin/phpunit
```

All test files are located in the `tests/` folder and follow PSR-4 autoloading with the `Tests\` namespace.

---

## Development Mode

Set `app.env: dev` to enable detailed error pages (via Whoops) and debugging tools.

---

## Production

Set `app.env: prod` to enable compiled container and cache.

---

## License

This project is open-source and licensed under the [MIT License](LICENSE).

---

## Author

Developed by Donchev. Contributions are welcome.
