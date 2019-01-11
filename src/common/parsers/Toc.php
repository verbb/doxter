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

    protected $markdown = '';
    protected $headings = [];
    protected $anchors  = [];

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
        $this->markdown = $source;

        return $this->process();
    }

    public function process(): string
    {
        $this->buildLinkedMarkdown();

        return $this->buildTableOfContents();
    }

    protected function stringToStream(string $markdown)
    {
        $stream = fopen('php://temp', 'rb+');

        fwrite($stream, $markdown);
        rewind($stream);

        return $stream;
    }

    protected function buildLinkedMarkdown()
    {
        $stream   = $this->stringToStream($this->markdown);
        $markdown = '';

        while ($line = fgets($stream))
        {
            if (
                false !== strpos($line, '#') &&
                preg_match('/^(?P<prespace>\s+)?(?P<level>#{1,6})(?P<title>.*)(?P<postspace>\s+)?$/', $line, $matches) &&
                isset($matches['level'], $matches['title']))
            {
                $anchor = $this->getAnchorSlug($matches['title']);

                $this->headings[] = [
                    'level'  => strlen($matches['level']),
                    'title'  => $matches['title'],
                    'anchor' => $anchor,
                ];

                $markdown .= sprintf(
                    '%s%s <a name="%s" id="%s">%s</a>%s',
                    $matches['prespace'] ?? '',
                    $matches['level'],
                    $anchor,
                    $anchor,
                    trim($matches['title']),
                    $matches['postspace'] ?? ''
                );
            }
            else
            {
                $markdown .= $line;
            }
        }

        return $markdown;
    }

    protected function getAnchorSlug(string $string): string
    {
        $anchor = ElementHelper::createSlug(trim($string));

        if (isset($this->anchors[$anchor]))
        {
            $this->anchors[$anchor] = ($this->anchors[$anchor] + 1);

            $anchor .= '-'.$this->anchors[$anchor];
        }
        else
        {
            $this->anchors[$anchor] = 1;
        }

        return $anchor;
    }

    protected function buildTableOfContents(): string
    {
        if (!count($this->headings))
        {
            return '';
        }

        $markdown = '';

        foreach ($this->headings as $heading)
        {
            $markdown .= sprintf(
                '%s [%s](#%s)'."\n",
                str_repeat('    ', $heading['level'] - 1).'*',
                trim($heading['title']),
                $heading['anchor']
            );
        }

        return $markdown;
    }
}
