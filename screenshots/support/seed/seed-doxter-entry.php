/** Seed a Doxter field and long-form Markdown entry for the feature screenshot. */

use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
use craft\helpers\Json;
use craft\models\EntryType;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use verbb\doxter\fields\Doxter;

$fields = Craft::$app->getFields();
$entries = Craft::$app->getEntries();
$site = Craft::$app->getSites()->getPrimarySite();
$fieldHandle = 'docsScreenshotDoxter';
$sectionHandle = 'docsScreenshotDoxter';

$field = $fields->getFieldByHandle($fieldHandle);

if (!$field instanceof Doxter) {
    $field = new Doxter([
        'name' => 'Article body',
        'handle' => $fieldHandle,
        'showToolbar' => true,
        'enableLineWrapping' => true,
        'enableSpellChecker' => false,
        'enabledToolbarIconNames' => [
            'bold',
            'italic',
            'quote',
            'ordered-list',
            'unordered-list',
            'link',
            'image',
            'doxter-users',
            'doxter-entries',
            'doxter-assets',
            'doxter-tags',
        ],
    ]);

    if (!$fields->saveField($field)) {
        throw new RuntimeException('Unable to save Doxter field: ' . Json::encode($field->getErrors()));
    }
}

$section = $entries->getSectionByHandle($sectionHandle);

if (!$section) {
    $entryType = new EntryType([
        'name' => 'Doxter articles',
        'handle' => $sectionHandle . 'Type',
        'hasTitleField' => true,
    ]);

    $layout = new FieldLayout(['type' => Entry::class]);
    $tab = new FieldLayoutTab([
        'name' => Craft::t('app', 'Content'),
        'layout' => $layout,
    ]);
    $tab->setElements([new CustomField($field)]);
    $layout->setTabs([$tab]);
    $entryType->setFieldLayout($layout);

    if (!$entries->saveEntryType($entryType)) {
        throw new RuntimeException('Unable to save Doxter entry type: ' . Json::encode($entryType->getErrors()));
    }

    $section = new Section([
        'name' => 'Doxter articles',
        'handle' => $sectionHandle,
        'type' => Section::TYPE_CHANNEL,
    ]);
    $section->setEntryTypes([$entryType]);
    $section->setSiteSettings([
        new Section_SiteSettings([
            'siteId' => $site->id,
            'enabledByDefault' => true,
            'hasUrls' => true,
            'uriFormat' => 'articles/{slug}',
            'template' => '_screenshot/article',
        ]),
    ]);

    if (!$entries->saveSection($section)) {
        throw new RuntimeException('Unable to save Doxter section: ' . Json::encode($section->getErrors()));
    }
}

$entryType = $entries->getEntryTypesBySectionId($section->id)[0] ?? null;

if (!$entryType) {
    throw new RuntimeException('Doxter section has no entry type.');
}

$markdown = <<<'MARKDOWN'
# Table of Contents
> A “bring your own HTML” flat structure for creating links to important sections in your document.

## How to use
Tables of contents are part of the Doxter field API. That means you can generate structured navigation whenever you are rendering a Doxter field.

To generate a table of contents for your document, use the `toc` method available on your Doxter field.

Here is a quick example of how you could use the generated table of contents for a sidebar.

```twig
{% set tableOfContents = entry.doxterFieldHandle.toc %}

{% set sidebarContent %}
  <ul>
    {% for item in tableOfContents %}
      <li>
        <a href="{{ item.hash }}">{{ item.text }}</a>
      </li>
    {% endfor %}
  </ul>
{% endset %}
```
MARKDOWN;

$entry = Entry::find()
    ->sectionId($section->id)
    ->slug('table-of-contents')
    ->siteId($site->id)
    ->status(null)
    ->one();

if (!$entry) {
    $entry = new Entry([
        'sectionId' => $section->id,
        'typeId' => $entryType->id,
        'siteId' => $site->id,
        'slug' => 'table-of-contents',
        'enabled' => true,
    ]);
}

$entry->title = 'Table of Contents';
$entry->setFieldValue($fieldHandle, $markdown);

if (!Craft::$app->getElements()->saveElement($entry)) {
    throw new RuntimeException('Unable to save Doxter entry: ' . Json::encode($entry->getErrors()));
}

$entryEditPath = parse_url((string)$entry->getCpEditUrl(), PHP_URL_PATH);

echo Json::encode([
    'fieldId' => (int)$field->id,
    'entryEditRoute' => $entryEditPath,
], JSON_THROW_ON_ERROR);
