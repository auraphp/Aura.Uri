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
        
        $list = new PublicSuffixList([
            'au' => [
                'com' => [],
            ],
            'com' => [
                'uk' => [],
            ],
            'cy' => [
                'c' => [],
            ],
            'il' => [
                'co' => [],
            ],
            'jp' => [
                'kyoto' => [
                    'ide' => [],
                ],
            ],
            'om' => [
                'test' => [],
            ],
            'uk' => [
                'co' => [],
            ],
            'us' => [
                'ak' => [
                    'k12' => [],
                ],
            ],
        ]);
        
        $this->host = new Host($list);
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
    public function testSetFromString($string)
    {
        $this->host->setFromString($string);
        $this->assertEquals($string, $this->host->__toString());
    }

    public function hostDataProvider()
    {
        return array(
            array('example.com'),
            array('purple.com')
        );
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse($url, $publicSuffix, $registerableDomain, $subdomain)
    {
        $this->host->setFromString($url);

        $this->assertSame($subdomain, $this->host->getSubdomain());
        $this->assertEquals($publicSuffix, $this->host->getPublicSuffix());
        $this->assertEquals($registerableDomain, $this->host->getRegisterableDomain());
    }

    public function parseDataProvider()
    {
        return array(
            array('www.waxaudio.com.au', 'com.au', 'waxaudio.com.au', 'www'),
            array('example.com', 'com', 'example.com', null),
            array('cea-law.co.il', 'co.il', 'cea-law.co.il', null),
            array('edition.cnn.com', 'com', 'cnn.com', 'edition'),
            array('en.wikipedia.org', 'org', 'wikipedia.org', 'en'),
            array('a.b.c.cy', 'c.cy', 'b.c.cy', 'a'),
            array('test.k12.ak.us', 'k12.ak.us', 'test.k12.ak.us', null),
            array('www.scottwills.co.uk', 'co.uk', 'scottwills.co.uk', 'www'),
            array('b.ide.kyoto.jp', 'ide.kyoto.jp', 'b.ide.kyoto.jp', null),
            array('a.b.example.uk.com', 'uk.com', 'example.uk.com', 'a.b'),
            array('test.nic.ar', 'ar', 'nic.ar', 'test'),
            array('a.b.test.om', 'test.om', 'b.test.om', 'a', null),
            array('baez.songfest.om', 'om', 'songfest.om', 'baez'),
            array('politics.news.omanpost.om', 'om', 'omanpost.om', 'politics.news'),
        );
    }
    
}
