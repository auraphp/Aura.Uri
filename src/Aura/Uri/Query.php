<?php
namespace Aura\Uri;

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
        return $this->buildString($this->toArray());
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

    protected function buildString(array $array, $prefix = null)
    {
        $elem = [];
        foreach ($array as $key => $val) {
            
            $key = ($prefix)
                 ? $prefix . '[' . $key . ']'
                 : $key;
            
            if (is_array($val)) {
                $val = $this->buildString($val, $key);
            } else {
                $val = ($val === null || $val === false)
                     ? ''
                     : urlencode($val);
            }
            
            $elem[] = urlencode($key) . '=' . $val;
        }
        
        return implode('&', $elem);
    }
}
