import type { ScreenshotStep } from '@verbb/craft-screenshots/types';

/** Isolate the seeded Doxter document and size the frame to its final line. */
export function createDoxterEditorFrameStep(): ScreenshotStep {
    return {
        type: 'evaluate',
        expression: `
            (() => {
                document.getElementById('doxter-screenshot-frame')?.remove();

                const editor = document.querySelector('.doxter');
                if (!(editor instanceof HTMLElement)) {
                    throw new Error('Doxter editor was not found.');
                }

                const frame = document.createElement('div');
                frame.id = 'doxter-screenshot-frame';
                frame.style.cssText = [
                    'position:fixed',
                    'left:0',
                    'top:0',
                    'width:751px',
                    'overflow:hidden',
                    'background:#ffffff',
                    'z-index:2147483646',
                ].join(';');

                editor.style.width = '751px';
                editor.style.margin = '0';
                editor.style.background = '#ffffff';
                editor.style.border = '0';
                editor.style.borderRadius = '0';
                editor.style.boxSizing = 'border-box';

                const toolbar = editor.querySelector('.editor-toolbar');
                const codeMirror = editor.querySelector('.CodeMirror');
                const codeMirrorScroll = editor.querySelector('.CodeMirror-scroll');
                const toolbarHeight = toolbar instanceof HTMLElement ? Math.ceil(toolbar.getBoundingClientRect().height) : 48;
                const lines = Array.from(editor.querySelectorAll('.CodeMirror-line')).filter((line) =>
                    line.textContent?.trim()
                );
                const lastLine = lines.at(-1);

                if (!(lastLine instanceof HTMLElement)) {
                    throw new Error('The final Doxter editor line was not found.');
                }

                const editorTop = editor.getBoundingClientRect().top;
                const cropHeight = Math.ceil(lastLine.getBoundingClientRect().bottom - editorTop + 8);

                if (cropHeight <= toolbarHeight) {
                    throw new Error('The calculated Doxter crop height is invalid.');
                }

                frame.style.height = cropHeight + 'px';
                editor.style.height = cropHeight + 'px';

                if (codeMirror instanceof HTMLElement) {
                    codeMirror.style.height = (cropHeight - toolbarHeight) + 'px';
                    codeMirror.style.minHeight = '0';
                    codeMirror.style.borderTop = '0';
                    codeMirror.style.borderRight = '0';
                    codeMirror.style.borderBottom = '0';
                    codeMirror.style.borderLeft = '0';
                    codeMirror.style.borderRadius = '0';
                }

                if (codeMirrorScroll instanceof HTMLElement) {
                    codeMirrorScroll.style.height = (cropHeight - toolbarHeight) + 'px';
                    codeMirrorScroll.style.minHeight = '0';
                }

                if (toolbar instanceof HTMLElement) {
                    toolbar.style.borderTop = '0';
                    toolbar.style.borderRight = '0';
                    toolbar.style.borderBottom = '1px solid #cbd5e1';
                    toolbar.style.borderLeft = '0';
                    toolbar.style.borderRadius = '0';
                }

                frame.appendChild(editor);
                document.body.appendChild(frame);

                document.documentElement.style.background = '#ffffff';
                document.body.style.margin = '0';
                document.body.style.overflow = 'hidden';
                window.scrollTo(0, 0);
            })();
        `,
    };
}
