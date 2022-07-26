<?php
namespace verbb\doxter\models;

use craft\base\Model;

class Toc extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $text;

    /**
     * @var int
     */
    public $level;

    /**
     * @var string
     */
    private $_hash = null;

    /**
     * @var string
     */
    private $_uid = null;


    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function getHash(): string
    {
        if ($this->_hash === null) {
            $this->_hash = sprintf('#%s', $this->id);
        }

        return $this->_hash;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        if ($this->_uid === null) {
            $this->_uid = md5(time() . $this->id);
        }

        return $this->_uid;
    }
}
