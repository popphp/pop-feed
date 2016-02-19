<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Feed\Format;

/**
 * Abstract feed format class
 *
 * @category   Pop
 * @package    Pop_Feed
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0
 */
abstract class AbstractFormat
{

    /**
     * Feed URLs templates
     * @var array
     */
    protected $urls = [];

    /**
     * URL to parse
     * @var string
     */
    protected $url = null;

    /**
     * File to parse
     * @var string
     */
    protected $file = null;

    /**
     * Parsed object
     * @var mixed
     */
    protected $obj = null;

    /**
     * Feed limit
     * @var int
     */
    protected $limit = 0;

    /**
     * Feed content
     * @var array
     */
    protected $feed = [];

    /**
     * Context options
     * @var int
     */
    protected $contextOptions = [
        'http' => [
            'method'     => 'GET',
            'user_agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0'
        ]
    ];

    /**
     * Stream context
     * @var resource
     */
    protected $context = null;

    /**
     * Feed source
     * @var string
     */
    protected $source = null;

    /**
     * Feed options
     * @var mixed
     */
    protected $options = null;

    /**
     * Constructor method
     *
     * @param  mixed $options
     * @param  int   $limit
     * @throws Exception
     * @return AbstractFormat
     */
    public function __construct($options, $limit = 0)
    {
        $this->options = $options;
        $this->limit   = $limit;

        // Check if file or URL was passed
        if (is_array($options) && isset($options['file'])) {
            $this->file = $options['file'];
        } else if (is_array($options) && isset($options['url'])) {
            if ((substr($options['url'], 0, 7) == 'http://') || (substr($options['url'], 0, 8) == 'https://')) {
                $this->url = $options['url'];
            }
        } else if (is_string($options))  {
            if ((substr($options, 0, 7) == 'http://') || (substr($options, 0, 8) == 'https://')) {
                $this->url = $options;
            }
        }

        if ((null === $this->url) && (null === $this->file)) {
            throw new Exception('Error: Neither a valid file or URL was passed.');
        }

        if (null !== $this->file) {
            $this->source = file_get_contents($this->file);
        } else {
            // Set user agent
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $this->contextOptions['http']['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            }

            // Create stream context
            $this->context = (is_array($options) && isset($options['context'])) ?
                $options['context'] :
                stream_context_create($this->contextOptions);

            // Get the feed source
            $this->source = (is_array($options) && isset($options['source'])) ?
                $options['source'] :
                file_get_contents($this->url, false, $this->context);
        }

        // If the object is already parsed and passed into the constructor
        if (is_array($options) && isset($options['object'])) {
            $this->obj = $options['object'];
        }
    }

    /**
     * Calculate the elapsed time between the date passed and now.
     *
     * @param  string $time
     * @return string
     */
    public static function calculateTime($time)
    {
        // Calculate the difference.
        $timeDiff = time() - strtotime($time);
        $timePhrase = null;

        // If less than a minute
        if ($timeDiff < 60) {
            $timePhrase = $timeDiff . 'S';
        // If less than an hour.
        } else if ($timeDiff < 3600) {
            $timePhrase = round($timeDiff / 60) . 'M';
        // If less than a day.
        } else if (($timeDiff >= 3600) && ($timeDiff < 86400)) {
            $timePhrase = round(($timeDiff / 60) / 60) . 'H';
        // If less than a month.
        } else if (($timeDiff >= 86400) && ($timeDiff < 2592000)) {
            $timePhrase = round(((($timeDiff / 60) / 60) / 24)) . 'D';
        // If more than a month, less than 1 years
        } else if (($timeDiff >= 2592000) && ($timeDiff < 31536000)) {
            $timePhrase = round((((($timeDiff / 60) / 60) / 24) / 30)) . 'M';
        // If more than 2 years ago
        } else {
            $timePhrase = round((((($timeDiff / 60) / 60) / 24 / 30) / 12)) . 'Y';
        }

        // Return the calculated elapsed time.
        return $timePhrase;
    }

    /**
     * Parse a feed object
     *
     * @return void
     */
    abstract public function parse();

    /**
     * Get the URL
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
    /**
     * Get the file
     *
     * @return string
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * Get the parsed object
     *
     * @return mixed
     */
    public function obj()
    {
        return $this->obj;
    }

    /**
     * Set the feed
     *
     * @param  array $feed
     * @return AbstractFormat
     */
    public function setFeed(array $feed = [])
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Get the feed
     *
     * @return array
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Get the feed options
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set method to set the property to the value of feed[$name].
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->feed[$name] = $value;
    }

    /**
     * Get method to return the value of feed[$name].
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return (isset($this->feed[$name])) ? $this->feed[$name] : null;
    }

    /**
     * Return the isset value of feed[$name].
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->feed[$name]);
    }

    /**
     * Unset feed[$name].
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->feed[$name] = null;
    }

}
