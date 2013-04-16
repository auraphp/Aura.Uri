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
namespace Aura\Uri\HttpAdapter;

/**
 * Interface for http adapters
 *
 * Lifted pretty much completely from William Durand's excellent Geocoder
 * project
 * @link https://github.com/willdurand/Geocoder Geocoder on GitHub
 * @author William Durand <william.durand1@gmail.com>
 * @author Jeremy Kendall <jeremy@jeremykendall.net>
 */
interface HttpAdapterInterface
{
    /**
     * Returns the content fetched from a given URL.
     *
     * @param  string $url
     * @return string Retrieved content
     */
    public function getContent($url);
}
