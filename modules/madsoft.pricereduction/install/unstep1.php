<?php

use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) return;

Loc::loadMessages(__FILE__);

?>

<form action="<?=$APPLICATION->getCurPage(); ?>">
    <?= bitrix_sessid_post() ?>
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>"/>
    <input type="hidden" name="id" value="madsoft.pricereduction"/>
    <input type="hidden" name="uninstall" value="Y"/>
    <input type="hidden" name="step" value="2"/>

    <?= CAdminMessage::ShowMessage(Loc::getMessage('MOD_UNINST_WARN')); ?>
    <p><?= Loc::getMessage('MOD_UNINST_SAVE') ?></p>

    <p><input type="checkbox" name="savedata" value="Y" checked="checked"/>
        <label for="savedata"><?= Loc::getMessage('MOD_UNINST_SAVE_TABLES') ?></label>
    </p>

    <input type="submit" name="" value="<?= Loc::getMessage('MOD_UNINST_DEL') ?>"/>
</form>

