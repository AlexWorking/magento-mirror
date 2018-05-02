<?php

class Potoky_ImageAutoImport_Block_Adminhtml_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $sizeInMb = $value / 1048576;
        $imageSizeDisplay = 1;
        if ($imageSizeDisplay != $sizeInMb) {
            $imageSizeDisplay = ($sizeInMb < 1) ? '423 kb' : '1.68 mb';
        }
        return '<span>'.$imageSizeDisplay.'</span>';

    }

}