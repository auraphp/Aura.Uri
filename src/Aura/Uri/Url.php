<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Uri;

/**
 * 
 * Manipulates and generates URI strings.
 * 
 * @package Aura.Uri
 * 
 */
class Url
{
    /**
     * 
     * Collection point for configuration values.
     * 
     * @var array
     * 
     */
    protected $config = [
        'host' => null,
        'path' => '/',
        'uri'  => null,
    ];

    /**
     * 
     * The scheme (for example 'http' or 'https').
     * 
     * @var string
     * 
     */
    public $scheme = null;

    /**
     * 
     * The host specification (for example, 'example.com').
     * 
     * @var string
     * 
     */
    public $host = null;

    /**
     * 
     * The port number (for example, '80').
     * 
     * @var string
     * 
     */
    public $port = null;

    /**
     * 
     * The username, if any.
     * 
     * @var string
     * 
     */
    public $user = null;

    /**
     * 
     * The password, if any.
     * 
     * @var string
     * 
     */
    public $pass = null;

    /**
     * 
     * The path portion (for example, 'path/to/index.php').
     * 
     * @var array
     * 
     */
    public $path = null;

    /**
     * 
     * The dot-format extension of the last path element (for example, the "rss"
     * in "feed.rss").
     * 
     * @var string
     * 
     */
    public $format = null;

    /**
     * 
     * Contents of virtual $query property. Public access is allowed via
     * __get() with $query.
     * 
     * If you access this property, \Aura\Uri\Url treats \Aura\Uri\Url::$query as
     * authoritative, *not* the internal \Aura\Uri\Url::$query_str. If the
     * original query string did not follow PHP's query string rules, you may
     * lose data; however, for all other URIs this change should be
     * transparent. If you are emulating forms, be sure to set $query to an
     * empty array before adding elements.
     * 
     * Why do things this way? The reason is that parse_str() and
     * http_buildquery() may not return the query string *exactly* the way
     * it was set in the first place. E.g., "?foo&bar" will come back as
     * "?foo=&bar=", which may or may not be expected.
     * 
     * So instead, we **do not** parse the query string to an array until
     * the user attempts to manipulate the query elements. This guarantees
     * that if you don't examine or modify the query, you will get back
     * exactly what you put in.
     * 
     * If you examine or modify the query elements, though, that will invoke
     * parse_str() and http_build_query(), so you may not get back *exactly*
     * what you set in the first place. In almost every case this won't
     * matter.
     * 
     * Many thanks to Edward Z. Yang for this implementation.
     * 
     * @var array
     * 
     * @see $query_str
     * 
     * @see __set()
     * 
     * @see __get()
     * 
     * @see loadQuery()
     * 
     */
    protected $query = null;

    /**
     * 
     * The fragment portion (for example, the "foo" in "#foo").
     * 
     * @var string
     * 
     */
    public $fragment = null;

    /**
     * 
     * Internal "original" query string; if you examine or modify the $query
     * virtual property, this property is ignored when building the URI.
     * 
     * @var string
     * 
     * @see $query
     * 
     * @see __set()
     * 
     * @see __get()
     * 
     * @see loadQuery()
     * 
     */
    protected $query_str = null;

    /**
     * 
     * Url-encode only these characters in path elements.
     * 
     * Characters are ' ' (space), '/', '?', '&', and '#'.
     * 
     * @var array
     * 
     */
    protected $encode_path = array (
        ' ' => '+',
        '/' => '%2F',
        '?' => '%3F',
        '&' => '%26',
        '#' => '%23',
    );

    /**
     *
     * Default URL elements
     *
     * @var array
     *
     */
    protected $default_elements = [
        'scheme'   => null,
        'user'     => null,
        'pass'     => null,
        'host'     => null,
        'port'     => null,
        'path'     => null,
        'query'    => null,
        'fragment' => null,
    ];

    /**
     * 
     * Construction tasks
     * 
     * @return void
     * 
     */
    public function __construct($arg = null)
    {
        if ($arg instanceof \Aura\Web\Context) {
            $this->loadFromContextObject($arg);
        }
        elseif (is_string($arg) && strlen($arg) > 0) {
            $this->loadFromUrlString($arg);
        }
        elseif (is_array($arg) && count($arg) > 0) {
            $this->setConfig($arg);
        }
    }

    /** 
     * 
     * Converts the URI object to a string and returns it. 
     * 
     * @return string The full URI this object represents.
     * 
     */
    public function __toString()
    {
        return $this->get(true);
    }

    /**
     * 
     * Implements the virtual $query property.
     * 
     * @param string $key The virtual property to set.
     * 
     * @param string $val Set the virtual property to this value.
     * 
     * @return mixed The value of the virtual property.
     * 
     */
    public function __set($key, $val)
    {
        if ($key == 'query') {
            $this->query = $val;
        }
    }

