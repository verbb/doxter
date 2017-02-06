<?php
namespace selvinortiz\doxter\services;

use Craft;
use craft\web\View;
use craft\base\Component;
use craft\helpers\Template;
use craft\helpers\ArrayHelper;

use selvinortiz\doxter\events\DoxterEvent;
use selvinortiz\doxter\common\parsers\Header;
use selvinortiz\doxter\common\parsers\Markdown;
use selvinortiz\doxter\common\parsers\CodeBlock;
use selvinortiz\doxter\common\parsers\Shortcode;
use selvinortiz\doxter\common\parsers\Typography;
use selvinortiz\doxter\common\parsers\ReferenceTag;

use function selvinortiz\doxter\doxter;

/**
 * Class DoxterService
 *
 * @package selvinortiz\doxter\services
 */
class DoxterService extends Component {

    const EVENT_BEFORE_TYPOGRAPHY = 'beforeTypography';
    const EVENT_BEFORE_HEADER_PARSE = 'beforeHeaderParsing';
    const EVENT_BEFORE_MARKDOWN_PARSE = 'beforeMarkdownParsing';
    const EVENT_BEFORE_SHORTCODE_PARSE = 'beforeShortcodeParsing';
    const EVENT_BEFORE_CODEBLOCK_PARSE = 'beforeCodeBlockParsing';
    const EVENT_BEFORE_REFERENCETAG_PARSE = 'beforeReferenceTagParsing';

    /**
     * Parses source markdown into valid html using various rules and parsers
     *
     * @param string $source  The markdown source to parse
     * @param array  $options Passed in parameters via a template filter call
     *
     * @return \Twig_Markup
     */
    public function parse($source, array $options = []) {
        $codeBlockSnippet    = null;
        $addHeaderAnchors    = true;
        $addHeaderAnchorsTo  = ['h1', 'h2', 'h3'];
        $addTypographyStyles = true;
        $startingHeaderLevel = 1;
        $parseReferenceTags  = true;
        $parseShortcodes     = true;
        $options             = array_merge(doxter()->getSettings()->getAttributes(), $options);

        extract($options);

        // Parsing reference tags first so that we can parse markdown within them
        if ($parseReferenceTags) {
            $this->trigger(
                DoxterService::EVENT_BEFORE_REFERENCETAG_PARSE,
                new DoxterEvent(compact('source', 'options'))
            );

            $source = $this->parseReferenceTags($source, $options);
        }

        if ($parseShortcodes) {
            $this->trigger(
                DoxterService::EVENT_BEFORE_SHORTCODE_PARSE,
                new DoxterEvent(compact('source'))
            );

            $source = $this->parseShortcodes($source);
        }

        $this->trigger(
            DoxterService::EVENT_BEFORE_MARKDOWN_PARSE,
            new DoxterEvent(compact('source'))
        );

        $source = $this->parseMarkdown($source);

        $this->trigger(
            DoxterService::EVENT_BEFORE_CODEBLOCK_PARSE,
            new DoxterEvent(compact('source'))
        );

        $source = $this->parseCodeBlocks($source, compact('codeBlockSnippet'));

        if ($addHeaderAnchors) {
            $this->trigger(
                DoxterService::EVENT_BEFORE_HEADER_PARSE,
                new DoxterEvent(compact('source'))
            );

            $source = $this->parseHeaders($source, compact('addHeaderAnchorsTo', 'startingHeaderLevel'));
        }

        if ($addTypographyStyles || true) {
            $this->trigger(
                DoxterService::EVENT_BEFORE_TYPOGRAPHY,
                new DoxterEvent(compact('source'))
            );

            $source = $this->parseTypography($source);
        }

        return Template::raw($source);
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parseMarkdown($source, array $options = []) {
        return Markdown::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseMarkdownInline($source) {
        return Markdown::instance()->parseInline($source);
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parseReferenceTags($source, array $options = []) {
        return ReferenceTag::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parseHeaders($source, array $options = []) {
        return Header::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parseCodeBlocks($source, array $options = []) {
        return CodeBlock::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array  $options
     *
     * @return string
     */
    public function parseShortcodes($source, array $options = []) {
        Shortcode::instance()->registerShortcodes(doxter()->registerShortcodes());

        return Shortcode::instance()->parse($source, $options);
    }

    public function parseTypography($source, array $options = []) {
        return Typography::instance()->parse($source, $options);
    }

    /**
     * Ensures that a valid list of parseable headers is returned
     *
     * @param string $headerString
     *
     * @return array
     */
    public function getHeadersToParse($headerString = '') {
        $allowedHeaders = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        $headers = ArrayHelper::filterEmptyStringsFromArray(ArrayHelper::toArray($headerString));

        if (count($headers)) {
            foreach ($headers as $key => $header) {
                $header = strtolower($header);

                if (! in_array($header, $allowedHeaders)) {
                    unset($headers[$key]);
                }
            }
        }

        return $headers;
    }

    /**
     * Renders a plugin template whether the request is from the control panel or the site
     *
     * @param string $template
     * @param array  $vars
     *
     * @return string
     */
    public function renderPluginTemplate($template, array $vars = []) {
        $rendered = null;
        $template = sprintf('doxter/%s', $template);
        $oldMode  = Craft::$app->view->getTemplateMode();

        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        if (Craft::$app->view->doesTemplateExist($template)) {
            $rendered = Craft::$app->view->renderTemplate($template, $vars);
        }

        Craft::$app->view->setTemplateMode($oldMode);

        return $rendered;
    }

    /**
     * @param array $shortcodes
     */
    public function registerShortcodes(array $shortcodes) {
        Shortcode::instance()->registerShortcodes($shortcodes);
    }

    /**
     * @param $shortcode
     * @param $callback
     */
    public function registerShortcode($shortcode, $callback) {
        Shortcode::instance()->registerShortcode($shortcode, $callback);
    }
}
