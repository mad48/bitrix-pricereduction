<?
/** @global CMain $APPLICATION */

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request;

use Bitrix\Main\Web\Json;

use \Bitrix\Main\Config\Option;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;

//Loc::loadMessages(__FILE__);

define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


CModule::IncludeModule('highloadblock');
//use Bitrix\Highloadblock

$request = Application::getInstance()->getContext()->getRequest();

if ($request->isAjaxRequest()) {

    //global $USER;

    $email = htmlspecialchars($request->getPost("email"));
    $product_id = (int)$request->getPost("productid");

    \Bitrix\Main\Diag\Debug::writeToFile(array('email' => $email, 'product_id' => $product_id), "", "subs.log");


    $ID = Option::get("mad", "hblock_id");

    $hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById($ID)->fetch();//информация о блоке
    $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);//класс сущности
    $hlDataClass = $hlentity->getDataClass();

    $result = $hlDataClass::getList([
        'select' => array('UF_EMAIL', 'UF_PRODUCT_ID'),
        'order' => array('UF_EMAIL' => 'ASC'),
        'limit' => '1',
        'filter' => array('UF_EMAIL' => $email, 'UF_PRODUCT_ID' => $product_id),
    ]);

    $res = $result->fetch();

    if (!empty($res)) {

        //echo "уже  подписан";

        echo Json::encode([
            'success' => true,
            'subscribe' => 'already',
            'message' => Loc::getMessage('PRICE_REDUCTION_ALREADY_SUBSCRIBE')
        ]);

    } else {

        $ar_price = 0;

        //echo "ща подпишем";
        if (\CModule::IncludeModule("catalog")) {

            $pprice = GetCatalogProductPrice($product_id, 1);

            $subscribeId = $hlDataClass::add([
                'UF_EMAIL' => $email,
                'UF_PRODUCT_ID' => $product_id,
                'UF_PRICE' => $pprice['PRICE']
            ]);
        }

        $_SESSION['PRICE_REDUCTION_EMAIL'] = $email;

        if ($subscribeId) {
            echo Json::encode([
                'success' => true,
                'subscribe' => 'now',
                'message' => Loc::getMessage('PRICE_REDUCTION_SUCCESS_SUBSCRIBE')
            ]);
        } else {
            echo Json::encode([
                'success' => false,
                'subscribe' => 'error',
                'message' => Loc::getMessage('PRICE_REDUCTION_ERROR_SUBSCRIBE')
            ]);
        }
    }

    require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_after.php');

}


?>