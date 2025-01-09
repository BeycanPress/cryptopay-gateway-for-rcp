<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: CryptoPay Gateway for Restrict Content Pro
 * Version:     1.0.2
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for Restrict Content Pro.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: cryptopay-gateway-for-rcp
 * Tags: Bitcoin, Ethereum, Crypto, Payment, Restrict Content Pro
 * Requires at least: 5.0
 * Tested up to: 6.7.1
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('RCP_CRYPTOPAY_FILE', __FILE__);
define('RCP_CRYPTOPAY_VERSION', '1.0.2');
define('RCP_CRYPTOPAY_KEY', basename(__DIR__));
define('RCP_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('RCP_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('RCP_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\Integrator\Helpers;

/**
 * @return void
 */
function rcpCryptoPayRegisterModels(): void
{
    Helpers::registerModel(BeycanPress\CryptoPay\RCP\Models\TransactionsPro::class);
    Helpers::registerLiteModel(BeycanPress\CryptoPay\RCP\Models\TransactionsLite::class);
}

rcpCryptoPayRegisterModels();

add_action('plugins_loaded', function (): void {
    rcpCryptoPayRegisterModels();

    if (!defined('RCP_PLUGIN_FILE')) {
        Helpers::requirePluginMessage('Restrict Content Pro', admin_url('plugin-install.php?s=rcp&tab=search&type=term'));
    } elseif (Helpers::bothExists()) {
        new BeycanPress\CryptoPay\RCP\Loader();
    } else {
        Helpers::requireCryptoPayMessage('Restrict Content Pro');
    }
});
