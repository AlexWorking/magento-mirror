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
    ->addColumn('position_in_grid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Position of the banner in the Grid')
    ->addColumn('position_in_list', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Position of the banner in the List')
    ->addColumn('active_for_grid', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
        'nullable'  => true
    ), 'Is current widget instance active for Grid')
    ->addColumn('active_for_list', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
        'nullable'  => true
    ), 'Is current widget instance active for List')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_BOOLEAN,null, array(
        'nullable' => false
    ), 'Is current widget instance active or not')
    ->addIndex($installer->getIdxName('itembanner/bannerinfo', array('instance_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('instance_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('itembanner/bannerinfo', array('position_in_grid', 'active_for_grid'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('position_in_grid', 'active_for_grid'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('itembanner/bannerinfo', array('position_in_list', 'active_for_list'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('position_in_list', 'active_for_list'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('itembanner/bannerinfo', 'instance_id', 'widget/widget_instance', 'instance_id'),
        'instance_id', $installer->getTable('widget/widget_instance'), 'instance_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE);
$installer->getConnection()->createTable($table);
$installer->endSetup();