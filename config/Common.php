<?php
namespace Aura\Uri\_Config;

use Aura\Di\Config;
use Aura\Di\Container;

class Common extends Config
{
    public function define(Container $di)
    {
        /**
         * Aura\Uri\PublicSuffixList
         */
        $di->params['Aura\Uri\PublicSuffixList']['list'] = $di->lazyRequire(
            dirname(__DIR__) . '/data/public-suffix-list.php'
        );

        /**
         * Aura\Uri\Url\Factory
         */
        $di->params['Aura\Uri\Factory']['server'] = $_SERVER;
        $di->params['Aura\Uri\Factory']['psl'] = $di->lazyNew('Aura\Uri\PublicSuffixList');
    }
}
