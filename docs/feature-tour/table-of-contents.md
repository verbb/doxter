# Table of Contents
A _ “bring your own html”_ flat structure to create links to important sections in your document

## How to use
Table of contents are currently part of the Doxter field API. That means that you won’t be able to take adventage of table of contents unless you’re using the Doxter Field.

To generate a table of contents for your document, use the `toc` method available in your Doxter field.

Here is a quick example of how you could use the generated table of contents for your sidebar.

```twig
{% set tableOfContents = entry.doxterFieldHandle.toc %}

{% set sidebarContent %}
    <ul>
        {% for item in tableOfContents %}
            <li>
                <a href="{{ item.hash }}">{{ item.text }}</a>
            </li>
        {% endfor %}
    </ul>
{% endset %}
```

## TOC Model
Each item in the table of contents is an instance of `TocModel`.

**Public Properties**
- `id (string)`
- `text (string)`
- `level (int)`

**Accessor Methods**
- `getUid (string)`
- `getHash (string)`

