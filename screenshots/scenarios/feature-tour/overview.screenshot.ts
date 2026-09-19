import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedDoxterFixture } from '../../support/fixtures';
import { createDoxterEditorFrameStep } from '../../support/presets';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'doxter-feature-tour-overview',
    output: 'feature-tour/doxter-craft5-toc.png',
    route: () => entryEditRoute,
    viewport: {
        width: 820,
        height: 1100,
        deviceScaleFactor: 2,
    },
    expectedOutput: {
        width: 1502,
        height: 1274,
    },
    async setup(context) {
        const fixture = await seedDoxterFixture(context);
        entryEditRoute = fixture.entryEditRoute;
    },
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '.doxter .editor-toolbar', state: 'visible' },
        { type: 'selector', selector: '.doxter .CodeMirror', state: 'visible' },
    ],
    preSteps: [
        createDoxterEditorFrameStep(),
        { type: 'wait', waitFor: { type: 'selector', selector: '#doxter-screenshot-frame', state: 'visible' } },
        { type: 'wait', waitFor: { type: 'timeout', ms: 300 } },
    ],
    target: {
        type: 'selector',
        selector: '#doxter-screenshot-frame',
        padding: 0,
    },
    caption: 'A table-of-contents article in Doxter’s current Craft 5 editor.',
    intent: 'Shows only the useful seeded table-of-contents example, with the frame ending immediately after its final line.',
});
