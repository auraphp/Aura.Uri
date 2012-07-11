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

The following is a simple example. Say that the page address is currently
`http://anonymous::guest@example.com/path/to/index.php/foo/bar?baz=dib#anchor`.

You can use Uri to parse this complex string very easily:

```php
// create a URI object; this will automatically import the current
// location, which is...
// 
// http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor
$uri = new \Aura\Uri\Url(new \Aura\Uri\Request);

// now the $uri properties are ...
// 
// $uri->scheme   => 'http'
// $uri->host     => 'example.com'
// $uri->user     => 'anonymous'
// $uri->pass     => 'guest'
// $uri->path     => array('path', 'to', 'index.php', 'foo', 'bar')
// $uri->format   => 'xml'
// $uri->query    => array('baz' => 'dib')
// $uri->fragment => 'anchor'
```

Manipulation
------------

Now that we have imported the URI and had it parsed automatically, we
can modify the component parts, then fetch a new URI string.

```php
// change to 'https://'
$uri->scheme = 'https';

// remove the username and password
$uri->user = '';
$uri->pass = '';

// change the value of 'baz' to 'zab'
$uri->setQuery('baz', 'zab');

// add a new query element called 'zim' with a value of 'gir'
$uri->query['zim'] = 'gir';

// reset the path to something else entirely.
// this will additionally set the format to 'php'.
$uri->setPath('/something/else/entirely.php');

// add another path element
$uri->path[] = 'another';

// and fetch it to a string.
$new_uri = $uri->get();

// the $new_uri string is as follows; notice how the format
// is always applied to the last path-element.
// /something/else/entirely/another.php?baz=zab&zim=gir#anchor

// wait, there's no scheme or host!
// we need to fetch the "full" URI.
$full_uri = $uri->get(true);

// the $full_uri string is:
// https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
```

Properties
==========

This class has a number of public properties, all related to
the parsed URI processed by [[Uri::set()]]. They are ...

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
