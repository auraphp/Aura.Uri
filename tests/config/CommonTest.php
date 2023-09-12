<?php
namespace Aura\Uri;

use Aura\Uri\Url\Factory;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class WiringTest extends TestCase
{
    public function testInstances()
    {
        $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'data' . DIRECTORY_SEPARATOR
            . 'public-suffix-list.php';
        $psl = new PublicSuffixList(require $file);
        $factory = new Factory([], $psl);
        $this->assertInstanceOf('Aura\Uri\Url', $factory->newInstance('http://example.com'));
        $this->assertInstanceOf('Aura\Uri\Url', $factory->newCurrent());
    }
}
