<?


use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

//if (class_exists("dev_module")) return;
// имя класса строчными и без точек иначе может глючить

Class price_reduction extends CModule
{

    var $MODULE_ID = "pricereduction";

    /** @var string */
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;

    /** @var string */
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    var $MODULE_SORT;

    var $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    var $MODULE_GROUP_RIGHTS;


    var $excludeFiles;


    function __construct()
    {

        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->excludeFiles = array(
            ".",
            "..",
            "menu.php",
            "operation_description.php",
            "task_description.php"
        );

        /*        if (is_array($this->arModuleVersion)
                    && array_key_exists("VERSION", $this->arModuleVersion)
                    && array_key_exists("MODULE_VERSION_DATE", $this->arModuleVersion)
                ) {
                    $this->MODULE_VERSION = $this->arModuleVersion["VERSION"];
                    $this->MODULE_VERSION_DATE = $this->arModuleVersion["VERSION_DATE"];
                } else {
                    $this->MODULE_VERSION = "0.0.0";
                    $this->MODULE_VERSION_DATE = "0000-00-00 00:00:00";
                }*/

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION");

        $this->PARTNER_NAME = Loc::getMessage("PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("PARTNER_URI");

        $this->MODULE_SORT = 1;

        $this->MODULE_GROUP_RIGHTS = "Y";
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";

    }


    function DoInstall()
    {
        global $APPLICATION;

        if (CheckVersion(\Bitrix\Main\ModuleManager::getVersion("main"), "14.00.00")) {//$this->isVersionD7()

            \Bitrix\Main\ModuleManager::RegisterModule($this->MODULE_ID);

            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallFiles();

        } else {

            $APPLICATION->ThrowException(Loc::getMessage("BITRIX_VERSION_INCOMPATIBLE"));

        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_INSTALL_TITLE"), $this->GetPath() . "/install/step.php");


    }


    function DoUninstall()
    {
        global $APPLICATION;

        $request = Application::getInstance()->getContext()->getRequest();

        if ($request["step"] < 2) {

            $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep1.php");

        } elseif ($request["step"] == 2) {

            $this->UnInstallEvents();
            $this->UnInstallFiles();


            if ($request["savedata"] != "Y") {
                $this->UnInstallDB();
            }

            \Bitrix\Main\ModuleManager::UnRegisterModule($this->MODULE_ID);

            $APPLICATION->IncludeAdminFile(Loc::getMessage("MODULE_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep2.php");

        }

    }


    function InstallFiles($arParams = array())
    {
        CopyDirFiles($this->GetPath() . "/install/components", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/", true, true);

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . "/admin")) {
            CopyDirFiles($this->GetPath() . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);


            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->excludeFiles)) {
                        continue;
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/" . $this->MODULE_ID . "_" . $item,
                            '<' . '? require($_SERVER["DOCUMENT_ROOT"] ."' . $this->GetPath(true) . "/admin/" . $item . '");?' . '>');
                    }
                }
                closedir($dir);
            }
        }
        return true;
    }


    function UnInstallFiles()
    {
        return;
        Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/mad/");

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . "/admin")) {
            //DeleteDirFiles($this->GetPath() . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/", true, true);


            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->excludeFiles)) {
                        continue;
                        //\Bitrix\Main\IO\Directory::deleteFile($_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin/" . MODULE_ID . "_" . $item);

                    }
                }
                closedir($dir);
            }
        }
        return true;
    }


    function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }

    }


    function InstallDB()
    {
        $this->createHiloadBlock();
        $this->createMailEventType();
        $this->createMailEventTemplate();

        return true;
    }


    function UnInstallDB()
    {
        $this->deleteHiloadBlock();
        //$this->deleteMailEventType();
        //$this->deleteMailEventTemplate();

        return true;
    }


    function InstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        //$handlers = $eventManager->findEventHandlers("catalog", "onPriceUpdate");


        $eventManager->registerEventHandler(
            'catalog',
            'onPriceUpdate',
            'pricereduction',
            '\MadSoft\PriceReduction\Lib\Handler', 'onPriceUpdate');

        return true;
    }


    function UnInstallEvents()
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'catalog',
            'onPriceUpdate',
            'pricereduction',
            '\MadSoft\PriceReduction\Lib\Handler', 'onPriceUpdate');

        return true;
    }


    /**
     * Удаление Highloadblock по ID или NAME
     * Пример: deleteHiloadBlock(5); deleteHiloadBlock("PriceReduction");
     *
     * @param $hlblock
     * @return mixed
     */
    public function deleteHiloadBlock($hlblock = "PriceReduction")
    {
        $hlblock_delete_result = false;

        if (!empty($hlblock)) {

            $hlblock_params = \Bitrix\Highloadblock\HighloadBlockTable::getList([
                'select' => ['ID'],
                'filter' => [
                    'LOGIC' => 'OR', ['ID' => $hlblock], ['NAME' => $hlblock]
                ],
                'limit' => 1
            ])->fetch();

            if (!empty($hlblock_params)) {

                $hlblock_delete_result = \Bitrix\Highloadblock\HighloadBlockTable::delete($hlblock_params['ID']);

            }
        }

        return $hlblock_delete_result;
    }


