<?php
namespace selvinortiz\doxter\common\parsers;

use Michelf\SmartyPantsTypographer;

use Craft;

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
        $source = SmartyPantsTypographer::defaultTransform($source);

        $settings = new \PHP_Typography\Settings();

        $settings->set_hyphenation($options['addTypographyHyphenation'] ?? true);
        $settings->set_hyphenation_language(Craft::$app->language);
        
        $typographer = new \PHP_Typography\PHP_Typography();

        return $typographer->process($source, $settings);
    }
}
