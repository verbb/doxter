<?php
namespace selvinortiz\doxter\common\parsers;

use Michelf\SmartyPantsTypographer;

/**
 * Class Typography
 *
 * @package Craft
 */
class Typography extends BaseParser
{
    /**
     * @var Typography
     */
    protected static $_instance;

    public function parse($source, array $options = [])
    {
        return SmartyPantsTypographer::defaultTransform($source);
    }
}
