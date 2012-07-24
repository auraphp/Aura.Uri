Aura.Uri
========

The `Auri.Uri` package provides objects to help you create and manipulate URLs,
including query strings and path elements. It does so by splitting up the pieces
of the URL and allowing you modify them individually; you can then then fetch
them as a single URL string. This helps when building complex links,
such as in a paged navigation system.

Getting Started
===============

Instantiation
-------------

The easiest way to instantiate a URL object is to use the factory instance
script, like so:

```php
<?php
$url_factory = require '/path/to/Aura.Uri/scripts/instance.php';
$url = $url_factory->newInstance();
```

Alternatively, you can add the `src/` directory to your autoloader and
instantiate a URL factory object:

```php
<?php
use Aura\Uri\Url\Factory as UrlFactory;

$url_factory = new UrlFactory($_SERVER);
$url = $url_factory->newInstance();
```

When using the factory, you can populate the URL properties from a URL
string:

```php
<?php
$string = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');
$url = $url_factory->newInstance($string);

// now the $url properties are ...
// 
// $url->scheme   => 'http'
// $url->host     => 'example.com'
// $url->user     => 'anonymous'
// $url->pass     => 'guest'
// $url->path     => ArrayObject(['path', 'to', 'index.php', 'foo', 'bar'])
// $url->format   => '.xml'
// $url->query    => ArrayObject(['baz' => 'dib'])
// $url->fragment => 'anchor'
```

Alternatively, you can use the factory to create a URL representing the
current web request URI:

```php
<?php
$url = $url_factory->newCurrent();
```


Manipulation
------------

After we have created the URL object, we can modify the component parts, then
fetch a new URL string from the modified object.

```php
<?php
// start with a full URL
$string = 'http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');
$url = $url_factory->newCurrent();

// change to 'https://'
$url->setScheme('https');

// remove the username and password
$url->setUser(null);
$url->setPass(null);

// change the value of 'baz' to 'zab'
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
```

* * *
