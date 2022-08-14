<?php

namespace Drupal\product_qr\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a Product QR for product purchase Block.
 *
 * @Block(
 *   id = "product_qr_block",
 *   admin_label = @Translation("Product Purchase QR"),
 *   category = @Translation("Product QR")
 * )
 */
class ProductQRBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = 0;
    if ($node instanceof \Drupal\node\NodeInterface) {
      /** @var \Drupal\node\Entity\Node $nid */
      // You can get nid and anything else you need from the node object.
      $nid = $node->id();
      $purchase_link = $node->field_product_qr_link->uri;
    }
    // Set the block markup
    $block_markup = '<h3> There was a problem generating purchase link QR code </h3>';
    if ($nid && $purchase_link) {

      // Commenting the code block . This is an attempt to only cache the node QR code image link
//      $cid = 'product_qr:' . 'node_'.$nid;
//      $data = NULL;
//      // If the data is already cached
//      if ($cache = \Drupal::cache()->get($cid)) {
//        $data = $cache->data;
//      }
//      else {
//        /** @var \Drupal\product_qr\Services\QRGenerator $qrGenerator */
//        $qrGenerator = \Drupal::service('product_qr.qr_generator');
//        $product_qr_url = $qrGenerator->generateQrCodeImage($nid, $purchase_link);
//        $data = $product_qr_url;
//        \Drupal::cache()->set($cid, $data, Cache::PERMANENT);
//      }

      $qrGenerator = \Drupal::service('product_qr.qr_generator');
      $product_qr_url = $qrGenerator->generateQrCodeImage($nid, $purchase_link);
      $data = $product_qr_url;

      // If valid product qr url
      if ($product_qr_url) {
        $block_markup = '<h3> QR code generated </h3> <img src="' . $data . '">';
      }

    }
    return [
      '#markup' => $this->t($block_markup),
//      '#cache' => [
//        'max-age' => 0,
//      ]
    ];
  }




//  public function getCacheTags() {
//    //With this when your node change your block will rebuild
//    if ($node = \Drupal::routeMatch()->getParameter('node')) {
//      //if there is node add its cachetag
//      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
//    } else {
//      //Return default tags instead.
//      return parent::getCacheTags();
//    }
//  }
//
//  public function getCacheContexts() {
//    //if you depends on \Drupal::routeMatch()
//    //you must set context of this block with 'route' context tag.
//    //Every new route this block will rebuild
//    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
//  }

}