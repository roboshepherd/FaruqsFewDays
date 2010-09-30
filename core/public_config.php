<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // core/public_config.php                                //
    // GPLv3                                                 //
    // smiffy6969                                            //
    // www.razorcms.co.uk                                    //
    // www.mis-limited.com                                   //
    // 03/2008                                               //
    // ----------------------------------------------------- //
    // V0.1  -  03/2008  -  Version 0.1 first release        //
    //                                                       //
    //                      Some parts loosely based on      //
    //                      nanoCMS V0.3 - with thanks to    //
    //                      Kalyan Chakravarthy              //
    // ----------------------------------------------------- //
    // V0.2  -  06/2008  -  This file contains changes for   //
    //                      up issue to V0.2RC               //
    // ----------------------------------------------------- //
    // V0.2  -  08/2008  -  RC2 Bug fix release              //
    //                      No changes to this file          //
    // ----------------------------------------------------- //
    // V0.3  -  11/2008  -  BETA1 Bug fix and new features   //
    // ----------------------------------------------------- //
    // V0.3  -  12/2008  -  RC Bug fix and code walk         //
    // ----------------------------------------------------- //
    // V0.3  -  02/2009  -  RC2 Bug fix, code walk and some  //
    //                      movement of functions.           //
    // ----------------------------------------------------- //
    // V1.0  -  06/2009  -  ALPHA Security, Bug fix and new  //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  07/2009  -  BETA Bug fix and new             //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  08/2009  -  BETA2 Bug fix and new            //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  09/2009  -  RC Bug fix                       //
    // ----------------------------------------------------- //
    // V1.0  -  10/2009  -  Stable - Bug fix                 //
    // ----------------------------------------------------- //
    // V1.1  -  06/2010  -  Stable - Bug fix + Security fix  //
    // ----------------------------------------------------- //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////
    //   Optional System Configuration   //
    // EDIT LINES BELOW AT YOUR OWN RISK //
    ///////////////////////////////////////

    $RAZOR = array();

    // location of logs directory //
    $RAZOR['logs_dir'] = 'datastore/razor_temp_logs/';

    // location of failed login log //
    $RAZOR['failed_logs'] = 'razor_failed_login.txt';

    // location of datastore directory //
    $RAZOR['datastore_dir'] = 'datastore/';

    // location of system data file razor_data.txt //
    $RAZOR['system_file'] = 'datastore/razor_data.txt';

    // location of pages dir for stored content //
    $RAZOR['pages_dir'] = 'datastore/pages/';

    // location of media dir for stored content //
    $RAZOR['backup_dir'] = 'datastore/backup/';

    // location of blade packs //
    $RAZOR['bladepack_dir'] = 'blade_packs/';

    // name of homepage filename //
    $RAZOR['index_filename'] = 'index.php';

    // location of admin directory //
    $RAZOR['admin_dir'] = 'admin/';

    // name and location of admin filename //
    $RAZOR['admin_filename'] = 'admin/index.php';

    // name and location of admin config //
    $RAZOR['admin_config'] = 'admin/core/admin_config.php';

    // name and location of default public CSS file //
    $RAZOR['css_file_name'] = 'theme/default_css.css';

    // path to script directory to use in themes //
    $RAZOR['script_path'] = 'theme/scripts/';

    // default content name, sets the name of content loaded into the page //
    $RAZOR['default_content_name'] = 'slab';
    
    // default ext for filenames created for content //
    $RAZOR['default_file_ext'] = 'txt';

    // current version //
    $RAZOR['razor_current_ver'] = 'core v1.1 Stable';

    // sets amount of time max login attempts allowed in seconds eg 3600 = 60min //
    $RAZOR['razor_logat_time'] = 3600;

    // sets amount of failed login attempts allowed in time above //
    $RAZOR['razor_log_att'] = 8;

    // coning amount to reduce logs when checking, will not check more than this amount in the max time //
    $RAZOR['razor_log_amount'] = 60;
    
    // file manager path for sadmin //
    $RAZOR['razor_sadmin_path'] = '';
    
    // file manager path for admin and user //
    $RAZOR['razor_fileman_path'] = $RAZOR['datastore_dir'];

    // allowed file types to edit in file manager //
    $RAZOR['fileman_allowed_edit'] = 'txt,htm,html,php,htaccess,css';

    // allowed file types to view in file manager (document types only) //
    $RAZOR['fileman_allowed_view_doc'] = 'txt,pdf,htm,html,doc,xls,odt';

    // allowed file types to view in file manager (media types only) //
    $RAZOR['fileman_allowed_view_med'] = 'jpg,jpeg,png,gif,bmp';

    // allowed file types to view in file manager (media types only) //
    $RAZOR['html_charsets'] = 'ISO-8859-1,ISO-8859-2,ISO-2022-JP,EUC-KR,US-ASCII,UTF-8,SHIFT_JIS';

    ///////////////////////////////////////////////
    //  Assigning variables into data constants  //
    //          DO NOT EDIT THESE LINES          //
    ///////////////////////////////////////////////

    define( 'RAZOR_LOGS_DIR' , $RAZOR['logs_dir'] );
    define( 'RAZOR_FAILED_LOGIN_LOG' , $RAZOR['failed_logs'] );
    define( 'RAZOR_DATASTORE_DIR' , $RAZOR['datastore_dir'] );
    define( 'RAZOR_DATA' , $RAZOR['system_file'] );
    define( 'RAZOR_EXTENSIONS_ORDER' , "txt,php,htm,html" );
    define( 'RAZOR_PAGES_DIR' , $RAZOR['pages_dir'] );
    define( 'RAZOR_BACKUP_DIR' , $RAZOR['backup_dir'] );
    define( 'RAZOR_URL_FORMAT' , $RAZOR['index_filename'].'?'.$RAZOR['default_content_name'].'=%s' );
    define( 'RAZOR_ADMIN_DIR' , $RAZOR['admin_dir'] );
    define( 'RAZOR_ADMIN_FILENAME' , $RAZOR['admin_filename'] );
    define( 'RAZOR_ADMIN_CONFIG' , $RAZOR['admin_config'] );
    define( 'RAZOR_HOME_FILENAME' , $RAZOR['index_filename'] );
    define( 'RAZOR_BLADEPACK_DIR' , $RAZOR['bladepack_dir'] );
    define( 'RAZOR_CSS_FILE' , $RAZOR['css_file_name'] );
    define( 'RAZOR_SCRIPT_PATH' , $RAZOR['script_path'] );
    define( 'RAZOR_DEFAULT_CONTENT_NAME' , $RAZOR['default_content_name'] );
    define( 'RAZOR_DEFAULT_FILE_EXT' , $RAZOR['default_file_ext'] );
    define( 'RAZOR_CURRENT_VERSION' , $RAZOR['razor_current_ver'] );
    define( 'RAZOR_LOGAT_TIME' , $RAZOR['razor_logat_time'] );
    define( 'RAZOR_LOG_ATT' , $RAZOR['razor_log_att'] );
    define( 'RAZOR_LOG_AMOUNT' , $RAZOR['razor_log_amount'] );
    define( 'RAZOR_SADMIN_PATH' , $RAZOR['razor_sadmin_path'] );
    define( 'RAZOR_FILEMAN_PATH' , $RAZOR['razor_fileman_path'] );
    define( 'RAZOR_FILEMAN_EDIT_TYPE' , $RAZOR['fileman_allowed_edit'] );
    define( 'RAZOR_FILEMAN_VIEW_DOC' , $RAZOR['fileman_allowed_view_doc'] );
    define( 'RAZOR_FILEMAN_VIEW_MED' , $RAZOR['fileman_allowed_view_med'] );
    define( 'RAZOR_HTML_CHARSETS' , $RAZOR['html_charsets'] );
?>
