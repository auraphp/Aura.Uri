<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Uri
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Uri;

/**
 * 
 * Manipulates and generates URLs.
 * 
 * @package Aura.Uri
 * 
 */
class Url
{
    /**
     * 
     * The scheme (for example 'http' or 'https').
     * 
     * @var string
     * 
     */
    protected $scheme;

    /**
     * 
     * The username, if any.
     * 
     * @var string
     * 
     */
    protected $user;

    /**
     * 
     * The password, if any.
     * 
     * @var string
     * 
     */
    protected $pass;

    /**
     * 
     * The host specification (for example, 'example.com').
     * 
     * @var string
     * 
     */
    protected $host;

    /**
     * 
     * The port number (for example, '80').
     * 
     * @var string
     * 
     */
    protected $port;

    /**
     * 
     * A Path object.
     * 
     * @var Path
     * 
     */
    protected $path;

    /**
     * 
     * A Query object.
     * 
     * @var Query
     * 
     */
    protected $query;

    /**
     * 
     * The fragment portion (for example, the "foo" in "#foo").
     * 
     * @var string
     * 
     */
    protected $fragment;

    // authority = userinfo@host:port

    /**
     * 
     * Constructor.
     * 
     * @param string $scheme The URL scheme (e.g. `http`).
     * 
     * @param string $user The username.
     * 
     * @param string $pass The password.
     * 
     * @param string $host The hostname.
     * 
     * @param int $port The port number.
     * 
     * @param Path $path The path elements, including format.
     * 
     * @param Query $query The query elements.
     * 
     * @param string $fragment The fragment.
     * 
     */
    public function __construct(
        $scheme,
        $user,
        $pass,
        $host,
        $port,
        Path $path,
        Query $query,
        $fragment
    ) {
        $this->scheme   = $scheme;
        $this->user     = $user;
        $this->pass     = $pass;
        $this->host     = $host;
        $this->port     = $port;
        $this->path     = $path;
        $this->query    = $query;
        $this->fragment = $fragment;
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
        return $this->getFull(true);
    }

    /**
     * 
     * Magic get for properties.
     * 
     * @param string $key The property to get.
     * 
     * @return mixed The value of the property.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    //FIXME , the @param and the method differs ?
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
    public function get()
    {
        // get the query as a string
        $query = $this->query->__toString();

        // we use trim() instead of empty() on string
        // elements to allow for string-zero values.
        return $this->path->__toString()
             . (empty($query)                ? '' : '?' . $query)
             . (trim($this->fragment) === '' ? '' : '#' . urlencode($this->fragment));
    }

    public function getFull()
    {
        // start with the scheme
        $url = empty($this->scheme)
             ? ''
             : urlencode($this->scheme) . '://';

        // add the username and password, if any.
        if (! empty($this->user)) {
            $url .= urlencode($this->user);
            if (! empty($this->pass)) {
                $url .= ':' . urlencode($this->pass);
            }
            $url .= '@';
        }

        // add the host and port, if any.
        $url .= (empty($this->host) ? '' : urlencode($this->host))
              . (empty($this->port) ? '' : ':' . (int) $this->port);

        return $url . $this->get();
    }

    /**
     *
     * set the scheme (for example 'http' or 'https').
     * 
     * @param string $scheme The scheme (for example 'http' or 'https').
     * 
     * @return Url the Url object
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * 
     * The username, if any.
     *
     * @param string $user
     * 
     * @return Url the Url object
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * 
     * set the password, if any.
     *
     * @param string $pass The password, if any.
     * 
     * @return Url the Url object
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     *
     * set the host specification (for example, 'example.com').
     * 
     * @param string $host the host name
     * 
     * @return Url the Url object
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     *
     * The port number (for example, '80').
     * 
     * @param int $port the port number
     * 
     * @return Url  the Url object
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     *
     * the path elements including the format
     * 
     * @param Path $path Path object
     * 
     * @return Url the Url object
     */
    public function setPath(Path $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     *
     * A Query object
     * 
     * @param Query $query The query elements.
     * 
     * @return Url the Url object
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     *
     * The fragment portion (for example, the "foo" in "#foo").
     * 
     * @param string $fragment the fragment
     * 
     * @return Url the Url object
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }
}
 