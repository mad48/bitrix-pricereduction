<?
//use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = Array(
    "PARAMETERS" => Array(

/*        'HBLOCK_ID' => array(
            'NAME' => 'Id инфоблока',
            'TYPE' => 'STRING',
            'MULTIPLE' => 'N',
            'PARENT' => 'BASE',
        ),*/

/*        "BUTTON_ID" => Array(
            "NAME" => Loc::getMessage("PRICE_REDUCTION_BUTTON_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),*/
        "BUTTON_CLASS" => Array(
            "NAME" =>  Loc::getMessage("PRICE_REDUCTION_BUTTON_CLASS"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
        "CACHE_TIME" => array("DEFAULT" => 3600),
    )
);