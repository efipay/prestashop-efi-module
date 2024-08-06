<?php

/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

use Contrib\Bundle\CoverallsV1Bundle\Config\Configurator;

require_once EFI_ROOT_URL . '/module/settings/AbstractForms.php';
require_once EFI_ROOT_URL . '/lib/payments/OpenFinance.php';


class OpenFinanceForm extends AbstractForms
{

    public function __construct()
    {
        parent::__construct('icon-qrcode');
        $this->submit = 'submitOpenFinanceForm';
        $this->values = $this->getFormValues();
        $this->form = $this->generateForm();
        $this->process = $this->verifyPostProcess();
    }

    /**
     * Generate inputs form
     *
     * @return void
     */
    public function generateForm()
    {

        $title = $this->module->l('Configuração do Pix via Open Finance', 'OpenFinanceForm');

        $fields = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Nome'),
                'prefix' => '<i class="icon-user"></i>',
                'desc' => $this->module->l('Nome do titular da conta Efí.'),
                'name' => 'GERENCIANET_NOME_OPENFINANCE',
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Documento'),
                'prefix' => '<i class="icon-book"></i>',
                'desc' => $this->module->l('Documento do titular da conta Efí.'),
                'name' => 'GERENCIANET_DOCUMENTO_OPENFINANCE',
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Agência'),
                'prefix' => '<i class="icon-gears"></i>',
                'hint' => 'Agência do titular da conta Efí.',
                'name' => 'GERENCIANET_AGENCIA_OPENFINANCE',
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Conta'),
                'prefix' => '<i class="icon-gears"></i>',
                'hint' => 'Conta do titular.',
                'name' => 'GERENCIANET_CONTA_OPENFINANCE',
            ],
            [
                'col' => 4,
                'type' => 'select',
                'label' => $this->module->l('Tipo de conta'),
                'prefix' => '<i class="icon-money"></i>',
                'name' => 'GERENCIANET_TIPO_CONTA_OPENFINANCE',
                'hint' => 'Tipo de conta do titular.',
                'options' => [
                    'query' => [
                        [
                            'id' => 'CACC',
                            'name' => 'Conta Corrente'
                        ],
                        [
                            'id' => 'SLRY',
                            'name' => 'Conta Salário'
                        ],
                        [
                            'id' => 'SVGS',
                            'name' => 'Conta Poupança'
                        ],
                        [
                            'id' => 'TRAN',
                            'name' => 'Conta de Transações'
                        ]
                    ],
                    'id' => 'id',
                    'name' => 'name'
                ]
            ]
        ];

        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {

        parent::postFormProcess();

        OpenFinance::configOpenFinance();

    }
    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        return array(
            'GERENCIANET_NOME_OPENFINANCE' => Configuration::get('GERENCIANET_NOME_OPENFINANCE'),
            'GERENCIANET_DOCUMENTO_OPENFINANCE' => Configuration::get('GERENCIANET_DOCUMENTO_OPENFINANCE'),
            'GERENCIANET_AGENCIA_OPENFINANCE' => Configuration::get('GERENCIANET_AGENCIA_OPENFINANCE'),
            'GERENCIANET_CONTA_OPENFINANCE' => Configuration::get('GERENCIANET_CONTA_OPENFINANCE'),
            'GERENCIANET_TIPO_CONTA_OPENFINANCE' => Configuration::get('GERENCIANET_TIPO_CONTA_OPENFINANCE'),
        );
    }





    public function validate()
    {
        $nome = Configuration::get('GERENCIANET_NOME_OPENFINANCE');

        // Verifica se o nome contém algum acento ou caractere especial
        if (preg_match('/[^\p{L}\p{N}\s]/u', $nome)) {
            return array('msg' => 'O campo Nome não pode conter acentos ou caracteres especiais.');
        }

        $documento = Configuration::get('GERENCIANET_DOCUMENTO_OPENFINANCE');

        if (!preg_match('/^[0-9]{11}$/', $documento) && !preg_match('/^[0-9]{14}$/', $documento)) {
            return array('msg' => 'Documento invalido.');
        }

        $conta = Configuration::get('GERENCIANET_CONTA_OPENFINANCE');

        if (strpos($conta, '-') !== false) {
            return array('msg' => 'Digite o número conta sem o caractere "-".');
        }


    }
}
