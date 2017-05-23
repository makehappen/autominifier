# autominifier

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


Automatically Minify and Concatenate your JS and CSS files and libraries into single files for improved application performance.

## Main features
 - Auto minify and concatenate js and css libraries into single .min files
 - Auto cache bust on changed versions
 - Auto correction of relative paths for css assets
 - Auto detection of development environments

## Other features
 - It just works, out of the box
 - Set it and forget it
 - No complex configurations
 - No Node.js required
 - No "watch" command required
 - No "--production" flag required
 - No configuration updates needed when adding new files
 - Auto detect already minified files
 - Optional config allows for custom list and order of files
 - Faster than Gulp or Grunt

## Install

Via Composer

``` bash
$ composer require makehappen/autominifier
```

## Usage

JS
``` php
<?= new Makehappen\AutoMinifier\Minify()->js(); ?>
// will result in "/js/app.min.js" followed by a cache bust id
```

CSS
``` php
<?= new Makehappen\AutoMinifier\Minify()->css(); ?>
// will result in "/css/app.min.css" followed by a cache bust id
```

Custom folder and/or file name
``` php
<?= new Makehappen\AutoMinifier\Minify()->js('/javascript', 'my-app.min.js'); ?>
// will result in "/javascript/my-app.min.js" followed by a cache bust id
```

## Conventions
 - By default, JavaScript files should live in the public /js folder or its sub-folders
 - By default, CSS files should live in the public /css folder or its sub-folders
 - The relative path from vendor folder to public folder should be ../public
 - Accepted extension files: .js, .css, .sass, .scss
 - Already minified files should have .min before their extensions
 - Only files intended to be included in the "app.min." files should live in these folders
 - Concatenation order is alphabetical, folders first, followed by file names
 - Development environments should end with ".dev" or be "localhost"
 - The package will use or create a "storage" folder located one level up from /vendor


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email florin@after5.io instead of using the issue tracker.

## Credits

- [Florin Ilie][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/makehappen/minifier.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/makehappen/minifier/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/makehappen/minifier.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/makehappen/minifier.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/makehappen/minifier.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/makehappen/minifier
[link-travis]: https://travis-ci.org/makehappen/minifier
[link-scrutinizer]: https://scrutinizer-ci.com/g/makehappen/minifier/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/makehappen/minifier
[link-downloads]: https://packagist.org/packages/makehappen/minifier
[link-author]: https://github.com/makehappen
[link-contributors]: ../../contributors
