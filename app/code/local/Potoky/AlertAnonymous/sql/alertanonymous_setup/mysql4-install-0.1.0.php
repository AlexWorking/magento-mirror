<?php

$installer = $this;
$installer->startSetup();
$tabName = $installer->getTable('alertanonymous/statistics');
$installer->getConnection()->dropTable($tabName);
$table = $installer->getConnection()
    ->newTable($tabName)
    ->addColumn('alert_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned'  => true,
        'nullable' => false,
        'primary' => true
    ), 'Alert ID')
    ->addColumn('alert_type',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => 'false',
    ), 'Stock or Price')
    ->addColumn('email',Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => 'false',
    ), 'Email of the anonymous customer' )
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false
    ), 'Product ID the customer waits an email')
    ->addColumn('add_date',  Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'false',
    ), 'Time when the first alert was added')
    ->addColumn('last_send_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => 'true',
        'default' => null
    ), 'Time when the last email was send on this particular alert')
    ->addColumn('send_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default'  => 0
    ), 'Shows the numpber of times the email was sent on this particular alert')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, array(
        'nullable' => false,
        'default'  => false
    ), 'Shows whether there has been sent an email on the last subscription.');
$installer->getConnection()->createTable($table);
$installer->endSetup();
