<?php
class Silk_CustomerDownload_Block_Adminhtml_CustomerDownload_File extends Mage_Adminhtml_Block_Customer_Form_Element_File {
	/**
	 * Return File preview link HTML
	 *
	 * @return string
	 */
	protected function _getPreviewHtml() {
		$html     = '';
		$fileName = end(explode('/', $this->getValue()));
		if ($this->getValue() && !is_array($this->getValue())) {
			$image = array(
				'alt'   => Mage::helper('adminhtml')->__('Download'),
				'title' => Mage::helper('adminhtml')->__('Download'),
				'src'   => Mage::getDesign()->getSkinUrl('images/fam_bullet_disk.gif'),
				'class' => 'v-middle'
			);
			$url = $this->_getPreviewUrl();
			$html .= '<span>';
			$html .= '<a href="'.$url.'">'.$this->_drawElementHtml('img', $image).'</a> ';
			$html .= '<a href="'.$url.'">'.Mage::helper('adminhtml')->__($fileName).'</a>';
			$html .= '</span>';
		}
		return $html;
	}
}