<?php
/**
 * Loader
 */
$loader->add('Aura\Uri\\', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src');

/**
 * Aura\Uri\PublicSuffixList
 */
$di->params['Aura\Uri\PublicSuffixList']['list'] = $di->lazyRequire(
    dirname(__DIR__) . '/data/public-suffix-list.php'
);

/**
 * Aura\Uri\Url\Factory
 */
$di->params['Aura\Uri\Url\Factory']['server'] = $_SERVER;
$di->params['Aura\Uri\Url\Factory']['psl'] = $di->lazyNew('Aura\Uri\PublicSuffixList');
