# Laravel Modular Template

[![Build Status](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/sinemacula/laravel-modular-template/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/maintainability.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)
[![Code Coverage](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template/coverage.svg)](https://qlty.sh/gh/sinemacula/projects/laravel-modular-template)

A GitHub template for building **stateless API** applications with Laravel 13 using a modular architecture. Each module
acts as an isolated `app/` directory with its own models, controllers, routes, commands, listeners, events, observers,
policies, and more — all wired into Laravel's native service discovery.

This template is designed for API-first development. All frontend scaffolding, sessions, and web middleware have been
removed. Routes are treated as API routes with no prefix.

## How It Works

The standard `app/` directory is replaced by a `modules/` directory. Each subdirectory under `modules/` is a
self-contained module that follows standard Laravel conventions:

```text
modules/
├── Foundation/              # Core framework module
│   ├── Configuration/       # Module discovery, application builder
│   ├── Console/             # Commands and schedule
│   ├── Providers/           # Service providers
│   └── Support/             # Helpers
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

Modules are auto-discovered at boot time and cached for performance. All standard Laravel conventions work inside each
module — there is no new API to learn.

### What Gets Discovered

| Convention        | Module Path            | How It's Loaded                       |
|-------------------|------------------------|---------------------------------------|
| Routes            | `Http/routes.php`      | Passed to `withRouting(api: ...)`     |
| Console commands  | `Console/Commands/`    | Glob-based via `withCommands()`       |
| Scheduled tasks   | `Console/schedule.php` | Glob-based via `withCommands()`       |
| Event listeners   | `Listeners/`           | Glob-based via `withEvents()`         |
| Views             | `Resources/views/`     | Registered in `ModuleServiceProvider` |
| Translations      | `Resources/lang/`      | Registered in `ModuleServiceProvider` |
| Service providers | `Providers/`           | Loaded via `withProviders()`          |

Everything else — controllers, requests, resources, events, observers, policies, models, jobs, mail, notifications —
works via PSR-4 autoloading. No registration required.

### Module Caching

Module paths are cached to `bootstrap/cache/modules.php` and integrated into Laravel's `optimize` / `optimize:clear`
lifecycle:

```bash
php artisan optimize        # Includes module:cache
php artisan optimize:clear  # Includes module:clear
```

## Getting Started

Click **Use this template** on GitHub, then:

```bash
composer setup
```

This installs dependencies, generates an app key, and runs migrations.

### Creating a Module

Create a directory under `modules/` with the desired namespace:

```text
modules/Billing/
├── Events/
│   └── InvoicePaid.php
├── Http/
│   ├── Controllers/
│   │   └── InvoiceController.php
│   ├── Requests/
│   │   └── CreateInvoiceRequest.php
│   ├── Resources/
│   │   └── InvoiceResource.php
│   └── routes.php
├── Listeners/
│   └── SendInvoiceNotification.php
├── Models/
│   └── Invoice.php
├── Observers/
│   └── InvoiceObserver.php
└── Policies/
    └── InvoicePolicy.php
```

The namespace follows PSR-4: `App\Billing\Models\Invoice`. No registration is required — the module is discovered
automatically.

## Development

```bash
composer dev             # Server, queue worker, and log viewer
composer test            # Run tests
composer test -- --parallel  # Run tests in parallel
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
