# Doxter plugin for Craft CMS
Slick Markdown Editor and Smart Text Parser for Craft CMS.

## Features
- Live Preview Support
- Fast and consistent Github Flavored Markdown Parsing
- **Linkable Headers** via named anchors
- Reference Tags parsing
- Extensible **Shortcode** parsing support
- Event driven parsing API for developers
- Advanced fenced code block parsing
- Support for typography styles for better web typography
- Emoji 🎉

## Installation
You can install Doxter via the plugin store, or through Composer.

### Craft Plugin Store
To install **Doxter**, navigate to the _Plugin Store_ section of your Craft control panel, search for `Doxter`, and click the _Try_ button.

### Composer
You can also add the package to your project using Composer and the command line.

1. Open your terminal and go to your Craft project:
```shell
cd /path/to/project
```

2. Then tell Composer to require the plugin, and Craft to install it:
```shell
composer require verbb/doxter && php craft plugin/install doxter
```

## Markdown
You may already know that [Craft](https://craftcms.com) ships with a markdown parser, which you can use in **Twig** like this:

```twig
{% set markdownString = "# Craft can parse markdown text" %}

{{ markdownString | md }}

{# or #}

{{ markdownString | markdown }}
```

So, if all you want is to parse a markdown string or markdown content in a plain text field, you don’t need Doxter.

If, on the other hand, a lot of your content is going to be driven by markdown, read on.

### Editor
The Doxter editor was designed to be used by developers and content editors alike. It attempts to make markdown more accessible to those who are less technical or haven’t written markdown before.

It also provides deep integration with Craft and the CP.

### Parser
The Doxter parser is more than a parser, it’s actually a set of parsers that work together to provide an incredibly smart conversion.

Markdown is now a first-class citizen in Craft CMS, but beyond that, Doxter makes working with Code Blocks and Shortcodes a joy.

## Typography
Here are a few of the styles that Doxter applies to your content:

- Straight quotes into ​“curly” quote HTML entities
- Backtick style quotes into ​“curly” quote HTML entities
- Dashes into **en-dash** and **em-dash** entities
- Three consecutive dots into an ellipsis entity
- French guillemets into true « guillemets » HTML entities.
- Comma-style quotes into their curly equivalent.
- Replace existing spaces with non-break where appropriate

### Usage
To apply typography styles, make sure to enable `addTypographyStyles` from plugin settings page. That will tell Doxter to apply typography styles to all your content inside Doxter fields, when rendered.

Alternatively, see the following section on **filters**

### Filters
If the content you want to convert is not stored in a Doxter field, you can use one of the provided filters for on the fly conversion.

```twig
{% set markdownString = "# Better typography out of the box" %}
{{ markdownString | doxter( { addTypographyStyles: true ) }}

{% set plainText = "I'm in love with typography. --Selvin Ortiz" %}
{{ plainText | doxterTypography() }}
```

## Shortcodes
Shortcodes are a _first-class_ citizen in Doxter. You can simply tell Doxter what shortcode tags you want to process and by which templates they should be rendered. Doxter will then parse the source and hand your template a `ShortcodeModel` for each of the shortcode tags processed.

This means that in addition to advanced markdown parsing, shortcodes are supported in cases where highly specialized markup is required, you want to have full control of the output, and you need to give your content editors an easy way to embed content.

### What are shortcodes?
A shortcode is a specific parsing rule that lets you do nifty things with very little effort. Shortcodes can embed videos and images or create output that would normally require lots of complicated, ugly code in just one line.

### Inline vs Block
There are two types of shortcode tags you can use: **Inline** and **Block**. 

Let’s start with an example of a **block tag**, a `quote` tag in this case:

```
[quote author="Harold Abelson"]
    Programs must be written for people to read, and only incidentally for machines to execute
[/quote]
```

```html
<blockquote>
    <p>
        Programs must be written for people to read, and only incidentally for machines to execute<br>
        -Harold Abelson
    </p>
</blockquote>
```

Think of **block tags** as the equivalent to `<divs>`. 

Now let’s take a look at an **inline tag**.

As an **inline tag** example, we’ll use an `image` shortcode tag.

### Image Shortcode
This shortcode creates a fluid image from a plain image source URL or from an asset.

```
[image src=/path/to/img.jpg fluid/]

- or -

[image src={asset:123:url} fluid/]
```

```html
<figure class="image">
    <img src="/path/to/img.jpg" alt="" class="fluid" />
</p>
```

We’re not even saving many key strokes. However, this is just a simple example to illustrate that you provide simple shortcodes that can be processed and transformed into beautiful, hand-crafted html once rendered by Doxter.

### Video Shortcode
Here is another example; A shortcode that can be used to embed vimeo or youtube videos with ease.

```
[vimeo src=213152344 color=333/]
```

```html
<iframe
    width="560"
    height="315"
    src="https://player.vimeo.com/video/213152344..."
    frameborder="0"
    webkitallowfullscreen
    mozallowfullscreen
    allowfullscreen>
</iframe>
```

Things get even more interesting when you couple the power of shortcodes, markdown, and reference tags. Here is an example `[bio]` shortcode that you could create by combining the above mentioned features of Doxter.

### Bio Shortcode
```
[bio user={user:123}/]
```

```html
<div class="card">
    <div class="card-image">
        <figure class="image is-4by3">
            <img src="path/to/cover.jpg" alt="">
        </figure>
    </div>
    
    <div class="card-content">
        <div class="media">
            <div class="media-left">
                <figure class="image is-48x48">
                    <img src="path/to/photo.jpg" alt="">
                </figure>
            </div>
            
            <div class="media-content">
                <p class="title is-4">John Smith</p>
                <p class="subtitle is-6">@johnsmith</p>
            </div>
        </div>

        <div class="content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br>
            <time datetime="2019-1-1">11:09 PM - 1 Jan 2019</time>
        </div>
    </div>
</div>
```

### Add your own Shortcodes
To add new shortcodes, you simple create a file called `doxter.php` inside of your `config` directory and define a mapping of shortcode tag(s) to template. Templates should be given as paths relative to your `templates` directory.

```php
<?php

return [
    'shortcodes' => [
        'tags' => [
            'bio' => '_shortcodes/bio',
            'img:image' => '_shortcodes/image',
            'vimeo:youtube' => '_shortcodes/video',
        ]
    ]
];
```

## Code Blocks
Doxter gives you the flexibility to define code blocks in the _standard_ way or _fenced_. If you _fence_ your code blocks, you can also specify a language identifier. Additionally, you can tell Doxter exactly how to render your code blocks for easy integration with your syntax highlighter.

### Standard Code Blocks
Standard code blocks are defined by one or more levels levels of indentation to denote where the code block begins/​ends

````
    $greeting = 'Hello';

    echo $greeting;
````

### Fenced Code Blocks
Fenced code blocks use three or five backticks to denote where the code block begins/​ends. You can also append a language identifier to the first set of backticks.

````
```php
$greeting = 'Hello';

echo $greeting;
```
````

### Custom Block Template String
Since different syntax highlighters require slightly different markup in order to work, Doxter provides a way for you to define exactly how your code should be rendered. This is done by allowing you to define a _code block template string_ that uses the placeholders `{languageClass}` and `{sourceCode}`.

Here are a few _code block template string_ examples for the syntax highlighters I use most often. Prism is currently my favorite 👍

```html
<!-- HighlightJS -->
<pre><code class="{languageClass}">{sourceCode}</code></pre>

<!-- RainbowJS -->
<pre><code data-language="{languageClass}">{sourceCode}</code></pre>

<!-- PrismJS -->
<pre><code class="language-{languageClass}">{sourceCode}</code></pre>
```

## Table of Contents
A _​“bring your own html”_ flat structure to create links to important sections in your document

### How to use
Table of contents are currently part of the Doxter field API. That means that you won’t be able to take adventage of table of contents unless you’re using the Doxter Field.

To generate a table of contents for your document, use the `toc` method available in your Doxter field.

Here is a quick example of how you could use the generated table of contents for your sidebar.

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

### TOC Model
Each item in the table of contents is an instance of `TocModel`.

**Public Properties**
- `id (string)`
- `text (string)`
- `level (int)`

**Accessor Methods**
- `getUid (string)`
- `getHash (string)`

## Reference Tags
No more stale links to other entries because someone changed the slug

Reference tags are similar to **shortcodes** in that they are short snippets that return dynamic content. For reference tags, the content comes from **Craft Elements** such as users, entries, categories, etc.

The Craft docs for [Reference Tags](http://buildwithcraft.com/docs/reference-tags) gives a pretty in-depth explanation of what they are and how to use them.

Doxter adheres to Craft’s parsing rules when processing reference tags and they get parsed before the markdown gets parsed, allowing for some pretty awesome functionality.

## Linkable Headers
If you’re using Doxter to write documentation or long-form content, this is a must.

Having the ability to link directly a specific section within an article is very important for easy to navigate documentation.

This is what linkable headers do. 

For every header level you consider important, Doxter can make sure that it gets a named anchor so that you can link to it directly.

## Parsing Options
Parsing option can be defined globally when defined in the plugin, per field, when the field is created or per call, when passed to a field method or the ` | doxter({})` filter.

### Parsing Filter `doxter`

```twig
{% set options = {
    parseShortcodes: false,
    addHeaderAnchors: false
} %}

{{ '# Doxter Rules' | doxter(options) }}
```

### `doxterFieldHandle.html(options)`

```twig
{% set options = {
    parseShortcodes: false,
    addHeaderAnchors: false
} %}

{{ entry.doxterFieldHandle.html(options) }}
```

### Option Reference
| Option | Type | Default | Description
| - | - | - | -
| `codeBlockSnippet` | `string` | `''` | See [Fenced Code Blocks](#code-blocks).
| `addHeaderAnchors` | `boolean` | `true` | Whether to parse headers and add anchors for direct linking.
| `addHeaderAnchorsTo` | `array` | `[h1, h2, h3]` | Which headers to add anchors to if header parsing is enabled.
| `addTypographyStyles` | `bool` | `false` | Whether [Typography Styles](#typography) should be applied.
| `startingHeaderLevel` | `string` | `h1` | Which tag should be use for the initial header.
| `parseReferenceTags` | `boolean` | `true` | Whether [Reference Tags](#reference-tags) should be parsed.
| `parseShortcodes` | `boolean` | `true` | Whether Doxter supported shortcodes should be parsed.

## Filters
Not using the Doxter field? No problem.

When you install Doxter, you get two filters that you get to use without need to create a field to store your content first.

You can use these two filters (`doxter` and `doxterTypography`) on any variable or string in your templates.

### Parsing Filter `doxter`
Doxter provides a filter that you can use to parse markdown in plain text fields or any other string that contains valid markdown, regardless of where it comes from.

Craft already provides a markdown filter that you can use it like this:

```twig
{{ '# Markdown Rules' | markdown }}
```

You can also use the shorter version: ` | md`.

However, Doxter goes beyond simple markdown parsing. It also provides support for _Reference Tags_, _Linkable Headers_, _Shortcodes_, and a few other hidden gems😉

You can use the Doxter filter like this:

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

### Typography Filter `doxterTypography`
If you want to get the benefit of advanced markdown parsing and also have a nice markdown field in the control panel, then the Doxter field is what you want.

Once you create a Doxter field and add it to your section, you’ll be able to get the rendered html like this:

```twig
{{ entry.doxterFieldHandle }}
{# or #}
{{ entry.doxterFieldHandle.html }}
```

If you want to get back exactly what you typed into the editor without modification, you can use:

```twig
{{ entry.doxterFieldHandle.raw }}
```

## Credits
Originally created by [Selvin Ortiz](https://github.com/selvindev).

## Show your Support
Doxter is licensed under the MIT license, meaning it will always be free and open source – we love free stuff! If you'd like to show your support to the plugin regardless, [Sponsor](https://github.com/sponsors/verbb) development.

<h2></h2>

<a href="https://verbb.io" target="_blank">
    <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>
