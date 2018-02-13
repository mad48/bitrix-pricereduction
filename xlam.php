<?php


/*    public function executeComponent()
    {
        if ($this->startResultCache()) {
            $this->arResult["Y5"] = $this->sqr($this->arParams["X"]);
        }

        $this->includeComponentTemplate();

        return $this->arResult["Y"];
    }*/


function afterPriceUpdate(&$id)
{
    Bitrix\Main\Diag\Debug::writeToFile(array('ID' => $id, 'ID2' => $id), "", "afterPriceUpdate.log");
}



//Создание почтового события.
 function sendMailEvent()
{
    $event = new CEvent;
    $mail_event_name = 'PRICE_REDUCTION';

    $mail_send_params = [
        "USER_ID" => 1,
        "LOGIN" => 'myLogin',
        "NAME" => 'Rustam',
        "EMAIL" => 'htmakers@gmail.com'
    ];

    $mail_send_id = $event->Send($mail_event_name, SITE_ID, $mail_send_params, "N");

    /*    select * from b_event
   order by date_insert desc*/


    echo "<pre>";
    print_r($mail_send_id);
    echo "</pre>";


    /*        $arEventFields = array(
                'EMAIL_TO' => COption::GetOptionString("main", "email_from"), // email админа в настройках главного модуля
            );
            CEvent::Send("USER_REGISTER", SITE_ID, $arEventFields);
        }*/

    /*        $arEventFields = array(
                'EMAIL_TO' => COption::GetOptionString("main", "email_from"), // email админа в настройках главного модуля
                "USER_ID" => $arResult['VALUES']['USER_ID'] // это передаем в шаблон
            );
            CEvent::Send("USER_REGISTER", SITE_ID, $arEventFields, N, 42);*/

}

//namespace PriceReduction\Lib;

class TestEcho
{
    public function test()
    {
        echo 'test';
    }

    public static function testTwo()
    {
        echo 'test two';
    }
}






/*
$eventManager->AddEventHandler("main", "OnPageStart", "loadLocalLib", 1);

function loadLocalLib()
{
    Loader::includeModule('local.lib');
}*/


//$eventManager->AddEventHandler("mymodule", "OnCatalogElementsImport", array('Local\Lib\Handlers\Price', 'afterPriceUpdateTest'));
/*use \Local\Lib\Handlers\Price;
\Local\Lib\Handlers\Price::doSometh();*/

/*if (\Bitrix\Main\Loader::includeModule('local.lib')) {
    echo "kkkkk";
    \Local\Lib\Handlers\Price::doSometh();

}*/

/*Bitrix\Main\Loader::registerAutoLoadClasses(null, array(
    '\PriceReduction\Lib\TestEcho' => '/local/modules/pricereduction/lib/testecho.php',
));*/


/*\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'mymodule',
    'OnCatalogElementsImport',
    array('PriceReduction\Lib\TestEcho', 'testTwo')
);*/


/*
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'mymodule',
    'OnCatalogElementsImport',
    array('Local\Lib\Handlers\Price', 'myHandler')
);*/



//$ID = $result->getId();
//$entity_data_class::Delete($ID);
//$result = $entity_data_class::update($ID, $data);

/*
// Получить ИД Торгового предложения
$IBLOCK_ID = 2;
$ID = 14; // ИД элемента инфоблока
$arInfo = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
if (is_array($arInfo)) {
$rsOffers = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arInfo['IBLOCK_ID'], 'PROPERTY_' . $arInfo['SKU_PROPERTY_ID'] => $ID));
while ($arOffer = $rsOffers->GetNext()) {
// $arOffer['ID'] — ИД торгового предлоежния
}
}*/

//echo $this->GetFolder();




/*

Обновление минимальной и максимальной цены товара Битрикс
29 февраля 2016, 13:36

function OnPriceUpdateHandler($ID, $arFields) {

    CModule::IncludeModule('iblock');
    CModule::IncludeModule('catalog');

    $arItem = CCatalogSku::GetProductInfo($arFields['PRODUCT_ID']);

    if (is_array($arItem))
    {

        $arOffers = getProductOffersList($arItem['ID']);
        $arOffersIds = array_keys($arOffers[$arItem['ID']]);

        $minimum = CPrice::GetList(array(), array(
            'CATALOG_GROUP_ID' => '1',
            'PRODUCT_ID' => $arOffersIds
        ), array('MIN' => 'PRICE'))->Fetch();
        $maximum = CPrice::GetList(array(), array(
            'CATALOG_GROUP_ID' => '1',
            'PRODUCT_ID' => $arOffersIds
        ), array('MAX' => 'PRICE'))->Fetch();

        CIBlockElement::SetPropertyValuesEx($arItem['ID'], false, array('MINIMUM_PRICE' => $minimum));
        CIBlockElement::SetPropertyValuesEx($arItem['ID'], false, array('MAXIMUM_PRICE' => $maximum));
    }
}*/






