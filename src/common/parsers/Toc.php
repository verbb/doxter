<?php
namespace selvinortiz\doxter\common\parsers;

use craft\helpers\ElementHelper;

/**
 * Class Toc
 *
 * @package selvinortiz\doxter\common\parsers
 */
class Toc extends BaseParser
{
    /**
     * @var Toc
     */
    protected static $_instance;

    /**
     * Parses reference tags recursively
     *
     * @param string $source
     * @param array  $options
     *
     * @return    array
     */
    public function parse($source, array $options = [])
    {
        return $this->getToc($source);
    }

    protected function getToc($source)
    {
        $toc = [];

        // Ensure using only "\n" as line-break
        $source = str_replace(["\r\n", "\r"], "\n", $source);

        preg_match_all(
            '/^(?:=|-|#).*$/m',
            $source,
            $matches,
            PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE
        );

        // preprocess: iterate matched lines to create an array of items
        // where each item is an array(level, text)
        $sourceLength = strlen($source);
        foreach ($matches[0] as $item)
        {
            $mark = substr($item[0], 0, 1);

            if ($mark == '#')
            {
                $item_text  = $item[0];
                $item_level = strrpos($item_text, '#') + 1;
                $item_text  = substr($item_text, $item_level);
            }
            else
            {
                // text is the previous line (empty if <hr>)
                $item_offset      = $item[1];
                $prev_line_offset = strrpos($source, "\n", -($sourceLength - $item_offset + 2));
                $item_text        =
                    substr($source, $prev_line_offset, $item_offset - $prev_line_offset - 1);
                $item_text        = trim($item_text);
                $item_level       = $mark == '=' ? 1 : 2;
            }
            if (!trim($item_text) || strpos($item_text, '|') !== false)
            {
                // item is an horizontal separator or a table header, don't mind
                continue;
            }
            $toc[] = ['level' => $item_level, 'slug' => ElementHelper::createSlug(trim($item_text)), 'text' => trim($item_text)];
        }

        return $toc;
    }
}
