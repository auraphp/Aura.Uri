<?php

namespace Aura\Uri;

class HostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aura\Uri\Host
     */
    protected $host;

    protected function setUp()
    {
        parent::setUp();

        $file = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR
              . 'data' . DIRECTORY_SEPARATOR
              . 'public-suffix-list.php';
        $psl = new PublicSuffixList(require $file);

        $this->host = new Host($psl);
    }

    protected function tearDown()
    {
        $this->host = null;
        parent::tearDown();
    }

    /**
     * @dataProvider hostDataProvider
     */
    public function test__toString($string)
    {
        $this->host->setFromString($string);
        $this->assertEquals($string, $this->host->__toString());
    }

    /**
     * @dataProvider hostDataProvider
     */
    public function testGet($string)
    {
        $this->host->setFromString($string);
        $this->assertEquals($string, $this->host->get());
    }

    /**
     * @dataProvider hostDataProvider
     */
    public function testSetFromString($string)
    {
        $this->host->setFromString($string);
        $this->assertEquals($string, $this->host->__toString());
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
    {
        $this->host->setFromString($hostPart);
        $this->assertSame($subdomain, $this->host->getSubdomain());
        $this->assertEquals($publicSuffix, $this->host->getPublicSuffix());
        $this->assertEquals($registerableDomain, $this->host->getRegisterableDomain());
        $this->assertEquals($hostPart, $this->host->get());
    }

    public function hostDataProvider()
    {
        return DataProvider::hostDataProvider();
    }

    public function parseDataProvider()
    {
        return DataProvider::parseDataProvider();
    }
}
