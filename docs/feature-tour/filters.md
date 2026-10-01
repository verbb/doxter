# Filters
Use Doxter’s filters when your Markdown comes from a string or a plain text field.

When you install Doxter, you get two filters that you get to use without need to create a field to store your content first.

You can use these two filters (`doxter` and `doxterTypography`) on any variable or string in your templates.

## Parsing Filter `doxter`
Doxter provides a filter that you can use to parse markdown in plain text fields or any other string that contains valid markdown, regardless of where it comes from.

Doxter purifies the generated HTML by default, removing executable markup and unsafe link schemes while preserving ordinary Markdown output. If a developer-controlled string deliberately contains trusted HTML, you can opt out for that call:

```twig
{{ trustedMarkdown | doxter({ allowUnsafeHtml: true }) }}
```

Do not disable purification for content written by editors, site visitors, or other untrusted sources. Use the [`purifierConfig`](docs:get-started/configuration#purifierconfig) setting when a trusted component needs a narrowly scoped HTML allowance instead.

Craft already provides a markdown filter that you can use it like this:

```twig
{{ '# Markdown Rules' | markdown }}
```

You can also use the shorter version: ` | md`.

Doxter also supports reference tags, linkable headers and shortcodes.

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

## Rendering a Doxter Field
A Doxter field stores the Markdown alongside access to its parsed output.

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

## Typography Filter `doxterTypography`

To process punctuation in an existing string, use the typography filter:

```twig
{{ 'The studio is open -- bring a notebook...' | doxterTypography }}
```

The typography filter follows the same HTML purification policy as the main `doxter` filter. For a developer-controlled string that deliberately contains trusted HTML, you can opt out for that call:

```twig
{{ trustedString | doxterTypography({ allowUnsafeHtml: true }) }}
```

Use the `doxter` filter when you also need Markdown structure such as headings and lists.
