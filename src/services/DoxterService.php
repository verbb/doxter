<?php
namespace selvinortiz\doxter\services;

use yii\base\Event;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use craft\helpers\ArrayHelper;

use selvinortiz\doxter\Doxter;
use selvinortiz\doxter\common\parsers\Header;
use selvinortiz\doxter\common\parsers\Markdown;
use selvinortiz\doxter\common\parsers\CodeBlock;
use selvinortiz\doxter\common\parsers\Shortcode;
use selvinortiz\doxter\common\parsers\ReferenceTag;

/**
 * Class DoxterService
 *
 * @package selvinortiz\doxter\services
 */
class DoxterService extends Component {

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
        $options             = array_merge(Doxter::getInstance()->getSettings()->getAttributes(), $options);

        extract($options);

        // Parsing reference tags first so that we can parse markdown within them
        if ($parseReferenceTags) {
            // if ($this->onBeforeReferenceTagParsing(compact('source', 'options'))) {}
            $source = $this->parseReferenceTags($source, $options);
        }

        if ($parseShortcodes) {
            // if ($this->onBeforeShortcodeParsing(compact('source'))) {}
            $source = $this->parseShortcodes($source);
        }

        // if ($this->onBeforeMarkdownParsing(compact('source'))) {}
        $source = $this->parseMarkdown($source);

        // if ($this->onBeforeCodeBlockParsing(compact('source', 'codeBlockSnippet'))) {}
        $source = $this->parseCodeBlocks($source, compact('codeBlockSnippet'));

        if ($addHeaderAnchors) {
            // if ($this->onBeforeHeaderParsing(compact('source', 'addHeaderAnchorsTo'))) {}
            $source = $this->parseHeaders($source, compact('addHeaderAnchorsTo', 'startingHeaderLevel'));
        }

        if ($addTypographyStyles) {
            // $source = $this->addTypographyStyles($source, $options);
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
        Shortcode::instance()->registerShortcodes(Doxter::getInstance()->registerShortcodes());

        return Shortcode::instance()->parse($source, $options);
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function addTypographyStyles($source) {
        if (! function_exists('\\typogrify')) {
            // require_once(dirname(__FILE__) . '/../common/parsedown/Typography.php');
        }

        try {
            // return \typogrify($source);
        }
        catch (\Exception $e) {
            // return $source;
        }
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
     * Renders a plugin template whether the request is for the control panel or the site
     *
     * @param string $template
     * @param array  $vars
     *
     * @return string
     */
    public function renderPluginTemplate($template, array $vars = []) {
        /*
        $path     = Craft::$app->path->getTemplatesPath();
        $rendered = null;

        Craft::$app->path->setTemplatesPath(Craft::$app->path->getPluginsPath() . '/doxter/templates/');

        if (Craft::$app->view->doesTemplateExist($template)) {
            $rendered = Craft::$app->view->renderTemplate($template, $vars);
        }

        Craft::$app->path->setTemplatesPath($path);

        return $rendered;
        */
    }

    /**
     * Returns the value of a deeply nested array key by using dot notation
     *
     * @param string $key
     * @param array  $data
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getValueByKey($key, array $data, $default = null) {
        if (! is_string($key) || empty($key) || ! count($data)) {
            return $default;
        }

        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                if (! array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        return array_key_exists($key, $data) ? $data[$key] : $default;
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

    /**
     * @param array $params
     *
     * @return bool
     */
    public function onBeforeReferenceTagParsing(array $params = []) {
        return $this->raiseOwnEvent(__FUNCTION__, $params);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function onBeforeShortcodeParsing(array $params = []) {
        return $this->raiseOwnEvent(__FUNCTION__, $params);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function onBeforeMarkdownParsing(array $params = []) {
        return $this->raiseOwnEvent(__FUNCTION__, $params);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function onBeforeCodeBlockParsing(array $params = []) {
        return $this->raiseOwnEvent(__FUNCTION__, $params);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function onBeforeHeaderParsing(array $params = []) {
        return $this->raiseOwnEvent(__FUNCTION__, $params);
    }

    /**
     * @param string $name
     * @param array  $params
     *
     * @return bool
     * @throws \Exception
     */
    protected function raiseOwnEvent($name, array $params = []) {
        $name  = explode('\\', $name);
        $event = new Event($this, $params);

        Craft::$app->trigger('doxter.' . array_pop($name), $event);

        return $event->handled;
    }
}
