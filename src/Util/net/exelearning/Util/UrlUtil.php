<?php

namespace App\Util\net\exelearning\Util;

use App\Constants;

/**
 * UrlUtil.
 *
 * Utility functions for working with Urls
 */
class UrlUtil
{
    /**
     * Returns relative url to TEMPORARY_CONTENT_STORAGE_DIRECTORY dir.
     *
     * @return string
     */
    public static function getTemporaryContentStorageUrl()
    {
        return Constants::FILES_DIR_NAME.Constants::SLASH.Constants::TEMPORARY_CONTENT_STORAGE_DIRECTORY;
    }

    /**
     * Returns relative url to a session dir.
     *
     * @return string
     */
    public static function getOdeSessionUrl($odeSessionId)
    {
        // validade $odeSessionId
        if (strlen($odeSessionId) < 8) {
            return false;
        }

        // Create iDevice dir
        $year = substr($odeSessionId, 0, 4);
        $month = substr($odeSessionId, 4, 2);
        $day = substr($odeSessionId, 6, 2);

        return UrlUtil::getTemporaryContentStorageUrl().Constants::SLASH.$year.Constants::SLASH.
            $month.Constants::SLASH.$day.Constants::SLASH.$odeSessionId.Constants::SLASH;
    }

    /**
     * Returns relative url to an OdeComponentsSync dir.
     *
     * @return string
     */
    public static function getOdeComponentsSyncUrl($odeSessionId, $odeIdeviceId)
    {
        $odeSessionUrl = self::getOdeSessionUrl($odeSessionId);

        if (!$odeSessionUrl) {
            return false;
        } else {
            return $odeSessionUrl.$odeIdeviceId.Constants::SLASH;
        }
    }

    /**
     * Returns relative url to tmp ode session dir.
     *
     * @return string
     */
    public static function getOdeSessionTmpUrl($odeSessionId)
    {
        $odeSessionUrl = self::getOdeSessionUrl($odeSessionId);

        if (!$odeSessionUrl) {
            return false;
        } else {
            return $odeSessionUrl.Constants::ODE_SESSION_TMP_DIR.Constants::SLASH;
        }
    }

    /**
     * Returns relative url to user tmp ode session dir.
     *
     * @return string
     */
    public static function getOdeSessionTmpUserUrl($odeSessionId, $user)
    {
        $odeSessionTmpUrl = self::getOdeSessionTmpUrl($odeSessionId);

        if (!$odeSessionTmpUrl || !$user) {
            return false;
        } else {
            return $odeSessionTmpUrl.$user->getUserId().Constants::SLASH;
        }
    }

    /**
     * Returns relative url to user tmp ode session export dir.
     *
     * @return string
     */
    public static function getOdeSessionExportUrl($odeSessionId, $user)
    {
        $odeSessionTmpUserUrl = self::getOdeSessionTmpUserUrl($odeSessionId, $user);

        if (!$odeSessionTmpUserUrl || !$user) {
            return false;
        } else {
            return $odeSessionTmpUserUrl.Constants::EXPORT_TMP_DIR.Constants::SLASH;
        }
    }

    /**
     * Returns relative url to iDevices dir.
     *
     * @return string
     */
    public static function getIdevicesUrl()
    {
        return Constants::IDEVICES_DIR_NAME;
    }

    /**
     * Returns relative url to and iDevice dir.
     *
     * @param string $iDeviceDir
     *
     * @return string
     */
    public static function getIdeviceUrl($iDeviceDir)
    {
        return self::getIdevicesUrl().Constants::SLASH.$iDeviceDir.Constants::SLASH;
    }

    /**
     * Returns relative url to themes dir.
     *
     * @return string
     */
    public static function getThemesUrl()
    {
        return Constants::THEMES_DIR_NAME;
    }

    /**
     * Returns relative url to and theme dir.
     *
     * @param string $themeDir
     */
    public static function getThemeUrl($themeDir)
    {
        // TODO
    }
}
