<?php
/**
 * check current version need to upgrade
 * User: anguoyue
 * Date: 13/10/2018
 * Time: 3:54 PM
 */

class Page_Version_UpgradeController extends Page_VersionController
{
    private $versionCode;
    private $versionName;

    private $upgradeErrCode = "error";
    private $upgradeErrInfo;

    function doRequest()
    {
        try {
            //校验upgradePassword
            if (!$this->checkUpgradePassword()) {
                throw new Exception("error upgrade password");
            }
            $currentCode = $_POST["versionCode"];
            if (empty($currentCode)) {
                $currentCode = 10011;
            }

            $result = false;

            if ($currentCode <= 10011) {
                $this->versionCode = 10012;
                $this->versionName = "1.0.12";
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10012) {
                $this->versionCode = 10013;
                $this->versionName = "1.0.13";
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10013) {
                $this->versionCode = 10014;
                $this->versionName = "1.0.14";
                $result = $this->upgrade_10013_10014();

            } elseif ($currentCode >= 10014 && $currentCode < 10100) {
                $this->versionCode = 10100;
                $this->versionName = "1.1.0";
                $result = $this->upgrade_10014_10100();
            } elseif ($currentCode == 10100) {
                $this->versionCode = 10101;
                $this->versionName = "1.1.1";
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
            } elseif ($currentCode == 10101) {
                $this->versionCode = 10102;
                $this->versionName = "1.1.2";
                $result = Upgrade_Client::doUpgrade($currentCode, $this->versionCode);
                //最新版本审计完成以后，删除密码存储文件，准备下次更新新密码
                $this->deleteUpgradeFile();
            }

            if ($result) {
                $this->upgradeErrCode = "success";
            }

            //update cache if exists
            if (function_exists("opcache_reset")) {
                opcache_reset();
            }

            $this->setUpgradeVersion($this->versionCode, $this->versionName, $this->upgradeErrCode, $this->upgradeErrInfo);

            if ($result) {
                $this->updateSiteConfigAsUpgrade($this->versionCode, $this->versionName);
            }

        } catch (Exception $e) {
            $this->logger->error("page.version.upgrade", $e);
            $this->setUpgradeVersion($this->versionCode, $this->versionName, "error", $e->getMessage() . " " . $e->getTraceAsString());
        }

        return;
    }


    private function checkUpgradePassword()
    {
        $upgradePassword = $_COOKIE['upgradePassword'];

        $serverPassword = $this->getUpgradePassword();
        $serverPassword = trim($serverPassword);

        if ($upgradePassword != sha1($serverPassword)) {
            throw new Exception("upgrade gaga-server by error password");
        }

        return true;
    }

}