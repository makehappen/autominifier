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
 - Auto detection of environment type

## Other features
 - It just works, out of the box
 - Set it and forget it
 - No complex configurations
 - No Node.js required
 - No "watch" command required
 - No "--production" flag required
 - No configuration updates needed when adding new files
 - Auto detect already minified files
 - Customize destination folder and file names
 - Faster than Gulp or Grunt

## Install

Via Composer

``` bash
$ composer require makehappen/autominifier
```

## Basic Usage

``` php
// minifier instance with default settings
$minifier = new Makehappen\AutoMinifier\Minify();

// returns "/js/app.min.js"
$minifier->js();

// returns "/css/app.min.css"
$minifier->css();
```

Post install:

- Add env.json files to .gitignore

- Update environment in env.json to development
``` json
{
    "environment": "development"
}
```

- Run minifier instance again to generate .min files


## Customized Usage

Custom folders and .min files: 
``` php
// minifier instance with public path relative to package src folder path
$minifier = new Makehappen\AutoMinifier\Minify();
$minifier->setPublicFolder('/../../../../public_html/');

// returns .min.js file path with custom destinations
$minifier->js('/javascript', 'functions.min.js');

// returns .min.css file path path with custom destinations
$minifier->js('/styles', 'style.min.css');
```

Custom list and order of files: config.json
```json
{
    "files": [
        "file-1.js",
        "folder2/file-2.js"
    ]
}
```

## Conventions
 - By default, JavaScript files should live in the public /js folder or its sub-folders
 - By default, CSS files should live in the public /css folder or its sub-folders
 - Default concatenation order is alphabetical, folders first, followed by file names
 - Default relative path from vendor folder to public folder is expected to be ../public
 - Accepted extension files: .js, .css, .sass, .scss

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

[ico-version]: https://img.shields.io/packagist/v/makehappen/autominifier.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/makehappen/autominifier/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/makehappen/autominifier.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/makehappen/autominifier.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/makehappen/autominifier.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/makehappen/autominifier
[link-travis]: https://travis-ci.org/makehappen/autominifier
[link-scrutinizer]: https://scrutinizer-ci.com/g/makehappen/autominifier/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/makehappen/autominifier
[link-downloads]: https://packagist.org/packages/makehappen/autominifier
[link-author]: https://github.com/makehappen
[link-contributors]: ../../contributors
