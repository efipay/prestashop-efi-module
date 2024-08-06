<?php



/**
 * Pix payment method
 */


class Pix
{

    /**
     * Return certificate file path
     *
     * @return string
     */
    public static function getCertPath()
    {
        return EFI_ROOT_URL . '/lib/certs/cert.pem';
    }



    /**
     * Create gerencianet data config
     *
     * @return array
     */
    public static function get_gn_api_credentials($credential)
    {
        $credential['pix_cert'] = Pix::getCertPath();
        return $credential;
    }

    /**
     * Update gerencianet webhook
     *
     * @return void
     */
    public static function updateWebhook($key)
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



        $url = Context::getContext()->link->getModuleLink('EfiPayPrestashop', 'notificationpix', array(), Configuration::get('PS_SSL_ENABLED'), null, null, false) . '?checkout=custom&';

        // Remove /pix/ porque será adicionado pela gerencianet na chamada do webhook
        $url = str_replace('/pix/', '', $url);

        // Se for localhost não faz update do weebhook
        if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
            error_log(' :: GERENCIANET :: Localhost is not a valid webhook');
        } else {
            $credential = Pix::get_gn_api_credentials($gn->get_gn_api_credentials());
            $skip_mtls = Configuration::get('GERENCIANET_VALIDAR_MTLS')  == 1 ? 'false' : 'true'; // Precisa ser string

            $hash = hash('sha256', substr(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'), strlen(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO')) - 8) . $_SERVER['SERVER_ADDR']);
            $url = $url . 'hash=' . $hash . '&';

            $gnApi = $gn->update_webhook($credential, $key, $skip_mtls, $url);

            return json_decode($gnApi);
        }
    }
}