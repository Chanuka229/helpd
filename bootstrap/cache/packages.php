<?php return array (
  'dacoto/laravel-dashboard-installer' => 
  array (
    'providers' => 
    array (
      0 => 'dacoto\\LaravelInstaller\\Providers\\LaravelDashboardInstallerProvider',
    ),
  ),
  'dacoto/laravel-setenv' => 
  array (
    'providers' => 
    array (
      0 => 'dacoto\\SetEnv\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'SetEnv' => 'dacoto\\SetEnv\\Facade',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'fruitcake/laravel-cors' => 
  array (
    'providers' => 
    array (
      0 => 'Fruitcake\\Cors\\CorsServiceProvider',
    ),
  ),
  'kkomelin/laravel-translatable-string-exporter' => 
  array (
    'providers' => 
    array (
      0 => 'KKomelin\\TranslatableStringExporter\\Providers\\ExporterServiceProvider',
    ),
  ),
  'laravel/sanctum' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Sanctum\\SanctumServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'sentry/sentry-laravel' => 
  array (
    'providers' => 
    array (
      0 => 'Sentry\\Laravel\\ServiceProvider',
      1 => 'Sentry\\Laravel\\Tracing\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Sentry' => 'Sentry\\Laravel\\Facade',
    ),
  ),
  'tucker-eric/eloquentfilter' => 
  array (
    'providers' => 
    array (
      0 => 'EloquentFilter\\ServiceProvider',
    ),
  ),
);