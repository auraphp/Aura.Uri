<?php

namespace Aura\Uri;

$dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR;
require $dir . 'Aura.Uri' . DIRECTORY_SEPARATOR . 'src.php';
require $dir . 'Aura.Web' . DIRECTORY_SEPARATOR . 'src.php';

use Aura\Web;


echo time() . PHP_EOL . PHP_EOL;


// Load from a Context object...
//$url = new Url(new Web\Context($GLOBALS));
$factory = new Url\Factory();
$url = $factory->newCurrent($GLOBALS);
print_r($url);

// now the $url properties are ...
//
// $url->scheme   => 'http'
// $url->host     => 'example.com'
// $url->path     => array('path', 'to', 'index.php', 'foo', 'bar')
// $url->format   => 'xml'
// $url->query    => array('baz' => 'dib')


// Load from a string...
//$url = new Url('http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');
$url = $factory->newInstance('http://anonymous:guest@example.com/path/to/index.php/foo/bar.xml?baz=dib#anchor');
print_r($url);

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


// modify a Url object

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
// we need to fetch the "full" URI.
$full_url = $url->get(true);

// the $full_uri string is:
// https://example.com/something/else/entirely/another.php?baz=zab&zim=gir#anchor
echo $full_url;

