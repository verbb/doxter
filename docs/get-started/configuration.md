# Configuration

You can customise Doxter’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `doxter.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will disable automatic heading anchors:

```php
<?php

return [
    'addHeaderAnchors' => false,
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `shortcodes`

**Type:** `array` · **Default:** `[]`

A collection of shortcodes for the editor.
:::

::: reference
### `codeBlockSnippet`

**Type:** `string` · **Default:** `''`

Text to wrap code blocks for syntax highlighting.
:::

::: reference
### `allowUnsafeHtml`

**Type:** `bool` · **Default:** `false`

Whether Doxter should return parsed HTML without purification. Leave this disabled for content written by editors or received from front-end forms. Enable it only when every source string and shortcode parameter is controlled by a developer who is trusted to add executable markup to the site.
:::

::: reference
### `purifierConfig`

**Type:** `array` · **Default:** `['Attr.EnableID' => true]`

Configuration passed to Craft’s HTML purifier. Doxter enables HTML IDs so linkable headings continue to work. Add narrowly scoped allowances here when trusted shortcode templates need markup that the default purifier removes.

For example, this configuration permits Vimeo player iframes while continuing to purify the rest of the generated HTML:

```php
<?php

return [
    'purifierConfig' => [
        'HTML.SafeIframe' => true,
        'URI.SafeIframeRegexp' => '%^https://player.vimeo.com/video/%',
    ],
];
```
:::

::: reference
### `addHeaderAnchors`

**Type:** `bool` · **Default:** `true`

Whether to enable header anchor parsing.
:::

::: reference
### `addHeaderAnchorsTo`

**Type:** `array|null` · **Default:** `['h1', 'h2', 'h3']`

Set which headers to make linkable.
:::

::: reference
### `startingHeaderLevel`

**Type:** `int` · **Default:** `1`

Set the starting header level (as a number, 1-6).
:::

::: reference
### `addTypographyHyphenation`

**Type:** `bool` · **Default:** `true`

Whether to add typography hyphenation.
:::

::: reference
### `addTypographyStyles`

**Type:** `bool` · **Default:** `true`

Whether to add typography styles.
:::

::: reference
### `parseReferenceTags`

**Type:** `bool` · **Default:** `true`

Whether to parse reference tags.
:::

::: reference
### `parseShortcodes`

**Type:** `bool` · **Default:** `true`

Whether to parse shortcodes.
:::


## Control Panel
You can also manage the standard parsing settings through the Control Panel by visiting Settings → Doxter. The `allowUnsafeHtml` and `purifierConfig` security options are available only in `config/doxter.php` or as per-call parsing options, so changing the output trust policy remains a developer decision.
