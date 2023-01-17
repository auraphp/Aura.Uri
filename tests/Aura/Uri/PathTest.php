<?php
namespace Aura\Uri;

use PHPUnit\Framework\TestCase;

/**
 * Test class for Path.
 * Generated by PHPUnit on 2012-07-21 at 15:45:14.
 */
class PathTest extends TestCase
{
    /**
     * @var Path
     */
    protected $path;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->path = new Path;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown() : void
    {
        parent::tearDown();
    }

    /**
     * @covers \Aura\Uri\Path::__toString
     */
    public function test__toString()
    {
        $path = '/foo/bar/baz/dib.gir';
        $this->path->setFromString($path);
        $actual = $this->path->__toString();
        $this->assertSame($path, $actual);
    }

    /**
     * @covers \Aura\Uri\Path::setFromString
     */
    public function testSetFromString()
    {
        $path = '/foo/bar/baz/dib.gir';
        $this->path->setFromString($path);
        
        $expect = '.gir';
        $actual = $this->path->getFormat();
        $this->assertSame($expect, $actual);
        
        $actual = $this->path->__toString();
        $this->assertSame($path, $actual);
    }

    /**
     * @covers \Aura\Uri\Path::setFormat
     * @covers \Aura\Uri\Path::getFormat
     */
    public function testSetAndGetFormat()
    {
        $format = '.json';
        $this->path->setFormat($format);
        $actual = $this->path->getFormat($format);
        $this->assertSame($format, $actual);
    }
}
