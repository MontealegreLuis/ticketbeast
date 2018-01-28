# TicketBeast

## Tests

In order to run the tests create an `.env` file for Dusk

```
$ cp .env.dusk.example .env.dusk.local
```

Dusk needs a valid `APP_URL` and the configuration for its own database

Once you have the configuration file, initialize Dusk as follows

```
$ php artisan dusk:install
```

Dusk does not play well with `artisan serve`. Laravel Valet is the simplest alternative.

Then, your `APP_URL` should be something like `ticketbeast.test`
