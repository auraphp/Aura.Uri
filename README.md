Aura URI
========

The Auri URI package provides objects to help you create and manipulate URIs,
including query strings and path elements. It does so by splitting up the pieces
of the URI and allowing you modify them individually; you can then then fetch
them as a single URI string. This helps when building complex links,
such as in a paged navigation system.

Getting Started
===============

Instantiation
-------------

When loading a Url object, you can populate the object properties from a URL string:

```php
use Aura\Uri;

$url = new Uri\Url();

// Load from a string...
$url->loadFromUrlString('http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');

// now the $url properties are ...
// 
// $url->scheme   => 'http'
// $url->host     => 'example.com'
// $url->user     => 'anonymous'
// $url->pass     => 'guest'
// $url->path     => array('path', 'to', 'index.php', 'foo', 'bar')
// $url->format   => 'xml'
// $url->query    => array('baz' => 'dib')
// $url->fragment => 'anchor'
```

Alternately, Url can parse Context objects from Aura\Web.
Suppose that the page address is currently
`http://anonymous::guest@example.com/path/to/index.php/foo/bar?baz=dib#anchor`:

```php
use Aura\Uri;
use Aura\Web;

$url = new Uri\Url();

// Parse an Aura\Web\Context object...
$url->loadFromContextObject(new Web\Context($GLOBALS));

// now the $url properties are ...
// 
// $url->scheme   => 'http'
// $url->host     => 'example.com'
// $url->path     => array('path', 'to', 'index.php', 'foo', 'bar')
// $url->format   => 'xml'
// $url->query    => array('baz' => 'dib')
```

You can pass either of these directly to the constructor, if you wish:

```php
use Aura\Uri;
use Aura\Web;

$url_from_string  = new Uri\Url('http://anonymous::guest@example.com/path/to/index.php/foo/bar?baz=dib#anchor');
$url_from_context = new Uri\Url(new Web\Context($GLOBALS));
```

Manipulation
------------

Now that we have imported the URI and had it parsed automatically, we
can modify the component parts, then fetch a new URI string.

```php
// change to 'https://'
$url->scheme = 'https';

// remove the username and password
$url->user = '';
$url->pass = '';

// change the value of 'baz' to 'zab'
$url->setQuery('baz', 'zab');

// add a new query element called 'zim' with a value of 'gir'
$url->query['zim'] = 'gir';

// reset the path to something else entirely.
// this will additionally set the format to 'php'.
$url->setPath('/something/else/entirely.php');

// add another path element
$url->path[] = 'another';

// and fetch it to a string.
$new_uri = $url->get();

// the $new_uri string is as follows; notice how the format
// is always applied to the last path-element.
// /something/else/entirely/another.php?baz=zab&zim=gir#anchor

// wait, there's no scheme or host!
// we need to fetch the "full" URL.
$full_url = $url->get(true);

// the $full_url string is:
// https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
```

Properties
==========

This class has a number of public properties, all related to
the parsed URI processed by [[Uri::loadFromUrlString()]] and
[[Uri::loadFromContextObject()]]. They are ...

| Name       | Type    | Description
| ---------- | ------- | --------------------------------------------------------------
| `scheme`   | string  | The scheme protocol; e.g.: http, https, ftp, mailto
| `host`     | string  | The host name; e.g.: example.com
| `port`     | string  | The port number
| `user`     | string  | The username for the URI
| `pass`     | string  | The password for the URI
| `path`     | array   | A sequential array of the path elements
| `format`   | string  | The filename-extension indicating the file format
| `query`    | array   | An associative array of the query terms
| `fragment` | string  | The anchor or page fragment being addressed

As an example, the following URI would parse into these properties:

    http://anonymous:guest@example.com:8080/foo/bar.xml?baz=dib#anchor
    
    scheme   => 'http'
    host     => 'example.com'
    port     => '8080'
    user     => 'anonymous'
    pass     => 'guest'
    path     => array('foo', 'bar')
    format   => 'xml'
    query    => array('baz' => 'dib')
    fragment => 'anchor'
