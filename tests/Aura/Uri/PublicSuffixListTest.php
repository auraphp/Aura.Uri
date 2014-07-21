<?php

namespace Aura\Uri;

class PublicSuffixListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aura\Uri\PublicSuffixList
     */
    protected $psl;

    protected function setUp()
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
	public function test_mb_parse_urlCanReturnCorrectHost($url, $publicSuffix, $registerableDomain, $subdomain, $hostPart)
	{
		$this->assertEquals($hostPart, mb_parse_url('http://' . $hostPart, PHP_URL_HOST));
	}

    public function parseDataProvider()
    {
        return DataProvider::parseDataProvider();
    }
}