    /**
     * 
     * Implements access to $query **by reference** so that it appears to be 
     * a public $query property.
     * 
     * @param string $key The virtual property to return.
     * 
     * @return array
     * 
     */
    public function &__get($key)
    {
        if ($key == 'query') {
            if (is_null($this->query)) {
                $this->loadQuery();
            }
            return $this->query;
        }
    }

    /**
     *
     * Sets the Url class configuration options by an array
     *
     * @var array $config Array of configuration options to set
     *
     * @return void
     *
     */
    public function setConfig(array $config)
    {
        // fix the base path by adding leading and trailing slashes
        if (trim($config['path']) == '') {
            $config['path'] = '/';
        }
        if ($config['path'][0] != '/') {
            $config['path'] = '/' . $config['path'];
        }
        $config['path'] = rtrim($config['path'], '/') . '/';

        // set properties
        $this->config = array_merge($this->config, $config);
    }

    /**
     *
     * Loads properties from an Aura\Web\Context object
     *
     * @var \Aura\Web\Context $context \Aura\Web\Context object to load
     *
     * @return void
     *
     */
    public function loadFromContextObject(\Aura\Web\Context $context)
    {
        // build a default scheme (with '://' in it)
        $scheme = $context->isSsl() ? 'https://' : 'http://';

        // get the current host, using the default host name if needed.
        $host = $context->getServer('HTTP_HOST', $this->config['host']);

        // add the scheme and host
        $uri = $scheme . $host;

        // we need to see if mod_rewrite is turned on or off.
        // if on, we can use REQUEST_URI as-is.
        // if off, we need to use the script name, esp. for
        // front-controller stuff.
        // we make a guess based on the 'path' config key.
        // if it ends in '.php' then we guess that mod_rewrite is
        // off.
        if (substr($this->config['path'], -5) == '.php/') {
            // guess that mod_rewrite is off; build up from
            // component parts.
            $uri .= $context->getServer('SCRIPT_NAME')
                  . $context->getServer('PATH_INFO')
                  . '?' . $context->getServer('QUERY_STRING');
        } else {
            // guess that mod_rewrite is on
            $uri .= $context->getServer('REQUEST_URI');
        }

        $this->loadFromUrlString($uri);
    }

    /**
     *
     * Loads properties by parsing an URL string
     *
     * @var string $url Url string to load
     *
     * @return void
     *
     */
    public function loadFromUrlString($url)
    {
        // parse the uri and merge with the defaults
        $elem = $this->default_elements;
        $elem = array_merge($elem, parse_url($url));

        // load object properties
        $this->loadFromArray($elem);
    }

    /**
     *
     * Pre-processes, post-processes, and sets class properties based
     * on an array from parse_url()
     *
     * @var array $elem Array of URL data to load
     *
     * @return void
     *
     */
    protected function loadFromArray(array $elem)
    {
        // strip the prefix from the path.
        // the conditions are ...
        // $elem['path'] == '/index.php/'
        // -- or --
        // $elem['path'] == '/index.php'
        // -- or --
        // $elem['path'] == '/index.php/*'
        //
        $path = $this->config['path'];
        $len  = strlen($path);
        $flag = $elem['path'] == $path ||
                $elem['path'] == rtrim($path, '/') ||
                substr($elem['path'], 0, $len) == $path;

        if ($flag) {
            $elem['path'] = substr($elem['path'], $len);
        }

        // retain parsed elements as properties
        $this->scheme   = $elem['scheme'];
        $this->user     = $elem['user'];
        $this->pass     = $elem['pass'];
        $this->host     = $elem['host'];
        $this->port     = $elem['port'];
        $this->fragment = $elem['fragment'];

        // extended processing of parsed elements into properties
        $this->setPath($elem['path']); // will also set $this->format
        $this->setQuery($elem['query']);

        // finally, if we don't have a host, and there's a default,
        // use it
        if (! $this->host && isset($this->config['host']) && ! empty($this->config['host'])) {
            $this->host = $this->config['host'];
        }
    }

