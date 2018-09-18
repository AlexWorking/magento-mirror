<?php

$installer = $this;

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();
$tabName = $installer->getTable('itembanner/bannerinfo');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('bannerinfo_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned'  => true,
        'nullable' => false,
        'primary' => true
    ), 'Row ID')
    ->addColumn('instance_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Itembanner widget instance ID')
    ->addColumn('clicks_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
    ), 'The number the banner link has been clicked')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
        'default' => true
    ), 'Is the current widget instance active or not');
$installer->getConnection()->createTable($table);
$installer->endSetup();