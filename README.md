Aura.Uri
========

Provides objects to help you create and manipulate URLs,
including query strings and path elements. It does so by splitting up the pieces
of the URL and allowing you modify them individually; you can then fetch
them as a single URL string. This helps when building complex links,
such as in a paged navigation system.

## Foreword

### Installation

This library requires PHP 5.4 or later; we recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

It is installable and autoloadable via Composer as [aura/uri](https://packagist.org/packages/aura/uri).

Alternatively, [download a release](https://github.com/auraphp/Aura.Uri/releases) or clone this repository, then require or include its _autoload.php_ file.

### Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/auraphp/Aura.Uri/badges/quality-score.png?b=develop-2)](https://scrutinizer-ci.com/g/auraphp/Aura.Uri/)
[![Code Coverage](https://scrutinizer-ci.com/g/auraphp/Aura.Uri/badges/coverage.png?b=develop-2)](https://scrutinizer-ci.com/g/auraphp/Aura.Uri/)
[![Build Status](https://travis-ci.org/auraphp/Aura.Uri.png?branch=develop-2)](https://travis-ci.org/auraphp/Aura.Uri)

To run the unit tests at the command line, issue `composer install` and then `phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`, and [PHPUnit](http://phpunit.de/manual/) to be available as `phpunit`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

### Community

To ask questions, provide feedback, or otherwise communicate with the Aura community, please join our [Google Group](http://groups.google.com/group/auraphp), follow [@auraphp on Twitter](http://twitter.com/auraphp), or chat with us on #auraphp on Freenode.


# Getting Started

## Instantiation

The easiest way to instantiate a URL object is with the _UrlFactory_:

```php
<?php
use Aura\Uri\Url\Factory;

$url_factory = new Factory;
?>
```

Use the factory to create a new URL; you can populate the URL properties from a
string:

```php
<?php
$string = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');
$url = $url_factory->newInstance($string);

// now the $url properties are ...
//
// $url->scheme    => 'http'
// $url->user      => 'anonymous'
// $url->pass      => 'guest'
// $url->host      => Aura\Uri\Host, with these methods:
//                      ->get()                     => 'example.com'
//                      ->getSubdomain()            => null
//                      ->getRegisterableDomain()   => 'example.com'
//                      ->getPublicSuffix()         => 'com'
// $url->port      => null
// $url->path      => Aura\Uri\Path, with these ArrayObject elements:
//                      ['path', 'to', 'index.php', 'foo', 'bar']
//                    and this method:
//                      ->getFormat() => '.xml'
// $url->query     => Aura\Uri\Query, with these ArrayObject elements:
//                      ['baz' => 'dib']
// $url->fragment  => 'anchor'
?>
```

Alternatively, you can use the factory to create a URL representing the
current web request URI:

```php
<?php
$url = $url_factory->newCurrent();
?>
```


## Manipulation

After we have created the URL object, we can modify the component parts, then
fetch a new URL string from the modified object.

```php
<?php
// start with a full URL
$string = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor';
$url = $url_factory->newInstance($string);

// change to 'https://'
$url->setScheme('https');

// remove the username and password
$url->setUser(null);
$url->setPass(null);

// change the value of 'baz' from 'dib' to 'zab'
$url->query->baz = 'zab';

// add a new query element called 'zim' with a value of 'gir'
$url->query->zim = 'gir';

// reset the path to something else entirely.
// this will additionally set the format to '.php'.
$url->path->setFromString('/something/else/entirely.php');

// add another path element
$url->path[] = 'another';

// get the url as a string; this will be without the scheme, host, port,
// user, or pass.
$new_url = $url->get();

// the $new_url string is as follows; notice how the format
// is always applied to the last path-element:
// /something/else/entirely/another.php?baz=zab&zim=gir#anchor

// get the full url string, including scheme, host, port, user, and pass.
$full_url = $url->getFull();

// the $full_url string is as follows:
// https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
?>
```

# Public Suffix List Host Parsing

## Host Component Parts

In addition to URL creation and manipulation, `Aura.Uri` is capable of parsing a
host into its component parts, namely the host's subdomain, registerable domain,
and public suffix. A host's component parts are available via properties on the
Aura.Uri host object, as seen in the examples above.

## Public Suffix List

This parsing capability is possible as a result of the [Public Suffix List][], a community
resource and initiative of Mozilla.

## Updating the Public Suffix List

As the Public Suffix List is both an external resource and a living document, it's
important that you update your copy of the list from time to time.  You can do this
by executing the provided `update-psl.php` script.

    php /path/to/Aura.Uri/scripts/update-psl.php

Executing `update-psl.php` will retrieve the most current version of the Public Suffix
List, parse it to an array, and store it in the `/path/to/Aura.Uri/data` directory.

[Public Suffix List]: http://publicsuffix.org/
