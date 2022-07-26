<?php
namespace verbb\doxter\services;

use verbb\doxter\Doxter;
use verbb\doxter\common\parsers\Toc;
use verbb\doxter\common\parsers\Header;
use verbb\doxter\common\parsers\Markdown;
use verbb\doxter\common\parsers\CodeBlock;
use verbb\doxter\common\parsers\Shortcode;
use verbb\doxter\common\parsers\Typography;
use verbb\doxter\common\parsers\ReferenceTag;
use verbb\doxter\events\DoxterEvent;

use Craft;
use craft\base\Component;
use craft\helpers\ArrayHelper;
use craft\helpers\Template;
use craft\web\View;

use Spatie\YamlFrontMatter\YamlFrontMatter;
use yii\base\Exception;
use Twig\Markup;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use Twig\Error\LoaderError;

class Service extends Component
{
    // Constants
    // =========================================================================

    const EVENT_BEFORE_TYPOGRAPHY = 'beforeTypography';
    const EVENT_BEFORE_HEADER_PARSE = 'beforeHeaderParsing';
    const EVENT_BEFORE_MARKDOWN_PARSE = 'beforeMarkdownParsing';
    const EVENT_BEFORE_SHORTCODE_PARSE = 'beforeShortcodeParsing';
    const EVENT_BEFORE_CODEBLOCK_PARSE = 'beforeCodeBlockParsing';
    const EVENT_BEFORE_REFERENCETAG_PARSE = 'beforeReferenceTagParsing';
    const EVENT_AFTER_PARSE = 'afterParsing';


    // Public Methods
    // =========================================================================

    /**
     * Parses source markdown into valid html using various rules and parsers
     *
     * @param string|null $source The markdown source to parse
     * @param array $options Passed in parameters via a template filter call
     *
     * @return Markup
     */
    public function parse(string $source = null, array $options = []): Markup
    {
        if (!$this->canBeSafelyParsed($source)) {
            return new Markup('', Craft::$app->charset);
        }

        $options = array_merge(Doxter::$plugin->getSettings()->getAttributes(), $options);

        extract($options);

        // Parsing reference tags first so that we can parse markdown within them
        if ($options['parseReferenceTags'] ?? false) {
            $this->trigger(self::EVENT_BEFORE_REFERENCETAG_PARSE, new DoxterEvent(compact('source')));

            $source = $this->parseReferenceTags($source, $options);
        }

        if ($options['parseShortcodes'] ?? false) {
            $this->trigger(self::EVENT_BEFORE_SHORTCODE_PARSE, new DoxterEvent(compact('source')));

            $source = $this->parseShortcodes($source);
        }

        $this->trigger(
            self::EVENT_BEFORE_MARKDOWN_PARSE,
            new DoxterEvent(compact('source'))
        );

        $source = $this->parseMarkdown($source);

        $this->trigger(self::EVENT_BEFORE_CODEBLOCK_PARSE, new DoxterEvent(compact('source')));

        $source = $this->parseCodeBlocks($source, compact('codeBlockSnippet'));

        if ($options['addHeaderAnchors'] ?? false) {
            $this->trigger(self::EVENT_BEFORE_HEADER_PARSE, new DoxterEvent(compact('source')));

            $source = $this->parseHeaders($source, compact('addHeaderAnchorsTo', 'startingHeaderLevel'));
        }

        if ($options['addTypographyStyles']) {
            $this->trigger(self::EVENT_BEFORE_TYPOGRAPHY, new DoxterEvent(compact('source')));

            $source = $this->parseTypography($source, compact('addTypographyHyphenation'));
        }

        $source = Doxter::$plugin->getService()->decodeUnicodeEntities($source);

        // Create an event so we can update the source from it later
        $event = new DoxterEvent(compact('source'));

        $this->trigger(self::EVENT_AFTER_PARSE, $event);

        $source = $event->source;

        return Template::raw($source);
    }

    /**
     * Parses markdown and front matter from a file into valid html using various rules and parsers
     *
     * @param $slug
     * @param array $options Passed in parameters via a template filter call
     *
     * @return Markup
     * @throws Exception
     */
    public function parseFile($slug, array $options = []): ?Markup
    {
        $file = sprintf('%s/_doxter/%s.md', Craft::$app->path->getSiteTemplatesPath(), $slug);

        if (!is_readable($file)) {
            return null;
        }

        $md = YamlFrontMatter::parseFile($file);

        return array_merge($md->matter(), [
            'body' => $this->parse($md->body(), $options),
        ]);
    }

    public function parseToc(string $source = null, array $options = [])
    {
        return Toc::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseMarkdown(string $source, array $options = []): string
    {
        return Markdown::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parseMarkdownInline(string $source): string
    {
        return Markdown::instance()->parseInline($source);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseReferenceTags(string $source, array $options = []): string
    {
        return ReferenceTag::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseHeaders(string $source, array $options = []): string
    {
        return Header::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseCodeBlocks(string $source, array $options = []): string
    {
        return CodeBlock::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseShortcodes(string $source, array $options = []): string
    {
        return Shortcode::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     * @param array $options
     *
     * @return string
     */
    public function parseTypography(string $source, array $options = []): string
    {
        return Typography::instance()->parse($source, $options);
    }

    /**
     * Ensures that a valid list of parseable headers is returned
     *
     * @param string $headerString
     *
     * @return array
     */
    public function getHeadersToParse(string $headerString = ''): array
    {
        $allowedHeaders = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

        $headers = ArrayHelper::filterEmptyStringsFromArray(ArrayHelper::toArray($headerString));

        if (count($headers)) {
            foreach ($headers as $key => $header) {
                $header = strtolower($header);

                if (!in_array($header, $allowedHeaders)) {
                    unset($headers[$key]);
                }
            }
        }

        return $headers;
    }

    /**
     * @param string $template
     * @param array $vars
     *
     * @return string|null
     *
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderPluginTemplate(string $template, array $vars = []): ?string
    {
        $view = Craft::$app->getView();

        $rendered = null;
        $template = sprintf('doxter/%s', $template);
        $oldMode = $view->getTemplateMode();

        $view->setTemplateMode(View::TEMPLATE_MODE_CP);

        if ($view->doesTemplateExist($template)) {
            $rendered = $view->renderTemplate($template, $vars);
        }

        $view->setTemplateMode($oldMode);

        return $rendered;
    }

    /**
     * @param array $shortcodes
     */
    public function registerShortcodes(array $shortcodes): void
    {
        Shortcode::instance()->registerShortcodes($shortcodes);
    }

    /**
     * @param $shortcode
     * @param $callback
     */
    public function registerShortcode($shortcode, $callback): void
    {

        Shortcode::instance()->registerShortcode($shortcode, $callback);
    }

    /**
     * Decodes html entities starting with &#x generally associated with emoji
     * Handles emoji within code blocks that are in the &amp;#x format
     *
     * @param $value
     *
     * @return string|string[]|null
     */
    public function decodeUnicodeEntities($value)
    {
        return preg_replace_callback('/((\&\#x[a-z\d]+\;)|(\&amp\;\#x[a-z\d]+\;))/i', function($matches) {
            return html_entity_decode($matches[1], ENT_HTML5, Craft::$app->charset);
        }, $value);
    }

    /**
     * Reports whether the source string can be safely parsed
     *
     * @param mixed $source
     *
     * @return bool
     */
    public function canBeSafelyParsed($source = null): bool
    {
        if (empty($source)) {
            return false;
        }

        return (is_string($source) || is_callable([$source, '__toString']));
    }
}
