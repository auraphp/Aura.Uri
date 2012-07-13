<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Uri\Url;

use Aura\Uri\Url;
use Aura\Web;

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
     * Creates and returns a new Url object.
     * 
     * @param unknown $arg URL string to load, or a Aura\Web\Context object
     * 
     * @return Aura\Uri\Url
     * 
     */
    public function newInstance($arg = null)
    {
        return new Url($arg);
    }

    /**
     * 
     * Creates and returns a new Url object based on the current page URL.
     * Requires the Aura\Web module.
     * 
     * @param array $globals Array of globals ($GLOBALS)
     * 
     * @return Aura\Uri\Url
     * 
     */
    public function newCurrent(array $globals)
    {
        return new Url(new Web\Context($globals));
    }
}

