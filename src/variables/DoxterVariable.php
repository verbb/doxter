<?php
namespace verbb\doxter\variables;

use verbb\doxter\Doxter;

class DoxterVariable
{
    // Public Methods
    // =========================================================================

    public function getPluginName(): string
    {
        return Doxter::$plugin->getPluginName();
    }
}
