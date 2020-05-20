<?php
/**
 * Fondy Payment Module
	*
 * NOTICE OF LICENSE
	*
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
	*
 * @category        Fondy
 * @package         fondy.fondy
 * @version         1.1.7
 * @author          Fondy
 * @copyright       Copyright (c) 2016 Fondy
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
	*
 * EXTENSION INFORMATION
	*
 * 1C-Bitrix        16.0
 * Fondy API       https://fondy.eu
	*
 */

IncludeModuleLangFile(__FILE__);

class fondy_fondy extends CModule
{

    const MODULE_ID = 'fondy.fondy';
    const PARTNER_NAME = 'DM';
    const PARTNER_URI = 'https://fondy.eu';

    var $MODULE_ID = 'fondy.fondy';
    var $PARTNER_NAME = 'DM';
    var $PARTNER_URI = 'https://fondy.eu';

    public $MODULE_GROUP_RIGHTS = 'N';

    public function __construct()
    {
        require(dirname(__FILE__).'/version.php');
        $this->MODULE_NAME = GetMessage('F_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('F_MODULE_DESC');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->PARTNER_NAME = 'DM';
        $this->PARTNER_URI = 'http://fondy.eu';
    }

    public function DoInstall()
    {
        if (IsModuleInstalled('sale')) {
            global $APPLICATION;
            $this->InstallFiles();
            RegisterModule($this->MODULE_ID);
            return true;
        }

        $MODULE_ID = $this->MODULE_ID;
        $TAG = 'VWS';
        $MESSAGE = GetMessage('F_ERR_MODULE_NOT_FOUND', array('#MODULE#'=>'sale'));
        $intID = CAdminNotify::Add(compact('MODULE_ID', 'TAG', 'MESSAGE'));

        return false;
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        COption::RemoveOption($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/sale_payment/fondy_result',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/fondy_result',
            true, true
        );
		CopyDirFiles(
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/sale_payment',
			$_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment',
		true, true
        );
    }

    public function UnInstallFiles()
    {
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/fondy");
		DeleteDirFilesEx("/bitrix/tools/fondy_result");
        return true;
    }
}