<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\RCP\Gateways;

use BeycanPress\CryptoPay\Integrator\Type;
use BeycanPress\CryptoPay\Integrator\Helpers;

class GatewayLite extends \RCP_Payment_Gateway
{
    /**
     * @var string
     */
    public const ID = 'cryptopay_lite';

    /**
     * @var array<string>
     */
    // @phpcs:ignore
    public $supports = [];

    /**
     * @var int
     */
    // @phpcs:ignore
    public $user_id;

    /**
     * @var object
     */
    // @phpcs:ignore
    public $payment;

    /**
     * @var string
     */
    // @phpcs:ignore
    public $discount_code;

    /**
     * @var string
     */
    // @phpcs:ignore
    public $return_url;

    /**
     * @var string
     */
    // @phpcs:ignore
    public $currency;

    /**
     * @return void
     */
    public function init(): void
    {
        $this->supports = [
            'one-time'
        ];
    }

    /**
     * @return void
     */
    // @phpcs:ignore
    public function process_signup(): void
    {
        /**
         * @var \RCP_Payments $rcp_payments_db
         */
        global $rcp_payments_db;

        // Update payment record with transaction ID.
        $rcp_payments_db->update($this->payment->id, [
            'payment_type'   => self::ID,
        ]);

        $paymentUrl = Helpers::createSPP([
            'addon' => 'rcp',
            'addonName' => 'Restrict Content Pro',
            'order' => [
                'id' => $this->payment->id,
                'amount' => $this->payment->amount,
                'currency' => $this->currency,
            ],
            'params' => [
                'returnUrl' => $this->return_url,
            ],
            'type' => Type::LITE,
        ]);

        wp_redirect($paymentUrl);
        exit;
    }
}
