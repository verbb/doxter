# Parsing Options
Parsing option can be defined globally when defined in the plugin, per field, when the field is created or per call, when passed to a field method or the ` | doxter({})` filter.

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
| `codeBlockSnippet` | `string` | `''` | See [Fenced Code Blocks](#code-blocks).
| `addHeaderAnchors` | `boolean` | `true` | Whether to parse headers and add anchors for direct linking.
| `addHeaderAnchorsTo` | `array` | `[h1, h2, h3]` | Which headers to add anchors to if header parsing is enabled.
| `addTypographyStyles` | `bool` | `false` | Whether [Typography Styles](#typography) should be applied.
| `startingHeaderLevel` | `string` | `h1` | Which tag should be use for the initial header.
| `parseReferenceTags` | `boolean` | `true` | Whether [Reference Tags](#reference-tags) should be parsed.
| `parseShortcodes` | `boolean` | `true` | Whether Doxter supported shortcodes should be parsed.
