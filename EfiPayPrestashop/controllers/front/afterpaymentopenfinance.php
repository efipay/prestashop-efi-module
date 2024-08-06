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
include_once dirname(__FILE__) . '/../../lib/vendor/autoload.php';

class EfiPayPrestashopAfterpaymentopenfinanceModuleFrontController extends ModuleFrontController
{
public function initContent()
{

    if (!isset($_GET['identificadorPagamento'])) {
        echo $this->renderErrorPage();
        exit;
    }

    if (isset($_GET['erro'])){

        $erro = $_GET['erro'];
        $nome = $_GET['nome'];
        $mensagem = $_GET['mensagem'];

        echo $this->renderErrorPage($erro, $nome, $mensagem);
        exit;
    }

    $identificadorPagamento = $_GET['identificadorPagamento'];
    $currentState = dbEfiPayPrestaShop::getCurrentStateByIdCharge($identificadorPagamento);

    switch ($currentState) {
        case 1:
            echo $this->renderProcessingPage($identificadorPagamento);
            break;
        case 2:
            echo $this->renderSuccessPage($identificadorPagamento);
            break;
        case 8:
            echo $this->renderFailPage($identificadorPagamento);
            break;
        default:
            echo $this->renderErrorPage();
            break;
    }
    exit;
}

private function renderErrorPage(?string $erro = null, ?string $nome = null, ?string $mensagem = null) {
    $errorMessage = "Identificador de pagamento ou status inválido. Entre em contato com o Administrador da Loja";
    if ($erro && $nome && $mensagem) {
        $errorMessage = "{$erro} - {$nome}: {$mensagem}";
    }
    return "
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            padding: 20px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0px 0px 5px 5px rgba(47,47,47,0.2);
            text-align: center;
        }
        .container h1, .container p {
            margin-bottom: 20px;
        }
        h1 {
            color: #F37021;
            font-size: 5rem;
        }
        p {
            color: #666;
            font-size: 2rem;
        }
        button {
            background-color: #F37021;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 10px 20px;
            margin: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #F37021;
            opacity: 0.8;
        }
        @media (min-width: 768px) {
            container {
                width: 55%;
            }
            p {
                font-size: 1.5rem;
            }
            h1 {
                font-size: 3rem;
            }
            button {
                font-size: 1rem;
            }
        }
    </style>
    <div class='container' id='container'>
        <h1>Erro</h1>
        <p>{$errorMessage}</p>
        <button onclick='openHistory()'>Historico de pedidos</button>
    </div>
    <script>
        function openHistory() {
            window.open('{$this->context->link->getPageLink('history')}', '_self');
        }
    </script>";
}

private function renderProcessingPage($identificadorPagamento) {
    return "
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            padding: 20px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .container h1, .container p {
            margin-bottom: 20px;
        }
        h1 {
            color: #F37021;
            font-size: 5rem;
        }
        p {
            color: #666;
            font-size: 2rem;
        }
        @media (min-width: 768px) {
            container {
                width: 55%;
            }
            p {
                font-size: 1.5rem;
            }
            h1 {
                font-size: 3rem;
            }
        }
    </style>
    <div class='container' id='container'>
        <h1>Aguardando Processamento</h1>
        <p>O pagamento com o identificador {$identificadorPagamento} está sendo processado. Por favor, aguarde...</p>
    </div>
    <script>
        var intervalId = setInterval(function() {
            fetch('?fc=module&module=EfiPayPrestashop&controller=afterpaymentopenfinance&identificadorPagamento={$identificadorPagamento}')
                .then(response => response.text())
                .then(data => {
                    if (data.includes('<h1>Pagamento Concluído') || data.includes('<h1>Erro no Pagamento')) {
                        location.reload();
                    }
                });
        }, 3000);

        setTimeout(function() {
            clearInterval(intervalId);
        }, 180000); // 3 minutos em milissegundos
    </script>";
}

private function renderSuccessPage($identificadorPagamento) {
    return "
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            padding: 20px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .container h1, .container p {
            margin-bottom: 20px;
        }
        h1 {
            color: #F37021;
            font-size: 5rem;
        }
        p {
            color: #666;
            font-size: 2rem;
        }
        button {
            background-color: #F37021;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 10px 20px;
            margin: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #F37021;
            opacity: 0.8;
        }
        @media (min-width: 768px) {
            container {
                width: 55%;
            }
            p {
                font-size: 1.5rem;
            }
            h1 {
                font-size: 3rem;
            }
            button {
                font-size: 1rem;
            }
        }
    </style>
    <div class='container' id='container'>
        <h1>Pagamento Concluído</h1>
        <p>O pagamento com o identificador {$identificadorPagamento} foi concluído.</p>
        <p>O status da cobrança será atualizado em alguns instantes.</p>
        <button onclick='openHistory()'>Historico de pedidos</button>
    </div>
    <script>
        function openHistory() {
            window.open('{$this->context->link->getPageLink('history')}', '_self');
        }
    </script>";
}

private function renderFailPage($identificadorPagamento) {
    return "
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 80%;
            padding: 20px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .container h1, .container p {
            margin-bottom: 20px;
        }
        h1 {
            color: #F37021;
            font-size: 5rem;
        }
        p {
            color: #666;
            font-size: 2rem;
        }
        button {
            background-color: #F37021;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 10px 20px;
            margin: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #F37021;
            opacity: 0.8;
        }
        @media (min-width: 768px) {
            container {
                width: 55%;
            }
            p {
                font-size: 1.5rem;
            }
            h1 {
                font-size: 3rem;
            }
            button {
                font-size: 1rem;
            }
        }
    </style>
    <div class='container' id='container'>
        <h1>Erro no Pagamento</h1>
        <p>O pagamento com o identificador {$identificadorPagamento} teve um erro.</p>
        <p>O status da cobrança será atualizado em alguns instantes.</p>
        <button onclick='openHistory()'>Historico de pedidos</button>
    </div>
    <script>
        function openHistory() {
            window.open('{$this->context->link->getPageLink('history')}', '_self');
        }

    </script>";
}
}

