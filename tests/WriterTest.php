<?php

namespace Pop\Feed\Test;

use Pop\Feed\Writer;

class WriterTest extends \PHPUnit_Framework_TestCase
{

    protected $headers = [
        'title'     => 'Test Feed Title',
        'subtitle'  => 'Test Feed Description',
        'link'      => 'http://www.testfeed.com/',
        'language'  => 'en',
        'updated'   => '2010-01-12 13:01:32',
        'generator' => 'http://www.website.com/',
        'author'    => 'Some Editor'
    ];
    protected $items = [
        [
            'title'    => 'Entry Title 1',
            'link'     => 'http://www.testfeed.com/entry1',
            'comments' => 'http://www.testfeed.com/entry1#comments',
            'author'   => 'Entry Author 1',
            'updated'  => '2010-01-13 14:12:24',
            'summary'  => 'Entry Desc 1'
        ],
        [
            'title'    => 'Entry Title 2',
            'link'     => 'http://www.testfeed.com/entry2',
            'comments' => 'http://www.testfeed.com/entry2#comments',
            'author'   => 'Entry Author 2',
            'updated'  => '2010-01-12 14:12:24',
            'summary'  => 'Entry Desc 2'
        ],
        [
            'title'    => 'Entry Title 3',
            'link'     => 'http://www.testfeed.com/entry3',
            'comments' => 'http://www.testfeed.com/entry3#comments',
            'author'   => 'Entry Author 3',
            'updated'  => '2010-01-11 14:12:24',
            'summary'  => 'Entry Desc 3'
        ]
    ];

    public function testConstructor()
    {
        $feed = new Writer($this->headers, $this->items);
        $this->assertInstanceOf('Pop\Feed\Writer', $feed);
        $this->assertEquals(3, count($feed->getItems()));
    }

    public function testSetHeader()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setHeader('author', 'Another Editor');
        $this->assertEquals('Another Editor', $feed->getHeader('author'));
        $this->assertEquals(7, count($feed->getHeaders()));
    }

    public function testSetItemsEmptyException()
    {
        $this->setExpectedException('Pop\Feed\Exception');
        $feed = new Writer($this->headers, $this->items);
        $feed->setItems([]);
    }

    public function testSetItemsBadValueException()
    {
        $this->setExpectedException('Pop\Feed\Exception');
        $feed = new Writer($this->headers, [
            'foo' => 'bar'
        ]);
    }

    public function testAddItem()
    {
        $feed = new Writer($this->headers, [$this->items[0]]);
        $feed->addItem($this->items[1]);
        $this->assertEquals(2, count($feed->getItems()));
    }

    public function testAddItemEmptyException()
    {
        $this->setExpectedException('Pop\Feed\Exception');
        $feed = new Writer($this->headers, [$this->items[0]]);
        $feed->addItem([]);
    }

    public function testAddItems()
    {
        $feed = new Writer($this->headers, [$this->items[0]]);
        $feed->addItems([$this->items[1], $this->items[2]]);
        $this->assertEquals(3, count($feed->getItems()));
    }

    public function testAddItemsException()
    {
        $this->setExpectedException('Pop\Feed\Exception');
        $feed = new Writer($this->headers, [$this->items[0]]);
        $feed->addItems(['foo' => 'bar']);
    }

    public function testSetDataFormat()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setDateFormat('m/d/Y');
        $this->assertEquals('m/d/Y', $feed->getDateFormat());
    }

    public function testSetCharset()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setCharset('utf-8');
        $this->assertEquals('utf-8', $feed->getCharset());
    }

    public function testSetAtom()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setAtom();
        $this->assertTrue($feed->isAtom());
    }

    public function testSetRss()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setRss();
        $this->assertTrue($feed->isRss());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRenderRss()
    {
        $feed = new Writer($this->headers, $this->items);
        ob_start();
        $feed->render();
        $result = ob_get_clean();


        $this->assertContains('<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">', $result);
        $this->assertContains('<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">', (string)$feed);
        $this->assertContains('<title>Test Feed Title</title>', $result);
        $this->assertContains('<title>Test Feed Title</title>', (string)$feed);
        $this->assertContains('<title>Entry Title 1</title>', $result);
        $this->assertContains('<title>Entry Title 1</title>', (string)$feed);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRenderAtom()
    {
        $feed = new Writer($this->headers, $this->items);
        $feed->setAtom();
        ob_start();
        $feed->render();
        $result = ob_get_clean();

        $this->assertContains('<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en">', $result);
        $this->assertContains('<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en">', (string)$feed);
        $this->assertContains('<title>Test Feed Title</title>', $result);
        $this->assertContains('<title>Test Feed Title</title>', (string)$feed);
        $this->assertContains('<title>Entry Title 1</title>', $result);
        $this->assertContains('<title>Entry Title 1</title>', (string)$feed);
    }

}