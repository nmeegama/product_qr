<?php

namespace Drupal\product_qr\Services;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Symfony\Component\Filesystem\Filesystem;

class QRGenerator {

  /**
   *  A function to generate the QR code for a product
   * @param $nid
   * @param $purchase_link
   *
   * @return null
   */
  public function generateQrCodeImage($nid, $purchase_link) {
    $renderer = new ImageRenderer(
      new RendererStyle(400),
      new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);
    // PUBLIC FILES PATH TO DIRECTORY  for saving files.
    $dir_uri = 'public://product_qr_img';
    $product_qr_image_dir = \Drupal::service('file_system')->realpath($dir_uri);

    $file_system = new Filesystem();
    // Create Directory if dir not exists.
    if (!$file_system->exists($product_qr_image_dir)) {
      $file_system->mkdir($product_qr_image_dir);
    }
    // Image path for the Writer().
    $product_qr_image_path = $product_qr_image_dir . '/product_qr_' . $nid . '.png';
    // Image URI for Drupal.
    $product_qr_image_uri = $dir_uri . '/product_qr_' . $nid . '.png';
    try {
      $writer->writeFile($purchase_link, $product_qr_image_path);
      $image_uri = \Drupal::service('file_url_generator')->generateAbsoluteString($product_qr_image_uri);

    } catch (\Exception $e) {
      // Log the message if there is a problem
      \Drupal::logger('product_qr')->error(t('image was not created at ' . $product_qr_image_path . '.<br>'. $e->getMessage()));
      $image_uri = NULL;
    }

    return $image_uri;
  }

}