<?php
namespace selvinortiz\doxter\common\parsers;

use Craft;

/**
 * Class ReferenceTag
 *
 * @package selvinortiz\doxter\common\parsers
 */
class ReferenceTag extends BaseParser
{
    /**
     * @var ReferenceTag
     */
    protected static $_instance;

    /**
     * Parses reference tags recursively
     *
     * @param string $source
     * @param array  $options
     *
     * @return    string
     */
    public function parse($source, array $options = [])
    {
        return Craft::$app->getElements()->parseRefs($source);
    }
}
