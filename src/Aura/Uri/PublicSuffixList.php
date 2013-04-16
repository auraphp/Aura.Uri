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
class PublicSuffixList extends \ArrayObject
{
    /**
     * Public constructor
     *
     * @param mixed $list Array representing Public Suffix List or PHP Public
     * Suffix List file
     * @throws \InvalidArgumentException If $list is not array, file did not
     * contain an array, or $list is not an object
     */
    public function __construct($list)
    {
        if (!is_array($list)) {
            $list = include $list;
        }

        parent::__construct($list);
    }
}
