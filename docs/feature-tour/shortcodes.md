# Shortcodes

Shortcodes let content editors insert project-specific components without writing their HTML. You map each shortcode name to a site template, and Doxter passes that template the shortcode’s parameters, content and `ShortcodeModel` when the field is rendered.

Inside a shortcode template, read editor-supplied parameters from `shortcode.params` and enclosed content from `shortcode.content`. Parameter values are still available as top-level variables for backwards compatibility, but that access style is deprecated. Treat every parameter as editor-authored data: escape it for its output context and use an allow-list whenever it selects an HTML element, attribute name or other structural value.

```twig
{% set src = shortcode.params.src ?? '' %}
{% set requestedWrapper = shortcode.params.wrapper ?? null %}
{% set wrapper = requestedWrapper in ['figure', 'div', 'p'] ? requestedWrapper : null %}
```

## What Are Shortcodes?

A shortcode is a compact tag that a developer connects to a Twig template. It can represent an image, video, quotation or another component whose markup should remain in the project’s templates rather than the editor’s content.

## Inline vs Block

Inline tags represent a component without enclosed content. Block tags wrap content that is passed to the shortcode template.

This block tag supplies a quotation and an `author` parameter:

```text
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

The `quote` example is illustrative. Create and map a project template for it before using the tag.

## Image Shortcode

This shortcode creates a fluid image from a plain image source URL or from an asset.

```text
[image src=/path/to/img.jpg fluid/]

- or -

[image src={asset:123:url} fluid/]
```

```html
<figure class="image">
    <img src="/path/to/img.jpg" alt="" class="fluid" />
</figure>
```

The editor stores the compact tag. The mapped template controls the resulting figure and image markup.

## Video Shortcode

The bundled video starter template supports Vimeo and YouTube tags. This example passes a Vimeo video identifier and colour to that template:

```text
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

Doxter purifies rendered HTML by default, but shortcode templates should still validate structural choices at their source. If a trusted shortcode template needs an iframe, add narrow allowances through [`purifierConfig`](docs:get-started/configuration#purifierconfig) rather than disabling purification.

## Bio Shortcode

You can combine shortcodes and reference tags to load content for a project-specific component. For example, a `bio` shortcode could accept a user reference:

```text
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

The `bio` example does not have a bundled starter template. Create the card template in your project and map the `bio` tag to it.

## Use the Starter Templates

Doxter includes starter templates for audio, image and video shortcodes under the package’s `src/templates/_shortcodes` directory. They are examples and are not exposed automatically as site templates. Copy the templates you want to use into `<project>/templates/_doxter/shortcodes`, then add the corresponding mappings to `<project>/config/doxter.php` as shown below.

After copying, render a page containing one of the mapped tags. The component should appear on the page. If the original shortcode text remains, check that the destination filename matches the mapping and that the template exists under the project’s `templates` directory.

## Add Your Own Shortcodes

To add or replace shortcode mappings, create `<project>/config/doxter.php`. Each key contains one or more shortcode names separated by colons, and each value is a template path relative to the project’s `templates` directory. This example retains the starter mappings and adds the project’s `bio` template:

```php
<?php

return [
    'shortcodes' => [
        'tags' => [
            'audio' => '_doxter/shortcodes/audio',
            'img:image' => '_doxter/shortcodes/image',
            'vimeo:youtube' => '_doxter/shortcodes/video',
            'bio' => '_doxter/shortcodes/bio',
        ],
    ],
];
```
