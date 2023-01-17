<?php

namespace Aura\Uri;


use PHPUnit\Framework\TestCase;

class PublicSuffixListTest extends TestCase
{
    /**
     * @var PublicSuffixList
     */
    protected $psl;

    public function setUp() : void
    {
        parent::setUp();
        $file = dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR
              . 'data' . DIRECTORY_SEPARATOR
              . 'public-suffix-list.php';
        $this->psl = new PublicSuffixList(require $file);
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testGetPublicSuffix($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
    {
        $this->assertSame($publicSuffix, $this->psl->getPublicSuffix($hostPart));
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testGetRegisterableDomain($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
    {
        $this->assertSame($registerableDomain, $this->psl->getRegisterableDomain($hostPart));
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testGetSubdomain($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
    {
        $this->assertSame($subdomain, $this->psl->getSubdomain($hostPart));
    }

	/**
     * @dataProvider parseDataProvider
	 */
	public function testPHPparse_urlCanReturnCorrectHost($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
	{
		$this->assertEquals($hostPart, parse_url('http://' . $hostPart, PHP_URL_HOST));
	}

    public function parseDataProvider()
    {
        // $url, $publicSuffix, $registerableDomain, $subdomain, $hostPart
        return array(
            array('http://www.waxaudio.com.au/audio/albums/the_mashening', 'com.au', 'waxaudio.com.au', 'www', 'www.waxaudio.com.au'),
            array('example.com', 'com', 'example.com', null, 'example.com'),
            array('giant.yyyy', 'yyyy', 'giant.yyyy', null, 'giant.yyyy'),
            array('cea-law.co.il', 'co.il', 'cea-law.co.il', null, 'cea-law.co.il'),
            array('http://edition.cnn.com/WORLD/', 'com', 'cnn.com', 'edition', 'edition.cnn.com'),
            array('http://en.wikipedia.org/', 'org', 'wikipedia.org', 'en', 'en.wikipedia.org'),
            array('a.b.c.cy', 'c.cy', 'b.c.cy', 'a', 'a.b.c.cy'),
            array('https://test.k12.ak.us', 'k12.ak.us', 'test.k12.ak.us', null, 'test.k12.ak.us'),
            array('www.scottwills.co.uk', 'co.uk', 'scottwills.co.uk', 'www', 'www.scottwills.co.uk'),
            array('b.ide.kyoto.jp', 'ide.kyoto.jp', 'b.ide.kyoto.jp', null, 'b.ide.kyoto.jp'),
            array('a.b.example.uk.com', 'uk.com', 'example.uk.com', 'a.b', 'a.b.example.uk.com'),
            array('test.nic.ar', 'ar', 'nic.ar', 'test', 'test.nic.ar'),
            array('a.b.test.ck', 'test.ck', 'b.test.ck', 'a', 'a.b.test.ck'),
            array('baez.songfest.om', 'om', 'songfest.om', 'baez', 'baez.songfest.om'),
            array('politics.news.omanpost.om', 'om', 'omanpost.om', 'politics.news', 'politics.news.omanpost.om'),
            array('http://localhost', null, null, null, 'localhost'),
        );
    }

}
