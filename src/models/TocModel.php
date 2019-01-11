<?php
namespace selvinortiz\doxter\models;

use craft\base\Model;

class TocModel extends Model
{
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

    /**
     * @return string
     */
    public function getHash()
    {
        if ($this->_hash === null)
        {
            $this->_hash = sprintf('#%s', $this->id);
        }

        return $this->_hash;
    }

    /**
     * @return string
     */
    public function getUid()
    {
        if ($this->_uid === null)
        {
            $this->_uid = md5(time().$this->id);
        }

        return $this->_uid;
    }
}
