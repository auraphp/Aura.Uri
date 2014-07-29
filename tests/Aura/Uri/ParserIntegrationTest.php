<?php

namespace Aura\Uri;

use Aura\Uri\Url\Factory;

class ParserIntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testExcerciseUrlParsing($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
    {
        $file = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR
              . 'data' . DIRECTORY_SEPARATOR
              . 'public-suffix-list.php';
        $psl = new PublicSuffixList(require $file);
        $server = [];
        $factory = new Factory($server, $psl);

        $url = $factory->newInstance($url);
        $this->assertEquals($hostPart, $url->host);
        $this->assertEquals($publicSuffix, $url->host->getPublicSuffix());
        $this->assertEquals($registerableDomain, $url->host->getRegisterableDomain());
        $this->assertEquals($subdomain, $url->host->getSubdomain());
        $this->assertFalse(strpos('http:', $url->getSchemeless()));
    }

    public function parseDataProvider()
    {
        return DataProvider::parseDataProvider();
    }
}
