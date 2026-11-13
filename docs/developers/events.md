# Events

Doxter provides events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to change Doxter’s behaviour.

Doxter triggers its parsing events in order each time it parses content. Every event’s `source` property holds the content as it stands at that point, and the next step uses whatever value the listener leaves in it. None of the events can cancel parsing.

## Parsing Events

### The `beforeReferenceTagParsing` Event
The event that is triggered before Doxter parses reference tags in the source, which happens first, so reference tags can contain Markdown. It’s only triggered when [parseReferenceTags](../get-started/configuration.md#parsereferencetags) is enabled.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_REFERENCETAG_PARSE, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `beforeShortcodeParsing` Event
The event that is triggered before Doxter parses shortcodes in the source. It’s only triggered when [parseShortcodes](../get-started/configuration.md#parseshortcodes) is enabled.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_SHORTCODE_PARSE, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `beforeMarkdownParsing` Event
The event that is triggered before Doxter converts the Markdown source to HTML.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_MARKDOWN_PARSE, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `beforeCodeBlockParsing` Event
The event that is triggered before Doxter renders code blocks, after the Markdown has been converted to HTML.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_CODEBLOCK_PARSE, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `beforeHeaderParsing` Event
The event that is triggered before Doxter adds anchors to headers in the HTML. It’s only triggered when [addHeaderAnchors](../get-started/configuration.md#addheaderanchors) is enabled.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_HEADER_PARSE, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `beforeTypography` Event
The event that is triggered before Doxter applies typography styles to the HTML. It’s only triggered when [addTypographyStyles](../get-started/configuration.md#addtypographystyles) is enabled.

Change `$event->source` to alter the content before this step runs.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_BEFORE_TYPOGRAPHY, function(DoxterEvent $event) {
    $source = $event->source;
    // ...
    $event->source = $source;
});
```

### The `afterParsing` Event
The event that is triggered after Doxter finishes parsing, before the HTML is purified and returned.

Change `$event->source` to alter the final HTML. Doxter purifies the listener’s result with [purifierConfig](../get-started/configuration.md#purifierconfig), unless [allowUnsafeHtml](../get-started/configuration.md#allowunsafehtml) is enabled, so a listener can’t add markup the purifier removes.

```php
use verbb\doxter\events\DoxterEvent;
use verbb\doxter\services\Service;
use yii\base\Event;

Event::on(Service::class, Service::EVENT_AFTER_PARSE, function(DoxterEvent $event) {
    $event->source = str_replace('<table>', '<table class="table">', $event->source);
});
```
