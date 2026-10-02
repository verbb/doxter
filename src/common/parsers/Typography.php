<?php
namespace verbb\doxter\common\parsers;

use Craft;

use Michelf\SmartyPantsTypographer;

use PHP_Typography\PHP_Typography;
use PHP_Typography\Settings;

class Typography extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;

    private ?PHP_Typography $_typographer = null;


    // Public Methods
    // =========================================================================

    public function parse(string $source, array $options = []): mixed
    {
        $source = SmartyPantsTypographer::defaultTransform($source);

        $settings = new Settings();

        $settings->set_hyphenation($options['addTypographyHyphenation'] ?? true);
        $settings->set_hyphenation_language(Craft::$app->language);

        $this->_typographer ??= new PHP_Typography();

        return $this->_typographer->process($source, $settings);
    }
}
