<?php

/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    gerencianet
 *  @copyright Copyright (c) Gerencianet [http://www.gerencianet.com.br]
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

include_once dirname(__FILE__) . '/../../lib/dbEfiPayPrestaShop.php';
include_once dirname(__FILE__) . '/../../lib/EfiPayIntegration.php';

class EfiPayPrestashopNotificationopenfinanceModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        if (isset($_GET['hmac']) == hash('sha256', substr(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'), strlen(Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO')) - 8) . $_SERVER['SERVER_ADDR'])){
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, TRUE);
            $gerencianet = $this->module;
            $gerencianet->validateNotificationOpenFinance($input['status'], $input['identificadorPagamento']);
        } else {
            error_log(' :: GERENCIANET :: Invalid webhook');
        }


    }
}
