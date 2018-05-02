<?php

class Potoky_ImageAutoImport_Block_Adminhtml_GridContainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'imageautoimport';
        $this->_controller = 'adminhtml';
        $this->_mode = 'importtoqueue';

        parent::__construct();
        $this->removeButton('add');
        $this->setTemplate('imageautoimport/flow_grid.phtml');
    }

    public function getHeaderText()
    {
        return Mage::helper('imageautoimport')->__('Import of Images Flow');
    }
}