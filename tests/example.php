<?php
namespace Aura\Uri;
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src.php';

echo time() . PHP_EOL . PHP_EOL;

// create a URI object; this will automatically import the current
// location, which is...
// 
// http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor
$uri = new Url(new Request);

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
print_r($uri);


echo PHP_EOL . PHP_EOL;


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
echo $full_uri;

