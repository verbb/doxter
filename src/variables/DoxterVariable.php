<?php
namespace selvinortiz\doxter\variables;

use selvinortiz\doxter\Doxter;

/**
 * Class DoxterVariable
 *
 * @package selvinortiz\doxter\variables
 */
class DoxterVariable
{
    public function getVersion()
    {
        Doxter::getInstance()->version;
    }
}
