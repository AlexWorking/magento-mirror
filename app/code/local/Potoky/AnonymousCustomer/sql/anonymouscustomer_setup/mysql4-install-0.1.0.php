<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('anonymouscustomer/anonymous');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity Id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
    ), 'Website Id')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Email')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
    ), 'Store Id')
    ->addColumn('created_at',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'false',
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Created At');
$installer->getConnection()->createTable($table);
$installer->endSetup();
