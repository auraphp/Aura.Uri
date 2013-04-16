<?php
namespace Aura\Uri;
require_once dirname(__DIR__) . '/src.php';
return new Url\Factory(
    $_SERVER,
    new PublicSuffixList(
        include dirname(__DIR__) . '/data/public-suffix-list.php'
    )
);
