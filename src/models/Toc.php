<?php
namespace verbb\doxter\models;

use craft\base\Model;

class Toc extends Model
{
    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $text = null;
    public ?int $level = null;
    
    private ?string $_hash = null;
    private ?string $_uid = null;


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
