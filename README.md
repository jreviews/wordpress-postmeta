# Easily Access JReviews Fields from the Post Object

This package can be installed in WordPress site using JReviews to easily allow access to JReviews fields from the $post object.

## Installation

[Download the jreviews_postmeta.zip plugin package](https://github.com/jreviews/wordpress-postmeta/releases/latest) and install using the WordPress plugins manager on your site.

## Usage

To get the value of a custom field:

```php
$value = $post->{'jreviews:jr_fieldname'};
```

If the field has multiple options, it will return array.

You can also retrieve the average user rating via:

```php
$rating = $post->{'jreviews:rating'};
```

## Limitations

- The plugin doesn't process output formatting settings within JReviews custom fields. 

- Supported custom fields include:
    - select
    - selectmultiple
    - radiobuttons
    - checkboxes
    - text
    - decimal
    - integer
    - date
    - relatedlisting
    - website
    - email

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
