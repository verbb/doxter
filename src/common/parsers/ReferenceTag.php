<?php
namespace verbb\doxter\common\parsers;

use Craft;

class ReferenceTag extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;


    // Public Methods
    // =========================================================================

    /**
     * Parses reference tags recursively
     *
     * @param string $source
     * @param array $options
     *
     * @return mixed
     */
    public function parse(string $source, array $options = []): mixed
    {
        return Craft::$app->getElements()->parseRefs($source);
    }
}