// создание hiload блока
    public function createHiloadBlock()
    {

        //CModule::IncludeModule('highloadblock');
        \Bitrix\Main\Loader::includeModule('highloadblock');

        self::deleteHiloadBlock("PriceReduction");

        $hltable_name = [
            'NAME' => "PriceReduction",
            'TABLE_NAME' => "pricereduction"
        ];


        $hlblock_create_result = HighloadBlockTable::add($hltable_name);

        if ($hlblock_create_result->isSuccess()) {
            $hblock_id = $hlblock_create_result->getId();

            Option::set("mad", "hblock_id", $hblock_id);

            $oUserTypeEntity = new CUserTypeEntity();
            $email_field = array(
                'ENTITY_ID' => 'HLBLOCK_' . $hblock_id,
                'FIELD_NAME' => 'UF_EMAIL',
                'USER_TYPE_ID' => 'string',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'EDIT_FORM_LABEL' => array(
                    'ru' => 'E-mail',
                    'en' => 'E-mail',
                )
            );

            $productid_field = array(
                'ENTITY_ID' => 'HLBLOCK_' . $hblock_id,
                'FIELD_NAME' => 'UF_PRODUCT_ID',
                'USER_TYPE_ID' => 'integer',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'EDIT_FORM_LABEL' => array(
                    'ru' => 'ID продукта',
                    'en' => 'ID product',
                ),
                'SETTINGS' => array(
                    "DEFAULT_VALUE" => "0",
                )
            );

            $price_field = array(
                'ENTITY_ID' => 'HLBLOCK_' . $hblock_id,
                'FIELD_NAME' => 'UF_PRICE',
                'USER_TYPE_ID' => 'integer',
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'I',
                'EDIT_FORM_LABEL' => array(
                    'ru' => 'Цена',
                    'en' => 'Price',
                ),
                'SETTINGS' => array(
                    "DEFAULT_VALUE" => "0",
                )
            );

            $emailUserFieldres = $oUserTypeEntity->Add($email_field);
            $productidUserFieldres = $oUserTypeEntity->Add($productid_field);
            $productidUserFieldres = $oUserTypeEntity->Add($price_field);

        } else {
            echo "error";
            $errors = $hlblock_create_result->getErrorMessages();
            print_r($errors);
        }
    }


//Настройки > Настройки продукта > Почтовые события > Типы почтовых событий
    public function createMailEventType()
    {

        $event_type = new CEventType;

        if (!$event_type->Delete("PRICE_REDUCTION")) {
            echo Loc::getMessage("DELETE_ERROR");
        }


        $mail_event_params = [
            "LID" => "ru",
            "EVENT_NAME" => "PRICE_REDUCTION",
            "NAME" => "Снижение цены продукта",
            "DESCRIPTION" => "
                #EMAIL# - E-mail
                #PRODUCT_ID# - ID продукта
                #PRODUCT_NAME# - Наименование продукта
                #PRICE# - Цена"
        ];

        $mail_event_id = $event_type->Add($mail_event_params);

        if ($mail_event_id) {
            Option::set("mad", "mail_event_id", $mail_event_id);
        }

        // echo "<pre>";
        // print_r($event_type);
        // echo "</pre>";
    }


    //Настройки > Настройки продукта > Почтовые события > Почтовые шаблоны

    public function createMailEventTemplate()
    {

        $event_message = new CEventMessage;

        $old_mail_template_id = Option::get("mad", "mail_template_id");
        if ($old_mail_template_id) {
            if (!$event_message->Delete($old_mail_template_id)) {
                echo Loc::getMessage("DELETE_ERROR");
            }
        }

        $mail_template_params = [
            "ACTIVE" => "Y",
            "EVENT_NAME" => "PRICE_REDUCTION",
            "LID" => SITE_ID,
            "EMAIL_FROM" => "#SALE_EMAIL#",
            "EMAIL_TO" => "#EMAIL#",
            "BCC" => "#BCC#",
            "SUBJECT" => "Снижение цены продукта - #PRODUCT_ID#",
            "MESSAGE" => "
                    На сайте было снижение цены продукта.
                    ID: #PRODUCT_ID#
                     Наименование продукта: #PRODUCT_NAME#
                    Новая цена: #PRICE#
             ",
            "BODY_TYPE" => "text",
        ];

        $mail_template_id = $event_message->Add($mail_template_params);

        if ($mail_template_id) {
            Option::set("mad", "mail_template_id", $mail_template_id);
        }

//        echo "<pre>";
//        print_r($event_message);
//        echo "</pre>";

    }


}


?>