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

    /**
     * @var string Subdomain portion of host
     */
    protected $subdomain;

    /**
     * @var string Registerable domain portion of host
     */
    protected $registerableDomain;

    /**
     * @var string Public suffix portion of host
     */
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

    /**
     * Returns value of property $name
     *
     * @param  string $name Name of property
     * @return mixed  Value of property $name
     */
    public function __get($name)
    {
        return $this->$name;
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
     * @param string $spec The host string to use; for example, 'example.com'
     *
     * @return void
     *
     */
    public function setFromString($spec)
    {
        $this->publicSuffix = $this->psl->getPublicSuffix($spec);
        $this->registerableDomain = $this->psl->getRegisterableDomain($spec);
        $this->subdomain = $this->psl->getSubdomain($spec);
    }
}
