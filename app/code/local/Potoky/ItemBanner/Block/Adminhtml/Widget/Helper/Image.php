<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        if ($parent = $this->getValue()) {
            $parent = 'itembanner' . '/' . $parent;
        }

        return $parent;
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $parent = parent::getElementHtml();
        $parent .= '<script>var thumbNailId = ' . $this->getHtmlId() . '_image </script>';

        return $parent;
    }
}