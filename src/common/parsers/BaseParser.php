<?php
namespace selvinortiz\doxter\common\parsers;

use function selvinortiz\doxter\doxter;

/**
 * The base parser that all other parsers must extend
 *
 * Class DoxterBaseParser
 *
 * @package Craft
 */
abstract class BaseParser
{
    /**
     * @var object The parser instance which should be defined in each extending class
     */
    protected static $_instance;

    /**
     * Returns an instance of the class in called static context
     *
     * @return BaseParser|Markdown|ReferenceTag|Shortcode|CodeBlock|Header|Object
     */
    public static function instance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static;
        }

        return static::$_instance;
    }

    /**
     * Reports whether the source string can be safely parsed
     *
     * @param mixed $source
     *
     * @return bool
     */
    public function canBeSafelyParsed($source = null)
    {
        return doxter()->api->canBeSafelyParsed($source);
    }

    /**
     * Must be implemented by all extending parsers
     *
     * @param string $source
     * @param array  $options
     *
     * @return mixed
     */
    abstract public function parse($source, array $options = []);
}
