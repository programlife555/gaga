<?php
/**
 * Describe :upgrade 1.0.14(10014) to 1.1.0(10100)
 * Author: SAM<an.guoyue254@gmail.com>
 * Date: 2018/11/11
 * Time: 6:58 PM
 */

class Upgrade_From10014To10100 extends Upgrade_Version
{

    protected function doUpgrade()
    {
        return true;
    }

    protected function upgrade_DB_mysql()
    {
        return $this->executeMysqlScript();
    }

    protected function upgrade_DB_Sqlite()
    {
        return $this->executeSqliteScript();
    }

    public function upgrade_10014_10100()
    {
        $key = [
            "test_curl" => "testCurl",
            "session_verify_" => "sessionVerify",
        ];
        $this->updateSiteConfigKey($key);
        $result = $this->upgrade_10014_10100_siteSession();

        $this->upgrade_10014_10100_plugin();
        $this->upgradeErrCode = "success";
        return $result;
    }

    private function upgrade_10014_10100_plugin()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        $u2Gif = [
            'pluginId' => 104,
            'name' => "gif小程序",
            'logo' => $this->getSiteGifIcon(),
            'sort' => 2, //order = 2
            'landingPageUrl' => "index.php?action=miniProgram.gif.index",
            'landingPageWithProxy' => 1, //1 表示走site代理
            'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageU2Message,
            'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingChatbox,
            'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
            'authKey' => "",
            "management" => "",
            "addTime" => ZalyHelper::getMsectime()
        ];

        try {
            $this->ctx->SitePluginTable->insertMiniProgram($u2Gif);
        } catch (Exception $e) {
            $this->logger->error($tag, "ignore insert u2 104:" . $e);
        }

        $groupGif = [
            'pluginId' => 104,
            'name' => "gif小程序",
            'logo' => $this->getSiteGifIcon(),
            'sort' => 2, //order = 2
            'landingPageUrl' => "index.php?action=miniProgram.gif.index",
            'landingPageWithProxy' => 1, //1 表示走site代理
            'usageType' => Zaly\Proto\Core\PluginUsageType::PluginUsageGroupMessage,
            'loadingType' => Zaly\Proto\Core\PluginLoadingType::PluginLoadingChatbox,
            'permissionType' => Zaly\Proto\Core\PluginPermissionType::PluginPermissionAll,
            'authKey' => "",
            "management" => "",
            "addTime" => ZalyHelper::getMsectime()
        ];

        try {
            $this->ctx->SitePluginTable->insertMiniProgram($groupGif);
        } catch (Exception $e) {
            $this->logger->error($tag, "ignore insert  group 104:" . $e);
        }
        //update miniProgram management
        try {
            $data = [
                'landingPageUrl' => " https://duckchat.akaxin.com/wiki/",
            ];
            $where = [
                "pluginId" => 103,
            ];
            $this->ctx->SitePluginTable->updateProfile($data, $where);

            //site management default icon
            $data = [
                'logo' => $this->getPluginDefaultLogo("/public/img/manage/site_manage.png"),
            ];
            $where = [
                "pluginId" => 100,
            ];
            $this->ctx->SitePluginTable->updateProfile($data, $where);


            //site square default icon
            $data = [
                'logo' => $this->getPluginDefaultLogo("/public/img/manage/site_square.png"),
            ];
            $where = [
                "pluginId" => 199,
            ];
            $this->ctx->SitePluginTable->updateProfile($data, $where);

        } catch (Exception $e) {
            $this->logger->error($tag, "update 199 :" . $e);
        }
    }

    private function getSiteGifIcon()
    {
        $defaultIcon = WPF_ROOT_DIR . "/public/img/plugin/gif.png";
        if (!file_exists($defaultIcon)) {
            return "";
        }

        $defaultImage = file_get_contents($defaultIcon);
        $fileManager = new File_Manager();
        $fileId = $fileManager->saveFile($defaultImage, "20180201");
        return $fileId;
    }


    private function upgrade_10014_10100_siteSession()
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;

        try {
            $dbType = $this->ctx->dbType;

            $this->dropDBTable("siteSession_temp_10014");

            //rename table
            $sql = "alter table siteSession rename to siteSession_temp_10014";
            $result = $this->ctx->db->exec($sql);
            $this->logger->error($tag, "rename table siteSession to siteSession_temp_10014 result=" . $result);


            if ("mysql" == $dbType) {
                $this->executeMysqlScript();
            } else {
                //execute all table
                $this->executeSqliteScript();
            }
            //migrate data to new table
            $sql = "insert into 
                  siteSession(id ,sessionId ,userId ,deviceId ,devicePubkPem ,clientSideType ,timeWhenCreated ,timeActive, ipActive, userAgent
                  ,userAgentType,gatewayURL,gatewaySocketId) 
                select 
                  id ,sessionId ,userId ,deviceId ,devicePubkPem ,clientSideType ,timeWhenCreated ,timeActive, ipActive, userAgent
                  ,userAgentType,gatewayURL,gatewaySocketId
                from siteSession_temp_10014";
            $prepare = $this->ctx->db->prepare($sql);
            $flag = $prepare->execute();

            if ($flag && $prepare->errorCode() == "00000") {
                $this->upgradeErrCode = "success";
                $this->dropDBTable('siteSession_temp_10014');
                return true;
            }
            return true;
        } catch (Exception $ex) {
            $this->upgradeErrCode = "error";
            $this->logger->error($tag, $ex);
            throw new Exception(var_export($ex->getMessage(), true));
        }
    }

    private function getPluginDefaultLogo($logoPath)
    {
        $defaultIcon = WPF_ROOT_DIR . $logoPath;
        if (!file_exists($defaultIcon)) {
            return "";
        }

        $defaultImage = file_get_contents($defaultIcon);
        $fileManager = new File_Manager();
        $fileId = $fileManager->saveFile($defaultImage, "20180201");

        return $fileId;
    }
}