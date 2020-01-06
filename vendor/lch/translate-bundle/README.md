#### Configuration

```php
<?php

// config/bundles.php

return [
    LchTranslateBundle\LchTranslateBundle::class => ['all' => true],
    // ...
];
```

```yaml
# config/packages/lch_translate_bundle.yaml

lch_translate_bundle:
  available_languages:
    language.label.en: en
    language.label.fr: fr
    language.label.it: it
```

#### Usage

- Use LchTranslate\Model\Behavior\Translatable in translatable
entities.
