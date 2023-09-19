# Filters
Not using the Doxter field? No problem.

When you install Doxter, you get two filters that you get to use without need to create a field to store your content first.

You can use these two filters (`doxter` and `doxterTypography`) on any variable or string in your templates.

## Parsing Filter `doxter`
Doxter provides a filter that you can use to parse markdown in plain text fields or any other string that contains valid markdown, regardless of where it comes from.

Craft already provides a markdown filter that you can use it like this:

```twig
{{ '# Markdown Rules' | markdown }}
```

You can also use the shorter version: ` | md`.

However, Doxter goes beyond simple markdown parsing. It also provides support for _Reference Tags_, _Linkable Headers_, _Shortcodes_, and a few other hidden gems😉

You can use the Doxter filter like this:

```twig
{{ '# Doxter Rules' | doxter }}
```

Because Doxter does more than just parse markdown, you have the ability to pass in an options object.

```twig
{% set options = {
    parseShortcodes: false,
    addHeaderAnchors: false
} %}

{{ '# Doxter Rules' | doxter(options) }}
```

## Typography Filter `doxterTypography`
If you want to get the benefit of advanced markdown parsing and also have a nice markdown field in the control panel, then the Doxter field is what you want.

Once you create a Doxter field and add it to your section, you’ll be able to get the rendered html like this:

```twig
{{ entry.doxterFieldHandle }}
{# or #}
{{ entry.doxterFieldHandle.html }}
```

If you want to get back exactly what you typed into the editor without modification, you can use:

```twig
{{ entry.doxterFieldHandle.raw }}
```
