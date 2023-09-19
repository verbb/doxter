# Shortcodes
Shortcodes are a _first-class_ citizen in Doxter. You can simply tell Doxter what shortcode tags you want to process and by which templates they should be rendered. Doxter will then parse the source and hand your template a `ShortcodeModel` for each of the shortcode tags processed.

This means that in addition to advanced markdown parsing, shortcodes are supported in cases where highly specialized markup is required, you want to have full control of the output, and you need to give your content editors an easy way to embed content.

## What are shortcodes?
A shortcode is a specific parsing rule that lets you do nifty things with very little effort. Shortcodes can embed videos and images or create output that would normally require lots of complicated, ugly code in just one line.

## Inline vs Block
There are two types of shortcode tags you can use: **Inline** and **Block**. 

Let’s start with an example of a **block tag**, a `quote` tag in this case:

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

Now let’s take a look at an **inline tag**.

As an **inline tag** example, we’ll use an `image` shortcode tag.

## Image Shortcode
This shortcode creates a fluid image from a plain image source URL or from an asset.

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

We’re not even saving many key strokes. However, this is just a simple example to illustrate that you provide simple shortcodes that can be processed and transformed into beautiful, hand-crafted html once rendered by Doxter.

## Video Shortcode
Here is another example; A shortcode that can be used to embed vimeo or youtube videos with ease.

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

## Bio Shortcode
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

## Add your own Shortcodes
To add new shortcodes, you simple create a file called `doxter.php` inside of your `config` directory and define a mapping of shortcode tag(s) to template. Templates should be given as paths relative to your `templates` directory.

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