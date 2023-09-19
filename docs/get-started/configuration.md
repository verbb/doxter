# Configuration
Create a `doxter.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Doxter, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'shortcodes' => [],
        'codeBlockSnippet' => '',
        'addHeaderAnchors' => true,
        'addHeaderAnchorsTo' => ['h1', 'h2', 'h3'],
        'startingHeaderLevel' => 1,
        'addTypographyHyphenation' => true,
        'addTypographyStyles' => true,
        'parseReferenceTags' => true,
        'parseShortcodes' => true,
    ]
];
```

## Configuration options
- `shortcodes` - A collection of shortcodes for the editor.
- `codeBlockSnippet` - Text to wrap code blocks for syntax highlighting.
- `addHeaderAnchors` - Whether to enable header anchor parsing.
- `addHeaderAnchorsTo` - Set which headers to make linkable.
- `startingHeaderLevel` - Set the starting header level (as a number, 1-6).
- `addTypographyHyphenation` - Whether to add typography hyphenation.
- `addTypographyStyles` - Whether to add typography styles.
- `parseReferenceTags` - Whether to parse reference tags.
- `parseShortcodes` - Whether to parse shortcodes.

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Doxter.