    /**
     * 
     * Returns a URI based on the object properties.
     * 
     * @param bool $full If true, returns a full URI with scheme,
     * user, pass, host, and port. Otherwise, just returns the
     * path, format, query, and fragment. Default false.
     * 
     * @return string An action URI string.
     * 
     */
    public function get($full = false)
    {
        // the uri string
        $uri = '';

        // are we doing a full URI?
        if ($full) {

            // add the scheme, if any.
            $uri .= empty($this->scheme) ? '' : urlencode($this->scheme) . '://';

            // add the username and password, if any.
            if (! empty($this->user)) {
                $uri .= urlencode($this->user);
                if (! empty($this->pass)) {
                    $uri .= ':' . urlencode($this->pass);
                }
                $uri .= '@';
            }

            // add the host and port, if any.
            $uri .= (empty($this->host) ? '' : urlencode($this->host))
                  . (empty($this->port) ? '' : ':' . (int) $this->port);
        }

        // get the query as a string
        $query = $this->getQuery();

        // add the rest of the URI. we use trim() instead of empty() on string
        // elements to allow for string-zero values.
        return $uri . $this->getPath()
             . (empty($query)                ? '' : '?' . $query)
             . (trim($this->fragment) === '' ? '' : '#' . urlencode($this->fragment));
    }

    /**
     * 
     * Sets the query string in the URI, for Uri::getQuery() and Uri::$query.
     * 
     * This will overwrite any previous values.
     * 
     * @param string $spec The query string to use; for example,
     * `foo=bar&baz=dib`.
     * 
     * @return void
     * 
     */
    public function setQuery($spec)
    {
        // reset the origin value
        $this->query_str = $spec;

        // reset the parsed version
        $this->query = null;
    }

    /**
     * 
     * Returns the query portion as a string.  When [[Uri::$query | ]]
     * is non-null, uses [[php::http_buildquery() | ]] on it; otherwise, 
     * returns the [[Uri::$query_str]] property.
     * 
     * @return string The query string; e.g., `foo=bar&baz=dib`.
     * 
     */
    public function getQuery()
    {
        // check against the protected property, not the virtual public one,
        // to avoid __get() when it's not needed.
        if (is_array($this->query)) {
            return http_build_query($this->query);
        } else {
            return $this->query_str;
        }
    }

    /**
     * 
     * Sets the Uri::$path array from a string.
     * 
     * This will overwrite any previous values. Also, resets the format based
     * on the final path value.
     * 
     * @param string $spec The path string to use; for example,
     * "/foo/bar/baz/dib".  A leading slash will *not* create an empty
     * first element; if the string has a leading slash, it is ignored.
     * 
     * @return void
     * 
     */
    public function setPath($spec)
    {
        $spec = trim($spec, '/');

        $this->path = [];
        if (! empty($spec)) {
            $this->path = explode('/', $spec);
        }

        foreach ($this->path as $key => $val) {
            $this->path[$key] = urldecode($val);
        }

        $this->setFormatFromPath();
    }

    /**
     * 
     * Returns the path array as a string, including the format.
     * 
     * @return string The path string.
     * 
     */
    public function getPath()
    {
        // we use trim() instead of empty() on string elements
        // to allow for string-zero values.
        return $this->config['path']
             . (empty($this->path)         ? '' : $this->pathEncode($this->path))
             . (trim($this->format) === '' ? '' : '.' . urlencode($this->format));
    }

    /**
     * 
     * Loads $this->query with an array representation of $this->query_string
     * using [[php::parse_str() | ]].
     * 
     * @return void
     * 
     */
    protected function loadQuery()
    {
        // although the manual claims that setting magic_quotes_gpc at runtime
        // will have no effect since $_GET is already populated, it still
        // affects the behavior of parse_str().
        $old = get_magic_quotes_gpc();
        ini_set('magic_quotes_gpc', false);
        parse_str($this->query_str, $this->query);

        // reset to old behavior for consistency's sake.  There really should
        // be no reason for code to be relying on this.
        ini_set('magic_quotes_gpc', $old);
    }

    /**
     * 
     * Removes and stores any trailing .format extension of last path element.
     * 
     * @return void
     * 
     */
    protected function setFormatFromPath()
    {
        $this->format = null;
        $val = end($this->path);
        if ($val) {
            // find the last dot in the value
            $pos = strrpos($val, '.');
            if ($pos !== false) {
                $key = key($this->path);
                $this->format = substr($val, $pos + 1);
                $this->path[$key] = substr($val, 0, $pos);
            }
        }
    }

    /**
     * 
     * Converts an array of path elements into a string.
     * 
     * Does not use [[php::urlencode() | ]]; instead, only converts
     * characters found in Uri::$encode_path.
     * 
     * @param array $spec The path elements.
     * 
     * @return string A URI path string.
     * 
     */
    protected function pathEncode($spec)
    {
        if (is_string($spec)) {
            $spec = explode('/', $spec);
        }
        $keys = array_keys($this->encode_path);
        $vals = array_values($this->encode_path);
        $out = [];
        foreach ((array) $spec as $elem) {
            $out[] = str_replace($keys, $vals, $elem);
        }
        return implode('/', $out);
    }
}

