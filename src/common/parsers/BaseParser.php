<?php
namespace verbb\doxter\common\parsers;

use verbb\doxter\Doxter;

abstract class BaseParser implements BaseParserInterface
{
    // Static Methods
    // =========================================================================

    /**
     * Returns an instance of the class in called static context
     *
     * @return Object
     */
    public static function instance(): object
    {
        if (null === static::$_instance) {
            static::$_instance = new static;
        }

        return static::$_instance;
    }


    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;


    // Public Methods
    // =========================================================================

    /**
     * Reports whether the source string can be safely parsed
     *
     * @param mixed|null $source
     *
     * @return bool
     */
    public function canBeSafelyParsed(mixed $source = null): bool
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
    abstract public function parse(string $source, array $options = []): mixed;
}
