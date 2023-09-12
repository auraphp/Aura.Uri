<?php
/**
 *
 * This file is part of Aura for PHP.
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
     *
     * The public suffix list.
     *
     * @var array
     *
     */
    protected $psl;

    /**
     *
     * The full Host this object represents.
     *
     * @var string
     *
     */
    protected $host;

    /**
     *
     * Subdomain portion of host.
     *
     * @var string
     *
     */
    protected $subdomain;

    /**
     *
     * Registerable domain portion of host.
     *
     * @var string
     *
     */
    protected $registerable_domain;

    /**
     *
     * Public suffix portion of host.
     *
     * @var string
     *
     */
    protected $public_suffix;

    /**
     *
     * Constructor.
     *
     * @param PublicSuffixList $psl Public suffix list.
     *
     * @param array $spec Host elements.
     *
     */
    public function __construct(PublicSuffixList $psl, array $spec = [])
    {
        $this->psl = $psl;
        foreach ($spec as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     *
     * Returns this Host object as a string.
     *
     * @return string The full Host this object represents.
     *
     */
    public function __toString()
    {
        return $this->get();
    }

    /**
     *
     * Returns this Host object as a string.
     *
     * @return string The full Host this object represents.
     *
     */
    public function get()
    {
        if ($this->host !== null) {
            return $this->host;
        }

        // retain only the elements that are not empty
        $str = array_filter(
            [$this->subdomain, $this->registerable_domain],
            'strlen'
        );

        return implode('.', $str);
    }

    /**
     *
     * Sets values from a host string; overwrites any previous values.
     *
     * @param string $spec The host string to use; e.g., 'example.com'.
     *
     * @return void
     *
     */
    public function setFromString($spec)
    {
        $this->host = $spec;
        $this->public_suffix = $this->psl->getPublicSuffix($spec);
        $this->registerable_domain = $this->psl->getRegisterableDomain($spec);
        $this->subdomain = $this->psl->getSubdomain($spec);
    }

    /**
     *
     * Returns the public suffix portion of the host.
     *
     * @return string
     *
     */
    public function getPublicSuffix()
    {
        return $this->public_suffix;
    }

    /**
     *
     * Returns the subdomain portion of the host.
     *
     * @return string
     *
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     *
     * Returns the registerable domain portion of the host.
     *
     * @return string
     *
     */
    public function getRegisterableDomain()
    {
        return $this->registerable_domain;
    }
}
