# Parsing Options
Parsing options control how Doxter turns source text into HTML. Set shared defaults in the plugin configuration, choose field-specific behaviour when creating a field, or pass options for one rendering call. A per-call option is useful when the same content appears in more than one context.

For example, a full article might need linkable headings, while a short preview should omit those anchors. The examples below disable heading anchors and shortcode parsing for the current rendering call.

## Parsing Filter `doxter`

```twig
{% set options = {
    parseShortcodes: false,
    addHeaderAnchors: false
} %}

{{ '# Doxter Rules' | doxter(options) }}
```

## `doxterFieldHandle.html(options)`

```twig
{% set options = {
    parseShortcodes: false,
    addHeaderAnchors: false
} %}

{{ entry.doxterFieldHandle.html(options) }}
```

## Option Reference
| Option | Type | Default | Description
| - | - | - | -
| `codeBlockSnippet` | `string` | `''` | See [Fenced Code Blocks](docs:feature-tour/code-blocks).
| `addHeaderAnchors` | `boolean` | `true` | Whether to parse headers and add anchors for direct linking.
| `addHeaderAnchorsTo` | `array` | `[h1, h2, h3]` | Which headers to add anchors to if header parsing is enabled.
| `addTypographyStyles` | `bool` | `false` | Whether [Typography Styles](docs:feature-tour/typography) should be applied.
| `startingHeaderLevel` | `string` | `h1` | Which tag should be use for the initial header.
| `parseReferenceTags` | `boolean` | `true` | Whether [Reference Tags](docs:feature-tour/reference-tags) should be parsed.
| `parseShortcodes` | `boolean` | `true` | Whether Doxter supported shortcodes should be parsed.
