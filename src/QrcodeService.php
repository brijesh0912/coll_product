<?php
namespace Drupal\coll_product;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrcodeService {

  /**
   * QR-Code generater function user chillerplan library.
   *
   * @param string $app_link_url
   *   URL to convert into QR-code.
   */
    public function renderQrcode( string $app_link_url) {
        // creates code image and outputs it directly into browser
        if(!empty($app_link_url)){

          $options = new QROptions(
            [
              'eccLevel' => QRCode::ECC_L,
              'outputType' => QRCode::OUTPUT_MARKUP_SVG,
              'version' => 5,
            ]
          );

        $qrcode = (new QRCode($options))->render($app_link_url);
        return $qrcode;
        }
    }
}
