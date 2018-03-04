# Installation

## 1. Install package using composer
```bash
composer require admin-panel/admin-bundle:dev-master
```

## 2. Register bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new \AdminPanel\Symfony\AdminBundle\AdminPanelBundle()
    ];

    return $bundles;
}
```

## 3. Import routing

```yaml
# app/config/routing.yml

admin:
    resource: "@AdminPanelBundle/Resources/config/routing/admin.yml"
    prefix: /admin
```

## 4. Enable required framework option

You have to enable translation, form and csrf_protection framework options.

```yaml
# app/config/config.yml

framework:
    translator:      { fallback: %locale% }
    form: true
    csrf_protection:
        enable: true
```

If you using Symfony 4 then update following two files:

```yaml
# config/packages/framework.yaml

framework:
    #...
    form: true
    csrf_protection: true
    session: ~
    templating:
        engines: ['twig']
```
```yaml
# config/packages/translation.yaml

framework:
    default_locale: '%locale%'
    translator:
        paths:
            - '%kernel.project_dir%/translations'
        fallbacks:
            - '%locale%'
```