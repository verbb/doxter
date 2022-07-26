<?php
namespace verbb\doxter\common\parsers;

use Craft;

class ReferenceTag extends BaseParser
{
    // Properties
    // =========================================================================

    /**
     * @var ReferenceTag
     */
    protected static $_instance;


    // Public Methods
    // =========================================================================

    /**
     * Parses reference tags recursively
     *
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parse(string $source, array $options = []): string
    {
        return Craft::$app->getElements()->parseRefs($source);
    }
}
