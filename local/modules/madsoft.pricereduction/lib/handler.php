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

    public static function beforeDelete($Id)
    {
        $handler = self::getInstance();
        $handler->doSomething();
        $handler->doSomethingElse();

        return $Id;
    }


    protected function doSomething()
    {
        //do something
    }

    protected function doSomethingElse()
    {
        //do something else
    }

    public static function doSometh()
    {
        echo "doSometh";
        //do something else
    }

    public static function onPriceUpdate($PRICE_ID, $arFields)
    {
        //echo "Событие beforePriceUpdate";

        \CModule::IncludeModule("catalog");

        $product_id = $arFields['PRODUCT_ID'];
        $new_price = $arFields['PRICE'];

        $old_price = \CPrice::GetList(
            [],
            [
                "ID" => $PRICE_ID
            ]
        )->Fetch();

        //\Bitrix\Main\Diag\Debug::writeToFile(array('arFields' => $arFields), "", "mail_send_params.log");

        // если цена снизилась
        if ($new_price < $old_price['PRICE']) {

            $hlblock_id = \Bitrix\Main\Config\Option::get("mad", "hblock_id");

            $hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();
            $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
            $hlDataClass = $hlentity->getDataClass();

            $result = $hlDataClass::getList([
                'select' => array('UF_EMAIL', 'UF_PRODUCT_ID'),
                'filter' => array('UF_PRODUCT_ID' => $product_id),
            ]);


            //$product = \CCatalogProduct::GetByID($product_id);

            $product_properties = \CCatalogProduct::getList([
                'select' => array('ID', 'ELEMENT_NAME'),
                'filter' => array('ID' => $product_id),
                'limit' => 1
            ])->fetch();

            /*echo "product:<pre>";
            print_r($product_properties);
            echo "</pre>";*/

            //  if (self::trySendEmail()) {//}


            while ($res = $result->fetch()) {

                $params = [
                    "new_price" => $new_price,
                    "email" => $res['UF_EMAIL'],
                    "product_id" => $res['UF_PRODUCT_ID'],
                    "product_name" => $product_properties['ELEMENT_NAME']
                ];

                /*echo "Отправка писем:<pre>";
                print_r($params);
                echo "</pre>";*/


                //\Bitrix\Main\Diag\Debug::writeToFile(array('mail_send_params' => $param), "", "mail_send_params.log");
                self::sendEmail($params);
            }


        }


    }


    function sendEmail($params)
    {

        $event = new \CEvent;
        $mail_event_name = 'PRICE_REDUCTION';

        $mail_send_params = [
            "PRODUCT_ID" => $params['product_id'],
            "PRODUCT_NAME" => $params['product_name'],
            "PRICE" => $params["new_price"],
            "EMAIL" => $params['email']
        ];

        // select * from b_event  order by date_insert desc
        $mail_send_id = $event->Send($mail_event_name, SITE_ID, $mail_send_params, "N");

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




//use \Bitrix\Highloadblock\HighloadBlockTable;
//use \Bitrix\Main\Config\Option;
//use \Bitrix\Main\Mail\Event as MailEvent;
//use \Bitrix\Main\Event;

//CModule::IncludeModule('highloadblock');



/*
                                              $PRICE_ID = 1;
                                              $arFields = array
                                              (
                                                  "EXTRA_ID" => "",
                                                  "PRODUCT_ID" => 42,
                                                  "CATALOG_GROUP_ID" => 1,
                                                  "PRICE" => 2002,
                                                  "CURRENCY" => "RUB",
                                                  "QUANTITY_FROM" => "",
                                                  "QUANTITY_TO" => "",
                                                  "RECALC> "",
                                                  "PRICE_SCALE" => 2002
                                              );
                      /*

                                         \MadSoft\PriceReduction::beforePriceUpdate($PRICE_ID, $arFields);*/

// Создание события
// $event = new Event("mymodule", "OnCatalogElementsImport", array('test_string'));

// Вызов события
// $event->send();

// Обработка результатов вызова
/*            if ($event->getResults()) {
                echo 'Параметры, возвращенные из обработчика';


                foreach ($event->getResults() as $eventResult) {
                    echo "<pre style=\"display:block;\">";
                    print_r($eventResult->getParameters());
                    echo "</pre>";
                }
            }*/