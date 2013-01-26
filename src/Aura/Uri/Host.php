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
 * Processing the host
 *
 * @package Aura.Uri
 *
 */
class Host
{
    /**
     * @var PublicSuffixList Public Suffix List
     */
    protected $psl;


    protected $subdomain;
    protected $registerableDomain;
    protected $publicSuffix;


    /**
     *
     * Constructor
     *
     * @param PublicSuffixList $psl Public Suffix List
     *
     * @param array $spec Host elements
     *
     */
    public function __construct(PublicSuffixList $psl, array $spec = [])
    {
        $this->psl = $psl;
        foreach ($spec as $key => $val) {
            $this->$key = $val;
        }
    }

    public function __get($key)
    {
        return $this->$key;
    }
    
    /**
     *
     * Converts the Host object to a string and returns it.
     *
     * @return string The full Host this object represents.
     *
     */
    public function __toString()
    {
        $toString = array_filter(
            [$this->subdomain, $this->registerableDomain],
            'strlen'
        );

        return implode('.', $toString);
    }

    /**
     *
     * Sets values from a host string; overwrites any previous values.
     *
     * To set from an array, use `exchangeArray()`.
     *
     * @param string $spec The host string to use; for example, 'example.com'
     *
     * @return void
     *
     */
    public function setFromString($spec)
    {
        $this->registerableDomain = $this->getRegisterableDomain($spec);
        $this->publicSuffix = substr($this->registerableDomain, strpos($this->registerableDomain, '.') + 1);

        $registerableDomainParts = explode('.', $this->registerableDomain);
        $hostParts = explode('.', $spec);
        $subdomainParts = array_diff($hostParts, $registerableDomainParts);
        $this->subdomain = implode('.', $subdomainParts);

        if (empty($this->subdomain) && !is_null($this->subdomain)) {
            $this->subdomain = null;
        }
    }

    /**
     * Returns registerable domain portion of provided domain
     *
     * Per the test cases provided by Mozilla
     * (http://mxr.mozilla.org/mozilla-central/source/netwerk/test/unit/data/test_psl.txt?raw=1),
     * this method should return null if the domain provided is a public suffix.
     *
     * This method is based heavily on the code found in regDomain.inc.php
     * @link https://github.com/usrflo/registered-domain-libs/blob/master/PHP/regDomain.inc.php
     * A copy of the Apache License, Version 2.0, is provided with this
     * distribution
     *
     * @param  string $domain Domain
     * @return string Registerable domain
     */
    public function getRegisterableDomain($domain)
    {
        if (strpos($domain, '.') === 0) {
            return null;
        }

        $publicSuffix = array();

        $domainParts = explode('.', strtolower($domain));
        $registerableDomain = $this->breakdown($domainParts, $this->psl, $publicSuffix);

        // Remove null values
        $publicSuffix = array_filter($publicSuffix, 'strlen');

        if ($registerableDomain == implode('.', $publicSuffix)) {
            return null;
        }

        return $registerableDomain;
    }

    /**
     * Compares domain parts to the Public Suffix List
     *
     * This method is based heavily on the code found in regDomain.inc.php.
     *
     * A copy of the Apache License, Version 2.0, is provided with this
     * distribution
     *
     * @link https://github.com/usrflo/registered-domain-libs/blob/master/PHP/regDomain.inc.php regDomain.inc.php
     *
     * @param array $domainParts      Domain parts as array
     * @param array $psl Array representation of the Public Suffix
     * List
     * @param  array  $publicSuffix Builds the public suffix during recursion
     * @return string Public suffix
     */
    protected function breakdown(array $domainParts, $psl, &$publicSuffix)
    {
        $part = array_pop($domainParts);
        $result = null;

        if (array_key_exists($part, $psl) && array_key_exists('!', $psl[$part])) {
            return $part;
        }

        if (array_key_exists($part, $psl)) {
            array_unshift($publicSuffix, $part);
            $result = $this->breakdown($domainParts, $psl[$part], $publicSuffix);
        }

        if (array_key_exists('*', $psl)) {
            array_unshift($publicSuffix, $part);
            $result = $this->breakdown($domainParts, $psl['*'], $publicSuffix);
        }

        if ($result === null) {
            return $part;
        }

        return $result . '.' . $part;
    }

}
