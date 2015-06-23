<?php

namespace Pop\Feed\Test;

use Pop\Feed\Reader;
use Pop\Feed\Format;

class ReaderTest extends \PHPUnit_Framework_TestCase
{

    public function testRssConstructor()
    {
        $feed = new Reader(new Format\Rss(['file' => __DIR__ . '/tmp/rss.xml']));
        $this->assertInstanceOf('Pop\Feed\Reader', $feed);
        $this->assertInstanceOf('Pop\Feed\Format\Rss', $feed->adapter());
        $this->assertInstanceOf('ArrayObject', $feed->feed());
        $this->assertTrue($feed->isRss());
        $this->assertFalse($feed->isAtom());
        $this->assertEquals('Entry Title 1', $feed->items[0]['title']);
        $this->assertEquals('Entry Title 2', $feed->entries[1]['title']);
        $this->assertNull($feed->adapter()->url());
        $this->assertNotNull($feed->adapter()->file());
        $this->assertInstanceOf('SimpleXMLElement', $feed->adapter()->obj());
        $this->assertEquals(__DIR__ . '/tmp/rss.xml', $feed->adapter()->getOptions()['file']);
        $this->assertEquals(0, $feed->adapter()->getLimit());
    }

    public function testAtomConstructor()
    {
        $feed = new Reader(new Format\Atom(['file' => __DIR__ . '/tmp/atom.xml']));
        $this->assertInstanceOf('Pop\Feed\Reader', $feed);
        $this->assertInstanceOf('Pop\Feed\Format\Atom', $feed->adapter());
        $this->assertInstanceOf('ArrayObject', $feed->feed());
        $this->assertTrue($feed->isAtom());
        $this->assertFalse($feed->isRss());
        $this->assertEquals('Entry Title 1', $feed->items[0]['title']);
        $this->assertEquals('Entry Title 2', $feed->entries[1]['title']);
    }

    public function testSetFeed()
    {
        $feed = new Format\Rss(['file' => __DIR__ . '/tmp/rss.xml']);
        $feed->setFeed([]);
        $this->assertEquals(0, count($feed->getFeed()));
    }

    public function testMagicMethod()
    {
        $feed = new Format\Rss(['file' => __DIR__ . '/tmp/rss.xml']);
        $feed->setFeed([]);
        $feed->title = 'Test Title';
        $this->assertEquals('Test Title', $feed->title);
        $this->assertTrue(isset($feed->title));
        unset($feed->title);
        $this->assertFalse(isset($feed->title));
    }

}