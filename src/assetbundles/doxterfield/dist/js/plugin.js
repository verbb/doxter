/**
 * Doxter 3
 *
 * @author    Selvin Ortiz
 * @copyright (c) 2015 Selvin Ortiz
 */
'use strict';

var Doxter = function () {};

/**
 * Initialize and configure
 *
 * @param {String} id       The namespaced field id
 * @param {Object} settings Settings to use in editor configuration
 *
 * @returns {Doxter}
 */
Doxter.prototype.init = function (id, settings)
{
    this.id     = id;
    this.editor = {};
    this.config = this.configure(settings || {});

    return this;
};

/**
 * Creates a string with reference tags based on type and selected elements
 *
 * @param {String} type    The type of element (User, Entry, Asset, Tag)
 * @param {Array} elements The list of elements selected [1,2,3]
 *
 * @returns {string}
 */
Doxter.prototype.createReferenceTags = function (type, elements)
{
    var tags = "", tag;

    for (var i = 0; i < elements.length; i ++)
    {
        tag = type.toLowerCase() + ":" + elements[i].id;
        tags = tags + "{" + tag + "}";
    }

    return tags;
};

/**
 * Replaces text selection within the editor
 *
 * @param {String} text
 */
Doxter.prototype.replaceSelection = function (text)
{
    this.editor.codemirror.replaceSelection(text);
    this.editor.codemirror.focus();
};

/**
 * Creates an element selector modal for reference tag creation
 *
 * @param {String} type
 * @param {Object} criteria
 * @param {boolean} multiSelect
 */
Doxter.prototype.createSelectionModal = function (type, criteria, multiSelect)
{
    var self = this;

    Craft.createElementSelectorModal(
        'craft\\elements\\' + type,
        {
            multiSelect: !!multiSelect,
            criteria   : criteria || {},
            onSelect   : function (elements)
            {
                var tags = self.createReferenceTags(type, elements);

                if (tags)
                {
                    self.replaceSelection(tags);
                }
            }
        }
    );
};

Doxter.prototype.selectEntry = function()
{
    var self = this;

    return function()
    {
        self.createSelectionModal('Entry');
    }
};

Doxter.prototype.selectAsset = function()
{
    var self = this;

    return function()
    {
        self.createSelectionModal('Asset', {kind: 'image'});
    }
};

Doxter.prototype.selectUser = function()
{
    var self = this;

    return function()
    {
        self.createSelectionModal('User');
    }
};

Doxter.prototype.selectTag = function()
{
    var self = this;

    return function()
    {
        self.createSelectionModal('Tag');
    }
};

/**
 * Adds a toggle class fullscreen in order to stay on top sidebar nav ( Craft )
 * Leverages jQuery to perform the toggleClass on #container element
 * Uses SimpleMDE building Fullscreen Action.
 * @param {SimpleMDE} Obj.
 *
 */
Doxter.prototype.fullScreen = function (SimpleMDE) {
    var $container = $('#container');
    /**
     * Listens to scape key to remove Class "fullscreen"
     * from $('#container')
     */
    $(window).on({
        'keydown': function (evt) {
            if (evt.keyCode === 27 ) {
                $container.removeClass('fullscreen');
            }
        }
    })

    $container.toggleClass('fullscreen');

    // Built-in Method
    SimpleMDE.toggleFullScreen();
}

/**
 * Creates an array with the toolbar tools that are enabled
 * 
 * @param {boolean} enableParserToolbar
 * @param {Array} disabledTools The list of disabled tools (user, entry, image, tags)
 * 
 * @return {Array} 
 */
Doxter.prototype.getToolbar = function (settings)
{
    var self = this;
    var showToolbarOrHiddenIconList = settings.toolbarSettings;

    var defaultToolbar = [
        {
            name: 'bold',
            title: 'Bold (Ctrl+B',
            action: SimpleMDE.toggleBold,
            className: 'fa fa-bold'
        },
        {
            name     : 'italic',
            action   : SimpleMDE.toggleItalic,
            className: 'fa fa-italic',
            title    : 'Italic (Ctrl+I)'
        },
        {
            name     : 'quote',
            action   : SimpleMDE.toggleBlockquote,
            className: 'fa fa-quote-left',
            title    : 'Quote (Ctrl+\')'
        },
        {
            name     : 'ordered-list',
            action   : SimpleMDE.toggleOrderedList,
            className: 'fa fa-list-ol',
            title    : 'Numbered List (Ctrl+Alt+L)'
        },
        {
            name     : 'unordered-list',
            action   : SimpleMDE.toggleUnorderedList,
            className: 'fa fa-list-ul',
            title    : 'Generic List (Ctrl+L)'
        },
        {
            name     : 'link',
            action   : SimpleMDE.drawLink,
            className: 'fa fa-link',
            title    : 'Create Link (Ctrl+K)'
        },
        {
            name     : 'image',
            action   : SimpleMDE.drawImage,
            className: 'fa fa-picture-o',
            title    : 'Insert Image (Ctrl+Alt+I)'
        },
        {
            name     : 'doxter-users',
            action   : self.selectUser(),
            className: 'fa fa-users doxter-primary-icon',
            title    : 'User Reference (Ctrl+Alt+1)'
        },
        {
            name     : 'doxter-entries',
            action   : self.selectEntry(),
            className: 'fa fa-newspaper-o doxter-primary-icon',
            title    : 'Entry Reference (Ctrl+Alt+2)'
        },
        {
            name     : 'doxter-assets',
            action   : self.selectAsset(),
            className: 'fa fa-picture-o doxter-primary-icon',
            title    : 'Asset Reference (Ctrl+Alt+3)'
        },
        {
            name     : 'doxter-tags',
            action   : self.selectTag(),
            className: 'fa fa-tags doxter-primary-icon',
            title    : 'Tag Reference (Ctrl+Alt+4)'
        },
        {
            name     : 'fullscreen',
            action   : this.fullScreen, // Custom Full Screen
            className: 'fa fa-arrows-alt',
            title    : 'Toggle Fullscreen (F11)'
        }
    ];

    if (!!showToolbarOrHiddenIconList) {
        if (Array.isArray(showToolbarOrHiddenIconList)) {
            for (var i = 0; i < defaultToolbar.length; i++) {
                if (showToolbarOrHiddenIconList.indexOf(defaultToolbar[i].name) !== -1) {
                    defaultToolbar.splice(i, 1);
                    i--;
                }
            }
        }

        return defaultToolbar;
    }

    return [];
}

Doxter.prototype.configure = function (settings)
{
    var self = this;

    return {
        element: document.getElementById(self.id),
        status: settings.status || false, // autosave, lines, words, cursor
        toolbarTips: true,
        toolbarGuideIcon: false,
        autofocus: false,
        lineWrapping: !!settings.enableLineWrapping,
        indentWithTabs: !!settings.indentWithTabs,
        tabSize: settings.tabSize,
        forceSync: true,
        spellChecker: !!settings.enableSpellChecker,
        toolbar: self.getToolbar(settings)
    };
};

/**
 * Render Doxter in all its beauty
 */
Doxter.prototype.render = function ()
{
    this.editor = new SimpleMDE(this.config);
};
