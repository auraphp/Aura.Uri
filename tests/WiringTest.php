<?php
namespace Aura\Uri;

use Aura\Framework\Test\WiringAssertionsTrait;
use PHPUnit\Framework\TestCase;

class WiringTest extends TestCase
{
    use WiringAssertionsTrait;

    public function setUp() : void
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
