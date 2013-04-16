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
 * Processing the query string
 *
 * @package Aura.Uri
 *
 */
class Query extends \ArrayObject
{
    /**
     *
     * Returns the query portion as a string.
     *
     * @return string The query string; e.g., `foo=bar&baz=dib`.
     *
     */
    public function __toString()
    {
        return $this->buildString($this->getArrayCopy());
    }

    /**
     *
     * Sets values from a query string; overwrites any previous values.
     *
     * To set from an array, use `exchangeArray()`.
     *
     * @param string $spec The query string to use; for example,
     * `foo=bar&baz=dib`.
     *
     * @return void
     *
     */
    public function setFromString($spec)
    {
        parse_str($spec, $query);
        $this->exchangeArray($query);
    }

    /**
     *
     * Build string from an array
     *
     * @param array $array
     *
     * @param string $prefix Defaults to null
     *
     * @return string Returns a string
     */
    protected function buildString(array $array, $prefix = null)
    {
        $elem = [];
        foreach ($array as $key => $val) {

            $key = ($prefix)
                 ? $prefix . '[' . $key . ']'
                 : $key;

            if (is_array($val)) {
                $elem[] = $this->buildString($val, $key);
            } else {
                $val = ($val === null || $val === false)
                     ? ''
                     : urlencode($val);
                $elem[] = urlencode($key) . '=' . $val;
            }
        }

        return implode('&', $elem);
    }
}
