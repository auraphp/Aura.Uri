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
use Aura\Uri\PublicSuffixList;

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
     * Public suffix list
     *
     * @var PublicSuffixList
     */
    protected $psl;

    /**
     *
     * Constructor.
     *
     * @param array $server An array copy of $_SERVER.
     *
     * @param PublicSuffixList $psl Public suffix list
     */
    public function __construct(array $server, PublicSuffixList $psl)
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

        $this->psl = $psl;
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

        $host = new Host($this->psl, []);
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
     * @return array  Parsed url
     */
    public function parse($spec)
    {
        preg_match(Url::SCHEME_PATTERN, $spec, $schemeMatches);

        if (empty($schemeMatches)) {
            $spec = 'http://' . preg_replace('#^//#', '', $spec, 1);
        }

        return parse_url($spec);
    }
}
