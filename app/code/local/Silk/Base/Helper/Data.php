<?php
class Silk_Base_Helper_Data extends Mage_Core_Helper_Abstract
{
   public function addArtistBreadcrumbs($product,$crumbs){
      $optionId = $product->getArtist();
      $option_label = $product->getAttributeText('artist');
      $urlHelper = Mage::helper('amshopby/url');
      $url = $urlHelper->getOptionUrl('artist', $optionId);
      $last = array_slice($crumbs, -1,1);
      array_pop($crumbs);
      $artist['artist'] = array(
        'label' => $option_label,
        'title' => $option_label,
        'link' => $url,
        'first' => null,
        'last' => null,
        'readonly' => null,
        );
      return $crumbs+$artist+$last;
     }
}

 ?>
