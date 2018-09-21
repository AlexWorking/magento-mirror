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
    ->addColumn('page_groups', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Current Widget Instance Page Groups to help save instance model without controller')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
        'default' => false
    ), 'Is the current widget instance active or not')
    ->addIndex($installer->getIdxName('itembanner/bannerinfo', array('instance_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('instance_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('itembanner/bannerinfo', 'instance_id', 'widget/widget_instance', 'instance_id'),
        'instance_id', $installer->getTable('widget/widget_instance'), 'instance_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);
$installer->endSetup();