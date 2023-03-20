<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_POST["Update"]) > 0 && check_bitrix_sessid()) {
    COption::SetOptionString("qbsnotification", "field_name", $_REQUEST["field_name"]);
}

$fieldValue = COption::GetOptionString("qbsnotification", "field_name", "");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => Loc::getMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_SET")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <?php
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%">
            <label for="field_name"><?=Loc::getMessage("MY_MODULE_FIELD_NAME")?>:</label>
        </td>
        <td width="60%">
            <!--<input type="text" name="field_name" id="field_name" width="500px" value="phptag//htmlspecialcharsbx($fieldValue)?>"> -->
            <textarea cols="60" rows="10" name="field_name" id="field_name" ><?=htmlspecialcharsbx($fieldValue)?></textarea>
        </td>
    </tr>

    <?php
    $tabControl->Buttons();
    ?>
    <input type="submit" name="Update" value="<?=Loc::getMessage("MAIN_SAVE")?>" class="adm-btn-save">
    <!-- <input type="reset" name="reset" value="<?=Loc::getMessage("MAIN_RESET")?>"> -->
    <?php
    $tabControl->End();
    ?>
</form>


