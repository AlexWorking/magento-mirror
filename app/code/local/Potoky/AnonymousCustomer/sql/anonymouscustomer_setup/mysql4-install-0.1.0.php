<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('anonymouscustomer/anonymous');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('anonymous_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Anonymous Id')
    ->addColumn('reserved_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Reserved Id for a customer with these website_id and email if such one will ever going to be created')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Website Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Email')
    ->addColumn('created_at',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'false',
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Created At')
    ->addIndex($installer->getIdxName('customer/entity', array('reserved_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('reserved_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('customer/entity', array('email', 'website_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('email', 'website_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));
$installer->getConnection()->createTable($table);
$installer->endSetup();