/*
public
static function afterPriceUpdate($id)
{
    echo "Событие afterPriceUpdate";
    echo "<pre>";
    print_r($id->getParameters());
    echo "</pre>";
    \Bitrix\Main\Diag\Debug::writeToFile(array('ID' => $id), "", "afterPriceUpdate.log");

    //$application = \Bitrix\Main\Application::getInstance();


            \Bitrix\Main\Loader::includeModule("catalog");
            \Bitrix\Main\Loader::includeModule("iblock");
            \Bitrix\Main\Loader::includeModule("sale");
            \Bitrix\Main\Loader::includeModule("catalog");


    // CModule::IncludeModule("catalog");

    $all_const = get_defined_constants();

    $new_price_item = \CPrice::GetByID($id);
    $new_price = 300;//$new_price_item['PRICE'];
    $old_price = 500;//$all_const['OLD_PRICE_' . $id];
    $product_id = 102;//$new_price['PRODUCT_ID'];

    if ($old_price > $new_price) { // цена изменилась
        //CModule::IncludeModule("iblock");//Bitrix\Iblock\ElementTable::getList

        $hlblock_id = \Bitrix\Main\Config\Option::get("mad", "hblock_id");

        $hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById($hlblock_id)->fetch();//информация о блоке
        $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);//класс сущности
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

        echo "product:<pre>";
        print_r($product_properties);
        echo "</pre>";

        //  if (self::trySendEmail()) {//}

        //    select * from b_event  order by date_insert desc

        while ($res = $result->fetch()) {

            return;
            $param = [
                "new_price" => $new_price,
                "email" => $res['UF_EMAIL'],
                "product_id" => $res['UF_PRODUCT_ID'],
                "product_name" => $product_properties['ELEMENT_NAME']
            ];

            echo "Отправка писем:<pre>";
            print_r($param);
            echo "</pre>";

            self::sendEmail($param);
        }

    }
*/







































/*
        return;
        $all_const = get_defined_constants();

        $new_price = \CPrice::GetByID($id);
        $price = $all_const['OLD_PRICE_' . $id];

        if ($price != $new_price['PRICE']) { // цена изменилась
            CModule::IncludeModule("iblock");//Bitrix\Iblock\ElementTable::getList

            $rsEl = CIBlockElement::GetByID($new_price['PRODUCT_ID']);

            $item = $rsEl->Fetch();
            $rs = CUser::GetList($by = "", $order = "", array('UF_PRICE_ALERT' => $item['ID']), array('SELECT' => array("UF_PRICE_ALERT")));          // Перебираем всех найденных пользователей

            while ($customer = $rs->GetNext()) {
                if (in_array($item['ID'], $customer['UF_PRICE_ALERT']) AND $price > $new_price['PRICE']) {
                    CEvent::Send("PRODUCT_PRICE_CHANGE", $item['LID'], array('EMAIL' => $customer['EMAIL'], 'USER_NAME' => $customer['NAME'], 'ITEM_NAME' => $item['NAME'], 'ITEM_LINK' => $item['DETAIL_PAGE_URL'],
                        'ITEM_NEW_PRICE' => $new_price['PRICE'],
                        'ITEM_OLD_PRICE' => $price,
                    ), "N");
                }
            }

        }*/

//\Bitrix\Main\Diag\Debug::writeToFile(

//file_put_contents($_SERVER['DOCUMENT_ROOT']."/../log.txt", var_export($arResult, true));

//        $handl = fopen("777.txt", "w+");
//        fwrite($handl, $customer['EMAIL']);
//        fclose($handl);


/*        $arEventFields = array(
            'USER_TO' => 'email@site.ru',
            'MESSAGE' => 'Mail text',
            'SUBJECT' => 'Mail subject'
        );*/


/*    select * from b_event
order by date_insert desc*/

/*        CEvent::Send('PRICE_REDUCTION', SITE_ID, $arEventFields, 'N');


        Event::send(array(
            "EVENT_NAME" => "PRICE_REDUCTION",
            "LID" => SITE_ID,
            "C_FIELDS" => array(
                'USER_TO' => 'email@site.ru',
                'MESSAGE' => 'D7',
                'SUBJECT' => 'D7'
            ),
        ));*/


//AddEventHandler("main", "OnBeforeUserLogin", Array("MyClass", "BeforeLogin"));

