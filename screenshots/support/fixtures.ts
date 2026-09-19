import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

type DoxterFixture = {
    fieldId: number;
    entryEditRoute: string;
};

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-doxter-entry.php'), 'utf8');

/** Seed the long-form Markdown entry used by the Doxter feature capture. */
export async function seedDoxterFixture(context: ScreenshotSetupContext): Promise<DoxterFixture> {
    const output = await context.runCraftScript(seedScript, { label: 'seed-doxter-entry' });
    const fixture = JSON.parse(output.trim()) as DoxterFixture;

    if (!fixture.fieldId || !fixture.entryEditRoute) {
        throw new Error(`Invalid Doxter fixture payload: ${output}`);
    }

    return fixture;
}
