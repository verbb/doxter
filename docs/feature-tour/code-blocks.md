# Code Blocks
Doxter gives you the flexibility to define code blocks in the _standard_ way or _fenced_. If you _fence_ your code blocks, you can also specify a language identifier. Additionally, you can tell Doxter exactly how to render your code blocks for easy integration with your syntax highlighter.

## Standard Code Blocks
Standard code blocks are defined by one or more levels levels of indentation to denote where the code block begins/ ends

````
    $greeting = 'Hello';

    echo $greeting;
````

## Fenced Code Blocks
Fenced code blocks use three or five backticks to denote where the code block begins/ ends. You can also append a language identifier to the first set of backticks.

````
```php
$greeting = 'Hello';

echo $greeting;
```
````

## Custom Block Template String
Since different syntax highlighters require slightly different markup in order to work, Doxter provides a way for you to define exactly how your code should be rendered. This is done by allowing you to define a _code block template string_ that uses the placeholders `{languageClass}` and `{sourceCode}`.

Here are a few _code block template string_ examples for the syntax highlighters I use most often. Prism is currently my favorite 👍

```html
<!-- HighlightJS -->
<pre><code class="{languageClass}">{sourceCode}</code></pre>

<!-- RainbowJS -->
<pre><code data-language="{languageClass}">{sourceCode}</code></pre>

<!-- PrismJS -->
<pre><code class="language-{languageClass}">{sourceCode}</code></pre>
```