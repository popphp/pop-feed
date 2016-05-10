pop-feed
========

[![Build Status](https://travis-ci.org/popphp/pop-feed.svg?branch=master)](https://travis-ci.org/popphp/pop-feed)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-feed)](http://cc.popphp.org/pop-feed/)

OVERVIEW
--------
`pop-feed` is a component for generating and parsing web feeds while trying to normalize
the common nodes and items contained within a feed.

`pop-feed`is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-feed` using Composer.

    composer require popphp/pop-feed

BASIC USAGE
-----------

### Generating a feed

```php
use Pop\Feed\Writer;

$headers = [
    'published' => date('Y-m-d H:i:s'),
    'author'    => 'Test Author'
];

$items = [
    [
        'title'       => 'Some Item #1',
        'link'        => 'http://www.popphp.org/',
        'description' => 'This is the description of item #1',
        'published'   => date('Y-m-d H:i:s')
    ],
    [
        'title'       => 'Some Item #2',
        'link'        => 'http://popcorn.popphp.org/',
        'description' => 'This is the description of item #2',
        'published'   => date('Y-m-d H:i:s')
    ]
];

$feed = new Writer($headers, $items);
$feed->render();
```

Rendered as an RSS feed:

```xml
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/">
    <channel>
        <published>Tue, 21 Jul 2015 18:09:08 -0500</published>
        <author>Test Author</author>
        <item>
            <title>Some Item #1</title>
            <link>http://www.popphp.org/</link>
            <description>This is the description of item #1</description>
            <published>Tue, 21 Jul 2015 18:09:08 -0500</published>
        </item>
        <item>
            <title>Some Item #2</title>
            <link>http://popcorn.popphp.org/</link>
            <description>This is the description of item #2</description>
            <published>Tue, 21 Jul 2015 18:09:08 -0500</published>
        </item>
    </channel>
</rss>
```

Or, rendered as an Atom feed:

```php
$feed = new Writer($headers, $items);
$feed->setAtom();
$feed->render();
```

```xml
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="en">
    <published>Tue, 21 Jul 2015 18:10:39 -0500</published>
    <author>
        <name>Test Author</name>
    </author>
    <entry>
        <title>Some Item #1</title>
        <link href="http://www.popphp.org/" />
        <description>This is the description of item #1</description>
        <published>Tue, 21 Jul 2015 18:10:39 -0500</published>
    </entry>
    <entry>
        <title>Some Item #2</title>
        <link href="http://popcorn.popphp.org/" />
        <description>This is the description of item #2</description>
        <published>Tue, 21 Jul 2015 18:10:39 -0500</published>
    </entry>
</feed>
```

### Parsing a feed

If the feed is an RSS feed:

```php
use Pop\Feed\Reader;
use Pop\Feed\Format\Rss;

$feed = new Reader(new Rss('http://www.domain.com/rss'));

foreach ($feed->items as $item) {
    print_r($item);
}
```

If the feed is an Atom feed:

```php
use Pop\Feed\Reader;
use Pop\Feed\Format\Atom;

$feed = new Reader(new Atom('http://www.domain.com/feed'));

foreach ($feed->entries as $entry) {
    print_r($entry);
}
```