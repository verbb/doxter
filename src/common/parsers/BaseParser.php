<?php
namespace verbb\doxter\common\parsers;

use verbb\doxter\Doxter;

abstract class BaseParser
{
    // Static Methods
    // =========================================================================

    /**
     * Returns an instance of the class in called static context
     *
     * @return Object
     */
    public static function instance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static;
        }

        return static::$_instance;
    }


    // Properties
    // =========================================================================

    /**
     * @var object The parser instance which should be defined in each extending class
     */
    protected static $_instance;


    // Public Methods
    // =========================================================================

    /**
     * Reports whether the source string can be safely parsed
     *
     * @param mixed $source
     *
     * @return bool
     */
    public function canBeSafelyParsed($source = null): bool
    {
        return Doxter::$plugin->getService()->canBeSafelyParsed($source);
    }

    /**
     * Must be implemented by all extending parsers
     *
     * @param string $source
     * @param array $options
     *
     * @return mixed
     */
    abstract public function parse(string $source, array $options = []);
}
