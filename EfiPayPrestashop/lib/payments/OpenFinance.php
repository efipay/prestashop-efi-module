<?php

require_once EFI_ROOT_URL . '/lib/payments/Pix.php';

/**
 * Pix payment method
 */


class OpenFinance
{


    public static function configOpenFinance()
    {

        $gn = new
            EfiPayIntegration(
                Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
                (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
                Configuration::get('GERENCIANET_ID_CONTA')
            );



        $url = Context::getContext()->link->getModuleLink('EfiPayPrestashop', 'notificationopenfinance', array(), Configuration::get('PS_SSL_ENABLED'), null, null, false) . '?checkout=custom&';


        // Se for localhost não faz update do weebhook
        if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
            error_log(' :: GERENCIANET :: Localhost is not a valid webhook');
        } else {
            
            $credential = Pix::get_gn_api_credentials($gn->get_gn_api_credentials());
            $body = [
                'redirectURL' => Context::getContext()->link->getModuleLink('EfiPayPrestashop', 'afterpaymentopenfinance', array(), true),
                'webhookURL' => $url,
                'webhookSecurity' => [
                    'type' => 'hmac',
                    'hash' => hash('sha256', substr(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'), strlen(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO')) - 8) . $_SERVER['SERVER_ADDR'])
                ],
                'processPayment' => "async"
            ];

            $gnApi = $gn->config_app($credential, $body);

            return json_decode($gnApi);
        }
    }
}