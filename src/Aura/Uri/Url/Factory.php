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
namespace Aura\Uri\Url;

use Aura\Uri\Url;
use Aura\Uri\Path;
use Aura\Uri\Query;

/**
 * 
 * Factory to create new Url objects.
 * 
 * @package Aura.Uri
 * 
 */
class Factory
{
    /**
     * 
     * A string representing the current URL, built from $_SERVER.
     * 
     * @var string
     * 
     */
    protected $current;

    /**
     * 
     * Constructor.
     * 
     * @param array $server An array copy of $_SERVER.
     * 
     */
    public function __construct(array $server)
    {
        $https  = isset($server['HTTPS'])
               && strtolower($server['HTTPS']) == 'on';

        $ssl    = isset($server['SERVER_PORT'])
               && $server['SERVER_PORT'] == 443;

        if ($https || $ssl) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        if (isset($server['HTTP_HOST'])) {
            $host = $server['HTTP_HOST'];
        } else {
            $host = '';
        }

        if (isset($server['REQUEST_URI'])) {
            $resource = $server['REQUEST_URI'];
        } else {
            $resource = '';
        }

        $this->current = $scheme . '://' . $host . $resource;
    }

    /**
     * 
     * Creates and returns a new Url object.
     * 
     * If no host is specified, the parsing will fail.
     * 
     * @param string $spec The URL string to set from.
     * 
     * @return Url
     * 
     */
    public function newInstance($spec)
    {
        $elem = [
            'scheme'   => null,
            'user'     => null,
            'pass'     => null,
            'host'     => null,
            'port'     => null,
            'path'     => null,
            'query'    => null,
            'fragment' => null,
        ];

        $parts = parse_url($spec);
        
        $elem = (array) $parts + $elem;

        $path = new Path([]);
        $path->setFromString($elem['path']);

        $query = new Query([]);
        $query->setFromString($elem['query']);

        return new Url(
            $elem['scheme'],
            $elem['user'],
            $elem['pass'],
            $elem['host'],
            $elem['port'],
            $path,
            $query,
            $elem['fragment']
        );
    }

    /**
     * 
     * Creates and returns a new URL object based on the current URL.
     * 
     * @return Url
     * 
     */
    public function newCurrent()
    {
        return $this->newInstance($this->current);
    }
}
