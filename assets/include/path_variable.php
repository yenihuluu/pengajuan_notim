<?php

defined('DS')           ? null : define('DS',DIRECTORY_SEPARATOR);
defined('SITE_ROOT')    ? null : define('SITE_ROOT', dirname(dirname(dirname(__FILE__))));
defined('PATH_ASSETS')  ? null : define('PATH_ASSETS', SITE_ROOT.DS."assets");
defined('PATH_IMG')  ? null : define('PATH_IMG', PATH_ASSETS.DS."img");
defined('PATH_INCLUDE')  ? null : define('PATH_INCLUDE', PATH_ASSETS.DS."include");
defined('PATH_EXTENSION')  ? null : define('PATH_EXTENSION', PATH_ASSETS.DS."extensions");


/**
 * Check Internet Connection.
 * 
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @param            string $sCheckHost Default: www.google.com
 * @return           boolean
 */
function checkInternetConnection($sCheckHost = 'www.google.com') 
{
    return (bool) @fsockopen($sCheckHost, 80, $iErrno, $sErrStr, 5);
}