<?php
namespace Aura\Uri;

use Aura\Framework\Test\WiringAssertionsTrait;

class WiringTest extends \PHPUnit_Framework_TestCase
{
    use WiringAssertionsTrait;

    protected function setUp()
    {
        $this->loadDi();
    }

    public function testInstances()
    {
        $this->assertNewInstance('Aura\Uri\PublicSuffixList');
        $factory = $this->assertNewInstance('Aura\Uri\Url\Factory');
        $this->assertInstanceOf('Aura\Uri\Url', $factory->newInstance('http://example.com'));
        $this->assertInstanceOf('Aura\Uri\Url', $factory->newCurrent());
    }
}