/*class MyClass
{
    function BeforeLogin(&$arFields)
    {

        if (strtolower($arFields["LOGIN"]) == "admin") {
            global $APPLICATION;
            $handl = fopen("777.txt", "w+");
            fwrite($handl, "kk");
            fclose($handl);


            /*        use Bitrix\Main\Mail\Event;
        Event::send(array(
            "EVENT_NAME" => "NEW_USER",
            "LID" => "s1",
            "C_FIELDS" => array(
                "EMAIL" => "info@interv44olga.ru",
                "USER_ID" => 42
            ),
        ));*/


/*           CModule::IncludeModule("iblock"); //подключаем модуль инфоблоков

           $el = new CIBlockElement;

           global $USER;

           $PROP = array();
           $PROP['PRICE_REDUCTION_USER_ID'] = $USER->GetID();
           $PROP['PRICE_REDUCTION_EMAIL'] = "aa@aaa.ru";
           $PROP['PRICE_REDUCTION_SKU'] = 38;

           $arLoadProductArray = Array(
               'MODIFIED_BY' => $GLOBALS['USER']->GetID(), // элемент изменен текущим пользователем
               'IBLOCK_SECTION_ID' => false, // элемент лежит в корне раздела
               'IBLOCK_ID' => 4,
               'PROPERTY_VALUES' => $PROP,
               'NAME' => 'Элемент',
               'ACTIVE' => 'Y', // активен
               'PREVIEW_TEXT' => 'текст для списка элементов',
               'DETAIL_TEXT' => 'текст для детального просмотра',
               'DETAIL_PICTURE' => $_FILES['DETAIL_PICTURE'] // картинка, загружаемая из файлового поля веб-формы с именем DETAIL_PICTURE
           );

           $PRODUCT_ID = $el->Add($arLoadProductArray);


           $APPLICATION->throwException("Guest �� ����.");
           return false;
       }
   }
}*/
/*
 //Выборка данных
$dbElements = Bitrix\Iblock\ElementTable::query()
    ->setFilter(['IBLOCK_ID' => CATALOG_IBLOCK_ID, 'ACTIVE' => 'Y'])
    ->setSelect(['NAME', 'ID', 'DETAIL_PAGE_URL', 'DATE_ACTIVE_FROM'])
    ->addSelect('IBLOCK_SECTION_ID', 'PARENT_SECTION')
    ->setLimit(10)
    ->addOrder('id', 'DESC')
    ->exec();

while ($arElement = $dbElements->fetch()) {
    echo "{$arElement['NAME']} - " . $arElement['DATE_ACTIVE_FROM']->format('d.m.Y H:i:s');
}

//Добавление записи
$addResult = Bitrix\Iblock\ElementTable::add([
    'NAME' => 'Название нового элемента',
    'IBLOCK_ID' => CATALOG_IBLOCK_ID
]);
if (!$addResult->isSuccess()) {
    echo implode('<br>' ,$addResult->getErrorMessages());
}
 */





/*    function myHandler(Bitrix\Main\Event $event)
    {
        Bitrix\Main\Diag\Debug::writeToFile(array('ID' => $id, 'ID2' => $id), "", "logname.log");

        //$isNew = $event->getParameter("IS_NEW");
        echo 'Параметры, переданные в обработчик';
        echo "<pre style=\"display:block;\">";
        print_r($event->getParameters());
        echo "</pre>";

        // Возвращаем в событие результат обработки
        $result = new Bitrix\Main\EventResult($event->getEventType(), array(111, 222));
        return $result;
    }*/







/*public
static function beforePriceUpdate4444($PRICE_ID, $arFields)
{
    //$ee = $id->getParameters();
    \Bitrix\Main\Diag\Debug::writeToFile(array('ID' => $arFields), "", "beforePriceUpdate.log");
    return;

    CModule::IncludeModule("catalog");
    $price = CPrice::GetByID($id);
    define("OLD_PRICE_" . $id, $price['PRICE']);
    define("OLD_CURRENCY_" . $id, $price['CURRENCY']);
}
*/










/*$intIBlockID = 2;
$mxResult = \CCatalogSKU::GetInfoByProductIBlock($intIBlockID);
if (is_array($mxResult)) {

    $rsOffers = \CIBlockElement::GetList(
        array("PRICE" => "ASC"),
        array('IBLOCK_ID' => $mxResult['IBLOCK_ID'],
            'PROPERTY_' . $mxResult['SKU_PROPERTY_ID'] => $product_id)
    );
    while ($arOffer = $rsOffers->GetNext()) {
        $ar_price = GetCatalogProductPrice($arOffer["ID"], 1);
        break;
    }
}
}*/










//$product = \CCatalogProduct::GetByID($product_id);

/*        $old_price = \CPrice::GetList(
            [],
            [
                "ID" => $PRICE_ID
            ]
        )->fetch()['PRICE'];*/








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






/*    public static function beforeDelete($Id)
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
    }*/
