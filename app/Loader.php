<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\RCP;

use BeycanPress\CryptoPay\Integrator\Hook;
use BeycanPress\CryptoPay\Integrator\Helpers;
use BeycanPress\CryptoPay\RCP\Gateways\GatewayPro;
use BeycanPress\CryptoPay\RCP\Gateways\GatewayLite;

class Loader
{
    /**
     * Loader constructor.
     */
    public function __construct()
    {
        Helpers::registerIntegration('rcp');

        // add transaction page
        Helpers::createTransactionPage(
            esc_html__('Restrict Content Pro Transactions', 'rcp-cryptopay'),
            'rcp',
            10,
            [
                'orderId' => function ($tx) {
                    return Helpers::run('view', 'components/link', [
                        'url' => sprintf(admin_url('admin.php?page=rcp-payments&payment_id=%d&view=edit-payment'), $tx->orderId), // @phpcs:ignore
                        /* translators: %d: transaction order id */
                        'text' => sprintf(esc_html__('View payment #%d', 'gf-cryptopay'), $tx->orderId)
                    ]);
                }
            ]
        );

        Hook::addAction('payment_finished_rcp', [$this, 'paymentFinished']);
        Hook::addFilter('payment_redirect_urls_rcp', [$this, 'paymentRedirectUrls']);

        add_action('init', [Helpers::class, 'listenSPP']);
        add_filter('rcp_payment_gateways', [$this, 'registerGateways']);
        add_filter('rcp_merchant_transaction_id_link', [$this, 'transactionIdLink'], 10, 2);
    }

    /**
     * @param object $data
     * @return void
     */
    public function paymentFinished(object $data): void
    {
        $tx = $data->getModel()->findOneBy([
            'hash' => $data->getHash()
        ]);
        global $rcp_payments_db;

        update_post_meta($tx->getId(), 'rcp_transaction_id', $data->getHash());

        if ($data->getStatus()) {
            $rcp_payments_db->update($data->getOrder()->getId(), [
                'status'         => 'complete',
                'transaction_id' => $tx->getId(),
            ]);
            rcp_complete_registration($data->getOrder()->getId());
        } else {
            $rcp_payments_db->update($data->getOrder()->getId(), [
                'status'         => 'failed',
                'transaction_id' => $tx->getId(),
            ]);
        }
    }

    /**
     * @param object $data
     * @return array<string>
     */
    public function paymentRedirectUrls(object $data): array
    {
        return [
            'success' => $data->getParams()->get('returnUrl'),
            'failed' => add_query_arg([
                'rcp-action' => 'download_invoice',
                'payment_id' => $data->getOrder()->getId()
            ], home_url())
        ];
    }

    /**
     * @param string $transactionId
     * @param object $payment
     * @return string
     */
    public function transactionIdLink(string $transactionId, object $payment): string
    {
        if (!in_array($payment->gateway, [GatewayLite::ID, GatewayPro::ID], true)) {
            return $transactionId;
        }

        if (empty($transactionId)) {
            return esc_html__('Waiting for payment', 'rcp-cryptopay');
        }

        $txHash = get_post_meta($transactionId, 'rcp_transaction_id', true);

        if (empty($txHash)) {
            return esc_html__('Transaction not found', 'rcp-cryptopay');
        }

        return Helpers::run('view', 'components/link', [
            'url' => sprintf(admin_url('admin.php?page=%s_rcp_transactions&s=%s'), $payment->gateway, $txHash),
            'text' => esc_html__('View transaction', 'rcp-cryptopay')
        ]);
    }

    /**
     * @param array<string> $gateways
     * @return array<string>
     */
    public function registerGateways(array $gateways): array
    {
        if (Helpers::exists()) {
            $gateways[GatewayPro::ID] = [
                'label'        => __('CryptoPay', 'rcp-cryptopay'),
                'admin_label'  => __('CryptoPay', 'rcp-cryptopay'),
                'class'        => GatewayPro::class
            ];
        }

        if (Helpers::liteExists()) {
            $gateways[GatewayLite::ID] = [
                'label'        => __('CryptoPay Lite', 'rcp-cryptopay'),
                'admin_label'  => __('CryptoPay Lite', 'rcp-cryptopay'),
                'class'        => GatewayLite::class
            ];
        }
        return $gateways;
    }
}
