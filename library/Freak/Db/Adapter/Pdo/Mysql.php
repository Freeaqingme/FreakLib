<?php

class Freak_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {

    protected $_charset;

    protected $_commitStack = array();

    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }

        if (!empty($this->_config['charset'])) {
            $this->_charset = $this->_config['charset'];
            unset($this->_config['charset']);
        }

        parent::_connect();

        if($this->_charset != null) {
            $this->_connection->query("SET NAMES '" . $this->_charset . "'");
        }
    }

    public function beginTransaction() {
        $count = count($this->_commitStack);
        array_push($this->_commitStack, 'ZFsavePoint'.$count);
        if($count == 0)
        {
            parent::beginTransaction();
        } else {
            $this->query('SAVEPOINT ZFsavePoint'.$count);
        }

        return $this;
    }

    public function commit() {
        if(count($this->_commitStack) == 1) {
            parent::commit();
        }

        array_pop($this->_commitStack);
        return $this;
    }

    public function rollback() {
        $lastTransaction = array_pop($this->_commitStack);
        if(count($this->_commitStack == 0)) {
            parent::rollback();
        } else {
            $this->query('ROLLBACK TO SAVEPOINT '.$lastTransaction);
        }

        return $this;
    }
}
