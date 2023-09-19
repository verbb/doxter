# Typography
Here are a few of the styles that Doxter applies to your content:

- Straight quotes into “curly” quote HTML entities
- Backtick style quotes into “curly” quote HTML entities
- Dashes into **en-dash** and **em-dash** entities
- Three consecutive dots into an ellipsis entity
- French guillemets into true « guillemets » HTML entities.
- Comma-style quotes into their curly equivalent.
- Replace existing spaces with non-break where appropriate

## Usage
To apply typography styles, make sure to enable `addTypographyStyles` from plugin settings page. That will tell Doxter to apply typography styles to all your content inside Doxter fields, when rendered.

Alternatively, see the following section on **filters**

## Filters
If the content you want to convert is not stored in a Doxter field, you can use one of the provided filters for on the fly conversion.

```twig
{% set markdownString = "# Better typography out of the box" %}
{{ markdownString | doxter( { addTypographyStyles: true ) }}

{% set plainText = "I'm in love with typography. --Selvin Ortiz" %}
{{ plainText | doxterTypography() }}
```