<?php



function autoLoader($sClassName)
{
    //class directories
    $aClassPath = array(
                        ROOT_PATH.'classes/',
                        ROOT_PATH.'api/',
                        ROOT_PATH.'api/conf/',
                        ROOT_PATH.'api/core/',
                        ROOT_PATH.'api/core/dto/',
                        ROOT_PATH.'api/core/mappers/',
                        ROOT_PATH.'api/core/interface/',
                        ROOT_PATH.'api/core/responses/',
                       );

    //for each directory
    foreach($aClassPath as $sDir) {
        //see if the file exists
        if (file_exists($sDir.$sClassName.'.php')) {
            require_once($sDir.$sClassName.'.php');
            return;
        }
    }
}

spl_autoload_register('autoLoader');