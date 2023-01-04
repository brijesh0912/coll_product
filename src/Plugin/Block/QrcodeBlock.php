<?php
/**
 * @file
 * Contains \Drupal\coll_product\Plugin\Block\QrcodeBlock.
 */

namespace Drupal\coll_product\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\NodeInterface;
use Drupal\coll_product\QrcodeService;

/**
 * Provides a 'QRcode' block.
 *
 * @Block(
 *   id = "qrcode_block",
 *   admin_label = @Translation("QR-Code block"),
 *   category = @Translation("Custom QR Code block for Product Content type")
 * )
 */
class QrcodeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * QR-code generater.
   *
   * @var \Drupal\coll_product\Service\QrcodeService
   */
  protected $qrCodeService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('qrcodeservice')
    );
  }
  /**
   * Constructs a new QRcodeBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   * @param Drupal\coll_product\Service\QrcodeService $qrCodeService
  *   Qr generator service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, QrcodeService $qrCodeService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
    $this->qrCodeService = $qrCodeService;
  }

  /**
   * {@inheritdoc}
   */

   public function build() {

     /** @var \Drupal\node\NodeInterface $node */

    $node = $this->routeMatch->getParameter('node');

    if (!($node instanceof NodeInterface)) {
      return [];
    }

    // Render the field_app_purchase_link in body using the 'default' display mode.
    $app_purchase_link = $node->get('field_app_purchase_link')->getValue()[0]['uri'];

    $generatedQRcode = $this->qrCodeService->renderQrcode($app_purchase_link);
    \Drupal::logger('coll_product')->notice("generatedQRcode-> ". $generatedQRcode);

   // Build block theme and pass the QR-code image.
   return [
      '#theme' => 'qrcode_block',
      '#app_link_url' => $generatedQRcode,
    ];

  }

  /**
   * Disable cache for this block.
   */
  public function getCacheMaxAge() {
    // Avoid caching data for QR-Code.
    return 0;
  }
}
