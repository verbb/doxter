<?php
namespace selvinortiz\doxter\common\shortcodes;

use Craft;
use craft\elements\Asset;

use selvinortiz\doxter\models\ShortcodeModel;

use function selvinortiz\doxter\doxter;

class DoxterShortcodes
{
    /**
     * @param ShortcodeModel $code
     *
     * @return string
     */
    public function image(ShortcodeModel $code)
    {
        $src = $code->getParam('src');
        $alt = $code->getParam('alt');
        $asset = $code->getParam('asset');
        $transform = $code->getParam('transform', 'coverSmall');

        if ($asset && ($image = Craft::$app->elements->getElementById($asset))) {
            /**
             * @var Asset $image
             */
            $src = $image->getUrl($transform);

            if (empty($alt)) {
                $alt = $image->title;
            }
        }

        if (!empty($src)) {
            $vars = [
                'src' => $src,
                'alt' => $alt,
                'content' => $code->parseContent(),
                'wrapper' => $code->getParam('wrapper', 'p'),
                'class' => $code->getParam('class', 'fluid'),
            ];

            if (Craft::$app->view->doesTemplateExist('_doxter/shortcodes/image')) {
                return Craft::$app->view->renderTemplate('_doxter/shortcodes/image', $vars);
            }

            return doxter()->api->renderPluginTemplate('shortcodes/_image', $vars);
        }
    }

    /**
     * @param ShortcodeModel $code
     *
     * @return string
     */
    public function video(ShortcodeModel $code)
    {
        $src = $code->getParam('src');

        if (!empty($src)) {
            if (strpos($src, '/') !== false) {
                $src = array_pop(explode('/', $src));
            }

            $vars = [
                'src' => $src,
                'name' => $code->name,
                'title' => (int)$code->getParam('title', 0),
                'byline' => (int)$code->getParam('byline', 0),
                'color' => $code->getParam('color'),
            ];

            if (Craft::$app->view->doesTemplateExist('_doxter/shortcodes/video')) {
                return Craft::$app->view->renderTemplate('_doxter/shortcodes/video', $vars);
            }

            return doxter()->api->renderPluginTemplate('shortcodes/_video', $vars);
        }
    }

    /**
     * @param ShortcodeModel $code
     *
     * @return string
     */
    public function audio(ShortcodeModel $code)
    {
        $src = $code->getParam('src');

        if (!empty($src)) {
            $vars = [
                'src' => $src,
                'size' => $code->getParam('size', 'large'),
                'color' => $code->getParam('color', '00aabb'),
            ];

            if (Craft::$app->view->doesTemplateExist('_doxter/shortcodes/audio')) {
                return Craft::$app->view->renderTemplate('_doxter/shortcodes/audio', $vars);
            }

            return doxter()->api->renderPluginTemplate('shortcodes/_audio', $vars);
        }
    }

    /**
     * @param ShortcodeModel $code
     *
     * @return string
     */
    public function updates(ShortcodeModel $code)
    {
        $lines = array_filter(array_map('trim', explode(PHP_EOL, $code->content)));
        $notes = [];

        if (count($lines)) {
            foreach ($lines as $index => $line) {
                $line = doxter()->api->parseMarkdownInline(preg_replace(
                    '/^([ \-\+\*\=]+)?/',
                    '',
                    $line
                ));
                $type = $this->getUpdateTypeFromLine($line);
                $notes[] = [
                    'text' => $line,
                    'type' => $type,
                ];
            }
        }

        if (Craft::$app->view->doesTemplateExist('_doxter/shortcodes/updates')) {
            return Craft::$app->view->renderTemplate('_doxter/shortcodes/updates', compact('notes'));
        }

        return doxter()->api->renderPluginTemplate('shortcodes/_updates', compact('notes'));
    }

    /**
     * @param $line
     *
     * @return string
     */
    protected function getUpdateTypeFromLine($line)
    {
        if (is_string($line)) {
            $type = array_shift(explode(' ', trim($line)));

            switch (strtolower($type)) {
                case 'add':
                case 'adds':
                case 'added':
                    {
                        return 'added';
                        break;
                    }
                case 'fix':
                case 'fixes':
                case 'fixed':
                    {
                        return 'fixed';
                        break;
                    }
                case 'improve':
                case 'improves':
                case 'improved':
                    {
                        return 'improved';
                        break;
                    }
                case 'update':
                case 'updates':
                case 'updated':
                    {
                        return 'updated';
                        break;
                    }
                case 'remove':
                case 'removes':
                case 'removed':
                    {
                        return 'removed';
                        break;
                    }
                default:
                    {
                        return false;
                    }
            }
        }
    }
}
