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
        
        $list = new PublicSuffixList(require $file);
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

        $this->assertSame($subdomain, $this->host->subdomain);
        $this->assertEquals($publicSuffix, $this->host->publicSuffix);
        $this->assertEquals($registerableDomain, $this->host->registerableDomain);
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
    
    public function testPublicSuffixSpec()
    {
        // Any copyright is dedicated to the Public Domain.
        // http://creativecommons.org/publicdomain/zero/1.0/

        // null input.
        $this->checkPublicSuffix(null, null);
        // Mixed case.
        $this->checkPublicSuffix('COM', null);
        $this->checkPublicSuffix('example.COM', 'example.com');
        $this->checkPublicSuffix('WwW.example.COM', 'example.com');
        // Leading dot.
        $this->checkPublicSuffix('.com', null);
        $this->checkPublicSuffix('.example', null);
        $this->checkPublicSuffix('.example.com', null);
        $this->checkPublicSuffix('.example.example', null);
        // TLD with only 1 rule.
        $this->checkPublicSuffix('biz', null);
        $this->checkPublicSuffix('domain.biz', 'domain.biz');
        $this->checkPublicSuffix('b.domain.biz', 'domain.biz');
        $this->checkPublicSuffix('a.b.domain.biz', 'domain.biz');
        // TLD with some 2-level rules.
        $this->checkPublicSuffix('com', null);
        $this->checkPublicSuffix('example.com', 'example.com');
        $this->checkPublicSuffix('b.example.com', 'example.com');
        $this->checkPublicSuffix('a.b.example.com', 'example.com');
        $this->checkPublicSuffix('uk.com', null);
        $this->checkPublicSuffix('example.uk.com', 'example.uk.com');
        $this->checkPublicSuffix('b.example.uk.com', 'example.uk.com');
        $this->checkPublicSuffix('a.b.example.uk.com', 'example.uk.com');
        $this->checkPublicSuffix('test.ac', 'test.ac');
        // TLD with only 1 (wildcard) rule.
        $this->checkPublicSuffix('cy', null);
        $this->checkPublicSuffix('c.cy', null);
        $this->checkPublicSuffix('b.c.cy', 'b.c.cy');
        $this->checkPublicSuffix('a.b.c.cy', 'b.c.cy');
        // More complex TLD.
        $this->checkPublicSuffix('jp', null);
        $this->checkPublicSuffix('test.jp', 'test.jp');
        $this->checkPublicSuffix('www.test.jp', 'test.jp');
        $this->checkPublicSuffix('ac.jp', null);
        $this->checkPublicSuffix('test.ac.jp', 'test.ac.jp');
        $this->checkPublicSuffix('www.test.ac.jp', 'test.ac.jp');
        $this->checkPublicSuffix('kyoto.jp', null);
        $this->checkPublicSuffix('test.kyoto.jp', 'test.kyoto.jp');
        $this->checkPublicSuffix('ide.kyoto.jp', null);
        $this->checkPublicSuffix('b.ide.kyoto.jp', 'b.ide.kyoto.jp');
        $this->checkPublicSuffix('a.b.ide.kyoto.jp', 'b.ide.kyoto.jp');
        $this->checkPublicSuffix('c.kobe.jp', null);
        $this->checkPublicSuffix('b.c.kobe.jp', 'b.c.kobe.jp');
        $this->checkPublicSuffix('a.b.c.kobe.jp', 'b.c.kobe.jp');
        $this->checkPublicSuffix('city.kobe.jp', 'city.kobe.jp');
        $this->checkPublicSuffix('www.city.kobe.jp', 'city.kobe.jp');
        // TLD with a wildcard rule and exceptions.
        $this->checkPublicSuffix('om', null);
        $this->checkPublicSuffix('test.om', null);
        $this->checkPublicSuffix('b.test.om', 'b.test.om');
        $this->checkPublicSuffix('a.b.test.om', 'b.test.om');
        $this->checkPublicSuffix('songfest.om', 'songfest.om');
        $this->checkPublicSuffix('www.songfest.om', 'songfest.om');
        // US K12.
        $this->checkPublicSuffix('us', null);
        $this->checkPublicSuffix('test.us', 'test.us');
        $this->checkPublicSuffix('www.test.us', 'test.us');
        $this->checkPublicSuffix('ak.us', null);
        $this->checkPublicSuffix('test.ak.us', 'test.ak.us');
        $this->checkPublicSuffix('www.test.ak.us', 'test.ak.us');
        $this->checkPublicSuffix('k12.ak.us', null);
        $this->checkPublicSuffix('test.k12.ak.us', 'test.k12.ak.us');
        $this->checkPublicSuffix('www.test.k12.ak.us', 'test.k12.ak.us');
    }

    /**
     * This is a PHP interpretation of the checkPublicSuffix function referred to in 
     * the test instructions at the Public Suffix List project.
     *
     * "You will need to define a checkPublicSuffix() function which takes as a 
     * parameter a domain name and the public suffix, runs your implementation 
     * on the domain name and checks the result is the public suffix expected."
     *
     * @link http://publicsuffix.org/list/
     *
     * @param string $input Doman and public suffix
     * @param string $expected Expected result
     */
    public function checkPublicSuffix($input, $expected)
    {
        $this->assertSame($expected, $this->host->getRegisterableDomain($input));
    }
}
