# LaravelPayments


## To install

**Add to** app/Providers/NovaServiceProvider.php@**resources**:
```php
Nova::resources(Config::get('nova.dynamic_resources') ?? []);
```
