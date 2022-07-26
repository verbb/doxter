<?php
namespace verbb\doxter\common\parsers;

use verbb\doxter\models\Toc as TocModel;

use craft\helpers\ElementHelper;

class Toc extends BaseParser
{
    // Properties
    // =========================================================================

    /**
     * @var Toc
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
     * @return array
     */
    public function parse(string $source, array $options = []): array
    {
        return $this->getToc($source);
    }


    // Protected Methods
    // =========================================================================

    /**
     * @param $source
     *
     * @return array
     */
    protected function getToc($source): array
    {
        $tocs = [];

        // Ensure using only "\n" as line-break
        $source = str_replace(["\r\n", "\r"], "\n", $source);

        preg_match_all(
            '/^(?:=|#).*$/m',
            $source,
            $matches,
            PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE
        );

        $sourceLength = strlen($source);

        foreach ($matches[0] as $item) {
            $mark = substr($item[0], 0, 1);

            if ($mark == '#') {
                $text = $item[0];
                $level = strrpos($text, '#') + 1;
                $text = substr($text, $level);
            } else {
                // Text is the previous line (empty if <hr>)
                $offset = $item[1];
                $prevOffset = strrpos($source, "\n", -($sourceLength - $offset + 2));
                $text = substr($source, $prevOffset, $offset - $prevOffset - 1);
                $text = trim($text);
                $level = $mark == '=' ? 1 : 2;
            }

            if (!trim($text) || strpos($text, '|') !== false) {
                // Item is a horizontal separator or a table header, don't mind
                continue;
            }

            $id = ElementHelper::generateSlug(trim($text));

            $toc = new TocModel();
            $toc->id = $id;
            $toc->text = trim($text);
            $toc->level = $level;

            $tocs[] = $toc;
        }

        return $tocs;
    }
}
