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
 * Object representation of the Public Suffix List
 *
 * @package Aura.Uri
 *
 */
class PublicSuffixList
{
    /**
     * 
     * Public suffix list data.
     * 
     * @var array 
     * 
     */
    protected $psl;

    /**
     * 
     * Constructor.
     *
     * @param array $list Array representation of the Public Suffix List
     * 
     */
    public function __construct(array $list)
    {
        $this->psl = $list;
    }

    /**
     * Returns the public suffix portion of provided host
     *
     * @param  string $host host
     * @return string public suffix
     */
    public function getPublicSuffix($host)
    {
        if (strpos($host, '.') === 0) {
            return null;
        }

        if (strpos($host, '.') === false) {
            return null;
        }

        $host = strtolower($host);
        $parts = array_reverse(explode('.', $host));
        $publicSuffix = array();
        $psl = $this->psl;

        foreach ($parts as $part) {
            if (array_key_exists($part, $psl)
                && array_key_exists('!', $psl[$part])) {
                break;
            }

            if (array_key_exists($part, $psl)) {
                array_unshift($publicSuffix, $part);
                $psl = $psl[$part];
                continue;
            }

            if (array_key_exists('*', $psl)) {
                array_unshift($publicSuffix, $part);
                $psl = $psl['*'];
                continue;
            }

            // Avoids improper parsing when $host's subdomain + public suffix === 
            // a valid public suffix (e.g. host 'us.example.com' and public suffix 'us.com')
            break;
        }

        // Apply algorithm rule #2: If no rules match, the prevailing rule is "*".
        if (empty($publicSuffix)) {
            $publicSuffix[0] = $parts[0];
        }

        return implode('.', array_filter($publicSuffix, 'strlen'));
    }

    /**
     * Returns registerable domain portion of provided host
     *
     * Per the test cases provided by Mozilla
     * (http://mxr.mozilla.org/mozilla-central/source/netwerk/test/unit/data/test_psl.txt?raw=1),
     * this method should return null if the domain provided is a public suffix.
     *
     * @param  string $host host
     * @return string registerable domain
     */
    public function getRegisterableDomain($host)
    {
        if (strpos($host, '.') === false) {
            return null;
        }

        $host = strtolower($host);
        $publicSuffix = $this->getPublicSuffix($host);

        if ($publicSuffix === null || $host == $publicSuffix) {
            return null;
        }

        $publicSuffixParts = array_reverse(explode('.', $publicSuffix));
        $hostParts = array_reverse(explode('.', $host));
        $registerableDomainParts = array_slice($hostParts, 0, count($publicSuffixParts) + 1);

        return implode('.', array_reverse($registerableDomainParts));
    }

    /**
     * Returns the subdomain portion of provided host
     *
     * @param  string $host host
     * @return string subdomain
     */
    public function getSubdomain($host)
    {
        $host = strtolower($host);
        $registerableDomain = $this->getRegisterableDomain($host);

        if ($registerableDomain === null || $host == $registerableDomain) {
            return null;
        }

        $registerableDomainParts = array_reverse(explode('.', $registerableDomain));
        $hostParts = array_reverse(explode('.', $host));
        $subdomainParts = array_slice($hostParts, count($registerableDomainParts));

        return implode('.', array_reverse($subdomainParts));
    }
}
