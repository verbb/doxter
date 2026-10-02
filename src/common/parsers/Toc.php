<?php
namespace verbb\doxter\common\parsers;

use verbb\doxter\models\Toc as TocModel;

use DOMDocument;
use DOMElement;
use DOMXPath;

class Toc extends BaseParser
{
    // Properties
    // =========================================================================

    protected static ?BaseParserInterface $_instance = null;


    // Public Methods
    // =========================================================================

    public function parse(string $source, array $options = []): mixed
    {
        return $this->getToc($source);
    }


    // Protected Methods
    // =========================================================================

    /**
     * @return array
     */
    protected function getToc(string $source): array
    {
        $tocs = [];

        if (trim($source) === '') {
            return $tocs;
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML(
                '<!doctype html><html><head><meta charset="UTF-8"></head><body>' . $source . '</body></html>',
                LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING,
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $xpath = new DOMXPath($document);
        $headings = $xpath->query('//h1[@id] | //h2[@id] | //h3[@id] | //h4[@id] | //h5[@id] | //h6[@id]');

        foreach ($headings as $heading) {
            if (!$heading instanceof DOMElement) {
                continue;
            }

            $id = trim($heading->getAttribute('id'));
            $text = $this->_getHeadingText($heading);

            if ($id === '' || $text === '') {
                continue;
            }

            $toc = new TocModel();
            $toc->id = $id;
            $toc->text = $text;
            $toc->level = (int)substr($heading->tagName, 1);

            $tocs[] = $toc;
        }

        return $tocs;
    }


    // Private Methods
    // =========================================================================

    private function _getHeadingText(DOMElement $heading): string
    {
        $text = '';

        foreach ($heading->childNodes as $child) {
            if ($child instanceof DOMElement && $this->_isGeneratedAnchor($child)) {
                continue;
            }

            $text .= $child->textContent;
        }

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private function _isGeneratedAnchor(DOMElement $element): bool
    {
        if (strtolower($element->tagName) !== 'a') {
            return false;
        }

        $classes = preg_split('/\s+/', trim($element->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY);

        return in_array('anchor', $classes ?: [], true);
    }
}
