# Editor

Doxter stores Markdown in a field and renders it as HTML for your templates. Editors can write headings, links and other formatting without editing the surrounding page template.

Create a Doxter field with the handle `articleBody`, add it to an entry type's field layout and open an entry. Enter a short example:

```markdown
## Visiting the Studio

Our studio is open on **Saturday mornings**.

Bring a notebook and [book your place](/bookings) before arriving.
```

Save the entry. In its Twig template, output the field where the article should appear:

```twig
{{ entry.articleBody }}
```

The result contains a heading, paragraphs, bold text and a link. The site's CSS determines their appearance. You can also access `entry.articleBody.html` explicitly, or use `entry.articleBody.raw` when you need the original Markdown text.

## Parser

Doxter can also process [code blocks](docs:feature-tour/code-blocks), [reference tags](docs:feature-tour/reference-tags) and [shortcodes](docs:feature-tour/shortcodes). Configure the relevant [parsing options](docs:feature-tour/parsing-options) when your content needs those features. Start with the basic field output, then add one feature and inspect its rendered result.

## Typography

[Typography processing](docs:feature-tour/typography) changes punctuation such as straight quotes, repeated dots and dashes into their typographic forms. It affects the parsed output, so compare the original text and rendered page when choosing those options.
