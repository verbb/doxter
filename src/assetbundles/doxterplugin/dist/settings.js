var $settingsCodeBlockSnippetTextArea = $('#settings-codeBlockSnippet');

$('.addPrismSnippetBtn').on({
    click: function(event) {
        event.preventDefault();

        $settingsCodeBlockSnippetTextArea.val('<pre><code class="language-{languageClass}">{sourceCode}</code></pre>')
    }
});

$('.addRainbowSnippetBtn').on({
    click: function(event) {
        event.preventDefault();
        $settingsCodeBlockSnippetTextArea.val('<pre><code data-language="language-{languageClass}">{sourceCode}</code></pre>')
    }
});
