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
use Aura\Uri\Host;
use Aura\Uri\PublicSuffixListManager;

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

        $parts = $this->parse($spec);

        $elem = (array) $parts + $elem;

        $path = new Path([]);
        $path->setFromString($elem['path']);

        $query = new Query([]);
        $query->setFromString($elem['query']);

        $listManager = new PublicSuffixListManager();
        $host = new Host($listManager->getList(), []);
        $host->setFromString($elem['host']);

        return new Url(
            $elem['scheme'],
            $elem['user'],
            $elem['pass'],
            $host,
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

    /**
     * Parses url
     *
     * @param  string $spec Url to parse
     * @return Domain Parsed domain object
     */
    public function parse($spec)
    {
        $parts = [
            'scheme'   => null,
            'user'     => null,
            'pass'     => null,
            'host'     => null,
            'port'     => null,
            'path'     => null,
            'query'    => null,
            'fragment' => null,
        ];

        list($parts['scheme'], $spec) = explode('://', $spec);

        // If all that's left in $spec is a path, return empty array
        if (strpos($spec, '/') === 0) {
            return array();
        }

        // Does the URL contain a path?
        if (strpos($spec, '/') !== false) {
            // Extract path (and everything after)
            $parts['path'] = substr($spec, strpos($spec, '/'));
            // Remove path from host
            $spec = str_replace($parts['path'], '', $spec);
        }

        // Does the path include a querystring?
        if (strpos($parts['path'], '?') !== false) {
            list($parts['path'], $parts['query']) = explode('?', $parts['path']);
        }

        // Does the querystring include a fragment?
        if (strpos($parts['query'], '#') !== false) {
            list($parts['query'], $parts['fragment']) = explode('#', $parts['query']);
        }

        $parts['host'] = $spec;

        // Does $spec contain user:pass?
        if (strpos($spec, '@') !== false) {
            list($authParts, $parts['host']) = explode('@', $spec);
            list($parts['user'], $parts['pass']) = explode(':', $authParts);
        }

        // Does host contain a port?
        if (strpos($parts['host'], ':') !== false) {
            list($parts['host'], $parts['port']) = explode(':', $parts['host']);
        }

        return $parts;
    }
}
