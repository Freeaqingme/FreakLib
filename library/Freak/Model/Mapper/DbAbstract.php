<?php
/**
 * Abstract datamapper
 */
abstract class Freak_Model_Mapper_DbAbstract
    extends Freak_Model_Mapper_MapperAbstract
{
    /**
     * Default adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected static $_defaultAdapter;

    /**
     * Actual adapter to use
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    protected $_lastQuery;

    /**
     * Instantiate a new mapper with a specific adapter.
     *
     * If no adapter is defined, the default adapter is used. If there is also
     * no default adapter, an exception is thrown.
     *
     * @param  Zend_Db_Adapter_Abstract $adapter
     * @throws App_Mapper_RuntimeException When no adapter was defined
     * @throws
     */
    protected function __construct(Zend_Db_Adapter_Abstract $adapter = null)
    {
        if ($adapter === null) {
            $adapter = self::getDefaultAdapter();
        }

        if ($adapter === null) {
            throw new Freak_Model_Mapper_RuntimeException('No adapter was defined');
        }

        $this->_adapter = $adapter;

        $this->_init();
    }

    /**
     * Do some initial stuff
     *
     * @return void
     */
    protected function _init()
    {}

    public function getLastQueryTotalCount() {
    	if($this->_lastQuery===null) {
    		throw new Freak_Model_Mapper_RuntimeException('No query was executed to retrieve count of');
    	}
    	
    	$select = $this->_lastQuery;
    	$select->reset(Zend_Db_Select::LIMIT_COUNT)
               ->reset(Zend_Db_Select::LIMIT_OFFSET)
               ->reset(Zend_Db_Select::COLUMNS);
    	$select->columns(array('id' => 'COUNT(*)', 'totalCount' => new Zend_Db_Expr(1)));
    	$select->group('totalCount');
        return (int)$this->_adapter->fetchOne($select);
    }

    /**
     * Get the adapter
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getDefaultAdapter()
    {
        return self::$_defaultAdapter;
    }

    /**
     * Set the adapter
     *
     * @param  Zend_Db_Adapter_Abstract $adapter
     * @return void
     */
    public static function setDefaultAdapter(Zend_Db_Adapter_Abstract $adapter)
    {
        self::$_defaultAdapter = $adapter;
    }
}
