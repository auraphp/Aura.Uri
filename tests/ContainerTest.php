<?php
namespace Aura\Uri\_Config;

use Aura\Di\_Config\AbstractContainerTest;

class ContainerTest extends AbstractContainerTest
{
    protected function getConfigClasses()
    {
        return array(
            'Aura\Uri\_Config\Common',
        );
    }

    protected function getAutoResolve()
    {
        return false;
    }

    public function provideNewInstance()
    {
        return array(
            array('Aura\Uri\PublicSuffixList'),
            array('Aura\Uri\UrlFactory'),
        );
        // $factory = $this->assertNewInstance('Aura\Uri\Url\Factory');
        // $this->assertInstanceOf('Aura\Uri\Url', $factory->newInstance('http://example.com'));
        // $this->assertInstanceOf('Aura\Uri\Url', $factory->newCurrent());
    }
}
