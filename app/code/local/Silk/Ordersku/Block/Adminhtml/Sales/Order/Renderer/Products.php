<?php
class Silk_Ordersku_Block_Adminhtml_Sales_Order_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
         $products = explode("\n", $this->htmlEscape($row->getData($this->getColumn()->getIndex())));
         return implode('<br/>', $products);
    }
}
?>
