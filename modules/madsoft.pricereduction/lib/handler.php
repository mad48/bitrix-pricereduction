<?php


namespace MadSoft\PriceReduction\Lib;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

//use Bitrix\Main\Mail\Event;
use Bitrix\Main\Event;


Loc::loadMessages(__FILE__);

class Handler
{

    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Обработчик изменения цены продукта
     * 
     * @param $PRICE_ID
     * @param $arFields
     */
    public static function onPriceUpdate($PRICE_ID, $arFields)
    {
        \CModule::IncludeModule("catalog");

        $new_price = $arFields['PRICE'];

        $product_id = $arFields['PRODUCT_ID'];

        $product_properties = \CCatalogProduct::getList([
            'select' => array('ID', 'ELEMENT_NAME'),
            'filter' => array('ID' => $product_id),
            'limit' => 1
        ])->fetch();

        /*echo "product:<pre>";
        print_r($product_properties);
        echo "</pre>";*/

        $hlblock_id = \Bitrix\Main\Config\Option::get("mad", "hblock_id");

        $hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
        $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
        $hlDataClass = $hlentity->getDataClass();

        $subscribers = $hlDataClass::getList([
            'select' => array('UF_EMAIL', 'UF_PRODUCT_ID', 'UF_PRICE'),
            'filter' => array('UF_PRODUCT_ID' => $product_id),
        ]);


        while ($subscriber = $subscribers->fetch()) {

            // если цена снизилась
            if ($new_price < $subscriber['UF_PRICE']) {

                $params = [
                    "new_price" => $new_price,
                    "email" => $subscriber['UF_EMAIL'],
                    "product_id" => $subscriber['UF_PRODUCT_ID'],
                    "product_name" => $product_properties['ELEMENT_NAME']
                ];

                /*echo "Отправка писем:<pre>";
                print_r($params);
                echo "</pre>";*/

                //\Bitrix\Main\Diag\Debug::writeToFile(array('mail_send_params' => $params), "", "mail_send_params.log");
                self::sendEmail($params);
            }
        }


    }


    function sendEmail($params)
    {

        //  if (self::trySendEmail()) {//}
        
        $event = new \CEvent;
        $mail_event_name = 'PRICE_REDUCTION';

        $mail_send_params = [
            "PRODUCT_ID" => $params['product_id'],
            "PRODUCT_NAME" => $params['product_name'],
            "PRICE" => $params["new_price"],
            "EMAIL" => $params['email']
        ];

        //\Bitrix\Main\Diag\Debug::writeToFile(array('sendEmail' => $mail_send_params, "SITE_ID" => SITE_ID), "", "sendEmail.log");
        // select * from b_event  order by date_insert desc

        $mail_send_id = $event->Send($mail_event_name, 's1', $mail_send_params, "N");


        /*echo "<pre>";
        print_r($mail_send_id);
        echo "</pre>";*/
    }


    public static function trySendEmail()
    {
        if (mail("email@example.ru", "TEST SUBJECT", "TEST BODY")) {
            echo "Почтовая система работает!";
            return true;
        } else {
            echo "Неудача, почтовая система не работает, попробуйте еще!";
            return false;
        }
    }
}
