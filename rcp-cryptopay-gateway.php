<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: Restrict Content Pro - CryptoPay Gateway
 * Version:     1.0.0
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for Restrict Content Pro.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rcp-cryptopay
 * Tags: Cryptopay, Cryptocurrency, WooCommerce, WordPress, MetaMask, Trust, Binance, Wallet, Ethereum, Bitcoin, Binance smart chain, Payment, Plugin, Gateway, Moralis, Converter, API, coin market cap, CMC
 * Requires at least: 5.0
 * Tested up to: 6.4.3
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('CONSTANT_TAG_CRYPTOPAY_FILE', __FILE__);
define('CONSTANT_TAG_CRYPTOPAY_VERSION', '1.0.0');
define('CONSTANT_TAG_CRYPTOPAY_KEY', basename(__DIR__));
define('CONSTANT_TAG_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('CONSTANT_TAG_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('CONSTANT_TAG_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\Integrator\Helpers;

Helpers::registerModel(BeycanPress\CryptoPay\RCP\Models\TransactionsPro::class);
Helpers::registerLiteModel(BeycanPress\CryptoPay\RCP\Models\TransactionsLite::class);

load_plugin_textdomain('rcp-cryptopay', false, basename(__DIR__) . '/languages');

if (!defined('RCP_PLUGIN_FILE')) {
    add_action('admin_notices', function (): void {
        ?>
            <div class="notice notice-error">
                <p><?php echo sprintf(esc_html__('Restrict Content Pro - CryptoPay Gateway: This plugin requires Restrict Content Pro to work. You can download Restrict Content Pro by %s.', 'rcp-cryptopay'), '<a href="https://wordpress.org/plugins/restrict-content/" target="_blank">' . esc_html__('clicking here', 'rcp-cryptopay') . '</a>'); ?></p>
            </div>
        <?php
    });
} elseif (Helpers::bothExists()) {
    new BeycanPress\CryptoPay\RCP\Loader();
} else {
    add_action('admin_notices', function (): void {
        ?>
            <div class="notice notice-error">
                <p><?php echo sprintf(esc_html__('Restrict Content Pro - CryptoPay Gateway: This plugin is an extra feature plugin so it cannot do anything on its own. It needs CryptoPay to work. You can buy CryptoPay by %s.', 'rcp-cryptopay'), '<a href="https://beycanpress.com/product/cryptopay-all-in-one-cryptocurrency-payments-for-wordpress/?utm_source=wp_org_addons&utm_medium=rcp" target="_blank">' . esc_html__('clicking here', 'rcp-cryptopay') . '</a>'); ?></p>
            </div>
        <?php
    });
}
