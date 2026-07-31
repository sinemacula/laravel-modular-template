# Laravel Modular Template

[![Build Status](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/maintainability.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)
[![Code Coverage](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/coverage.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)

A GitHub template for building **stateless API** applications with Laravel 13 using a modular architecture. Powered by
[`sinemacula/laravel-modules`](https://github.com/sinemacula/laravel-modules), the standard `app/` directory is replaced
by a `modules/` directory where each subdirectory is a self-contained module — all wired into Laravel's native service
discovery with zero boilerplate.

> **Warning**
> This template is designed exclusively for **API-first development**. All frontend scaffolding, Blade views, sessions,
> web middleware, and the root `resources/` directory have been removed. Routes are treated as API routes with no prefix.
> If you need a full-stack web application, this is not the right starting point.

## What's Included

This template comes pre-configured with:

- **Modular architecture** via [`sinemacula/laravel-modules`](https://github.com/sinemacula/laravel-modules) — module
  auto-discovery, caching, and artisan commands
- **Foundation module** — application service provider and parallel testing support
- **User module** — a complete example demonstrating controllers, form requests, API resources, events, listeners,
  observers, and policies
- **100% test coverage** — unit and feature tests for all application code
- **Static analysis** — PHPStan level 8 via qlty with
  [`sinemacula/coding-standards`](https://github.com/sinemacula/coding-standards)

## Project Structure

```text
modules/
├── Foundation/              # Core framework module
│   └── Providers/           # Service providers
└── User/                    # Example domain module
    ├── Events/              # Domain events
    ├── Http/
    │   ├── Controllers/     # API controllers
    │   ├── Requests/        # Form request validation
    │   ├── Resources/       # API resources
    │   └── routes.php       # Module routes
    ├── Listeners/           # Event listeners
    ├── Models/              # Eloquent models
    ├── Observers/           # Model observers
    └── Policies/            # Authorization policies
```

A module may also contain the following, which the package discovers but the example module does not
use:

| Path                   | Discovered as                                         |
|------------------------|-------------------------------------------------------|
| `Console/Commands/`    | Artisan commands                                      |
| `Console/schedule.php` | Scheduled tasks                                       |
| `Resources/views/`     | View namespace, lowercased: `view('billing::index')`  |
| `Resources/lang/`      | Translation namespace: `__('billing::messages.sent')` |

Directory names become namespace segments, so they must be StudlyCase. Discovery accepts any
directory that is not dotted, which means a `modules/my_module/` is found but never resolves under
PSR-4.

Adding `Foundation/Resources/` has a side effect worth knowing: it becomes the application's
resource root, moving `resource_path()` and `lang_path()` into the module. `module:make` therefore
declines to create it for the default module.

Modules are auto-discovered at boot time and cached for performance. All standard Laravel conventions work inside each
module — there is no new API to learn. See the
[`sinemacula/laravel-modules` documentation](https://github.com/sinemacula/laravel-modules) for full details on what
gets discovered and how module caching works.

## Getting Started

Click **Use this template** on GitHub, then:

```bash
composer setup
```

This installs dependencies, generates an app key, and runs migrations.

### Creating a Module

Use the artisan command provided by `sinemacula/laravel-modules`:

```bash
php artisan module:make Billing
```

This scaffolds the standard directory structure under `modules/Billing/`. The namespace follows PSR-4:
`App\Billing\Models\Invoice`. No registration is required — the module is discovered automatically.

### Generating Classes Inside a Module

`app_path()` points at `modules/`, so Laravel's generators write to the `modules/` root rather than
into a module. Generators that take the name as-is accept a module path and produce the right
namespace:

```bash
php artisan make:model Invoice                  # modules/Invoice.php          namespace App
php artisan make:model Billing/Models/Invoice   # modules/Billing/Models/...   namespace App\Billing\Models
```

Generators that prepend their own namespace — `make:controller`, `make:request`, `make:policy` and
similar — cannot be steered this way. They apply their prefix regardless, so the file lands outside
the module:

```bash
php artisan make:controller Billing/InvoiceController
# modules/Http/Controllers/Billing/InvoiceController.php
```

For those, create the file by hand in the module, or generate it and move it. `module:make` already
lays down the directories.

### Module Paths

`module_path()` resolves the modules directory, and `resource_path()` accepts a `{module}::` prefix:

```php
module_path('Billing');                 // <base>/modules/Billing
resource_path('billing::views');        // <base>/modules/Billing/Resources/views
```

### Artisan Commands

| Command              | Description                                                 |
|----------------------|-------------------------------------------------------------|
| `module:make {name}` | Scaffold a new module with the standard directory structure |
| `module:list`        | List all discovered modules and their paths                 |
| `module:cache`       | Cache discovered module paths for faster resolution         |
| `module:clear`       | Clear the cached module paths                               |

Module caching is integrated into Laravel's `optimize` / `optimize:clear` lifecycle.

## Testing

Tests live in a central `tests/` tree that mirrors the module layout, rather than inside each module:

```text
tests/
├── Feature/
│   ├── Foundation/          # Module discovery, commands, application wiring
│   └── User/                # Mirrors modules/User
└── Unit/
    └── User/                # Mirrors modules/User
```

The package does not prescribe a layout or ship a base test case, so this is a convention of the
template rather than a requirement. Keeping tests outside the modules means a module directory holds
only shipped code, and `tests/Feature/Foundation/` has somewhere natural to assert that discovery
itself works.

Everything extends `Tests\TestCase`. Tests that do not need the framework booted say so per method:

```php
#[UnitTest]
public function testFillableAttributes(): void
```

## Development

```bash
composer dev             # Server, queue worker, and log viewer
composer test            # Run tests
composer check           # Static analysis and code quality (qlty)
composer format          # Auto-format code
```

Parallel testing is supported out of the box via ParaTest. Each parallel process gets its own database, seeded
automatically via `AppServiceProvider`.

## Requirements

- PHP ^8.3
- Laravel ^13.0

## Contributing

Contributions are welcome via GitHub pull requests.

## Security

If you discover a security issue, please contact Sine Macula directly rather than opening a public issue.

## License

Licensed under the [Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0).
