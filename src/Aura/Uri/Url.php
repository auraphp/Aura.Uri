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
     * The Host object
     *
     * @var Host
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
     * @param Host $host The host elements.
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
        Host $host,
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

    /**
     *
     * Returns the URL as a string, not including scheme or host.
     *
     * @return string The URL string.
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

    /**
     *
     * Returns the URL as a string, including the scheme and host.
     *
     * @return string The URL string.
     *
     */
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

        $host = $this->host->__toString();

        // add the host and port, if any.
        $url .= (empty($host) ? '' : urlencode($host))
              . (empty($this->port) ? '' : ':' . (int) $this->port);

        return $url . $this->get();
    }

    /**
     *
     * Set the scheme (for example 'http' or 'https').
     *
     * @param string $scheme The scheme (for example 'http' or 'https').
     *
     * @return $this
     *
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     *
     * Sets the username.
     *
     * @param string $user The username.
     *
     * @return $this
     *
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     *
     * Sets the password.
     *
     * @param string $pass The password.
     *
     * @return $this
     *
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     *
     * Sets the Host object for this URL.
     *
     * @param Host $host The host name.
     *
     * @return $this
     *
     */
    public function setHost(Host $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     *
     * Sets the port number (for example, '80').
     *
     * @param int $port The port number.
     *
     * @return $this
     *
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     *
     * Sets the Path object for this URL.
     *
     * @param Path $path The Path object.
     *
     * @return $this
     *
     */
    public function setPath(Path $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     *
     * Sets the Query object for this URL.
     *
     * @param Query $query The Query object.
     *
     * @return $this
     *
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     *
     * Sets the fragment portion (for example, the "foo" in "#foo").
     *
     * @param string $fragment The fragment.
     *
     * @return $this
     *
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }
}
