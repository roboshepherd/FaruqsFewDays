<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // admin/core/admin_config.php                           //
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

    /////////////////////////////////////
    //  Optional System Configuration  //
    /////////////////////////////////////

    $RAZOR = array();
    
    // super admin username - default is razor //
    $RAZOR['sadmin_username'] = 'admin';

    // super admin password - default is razor //
    $RAZOR['sadmin_password'] = 'b27e5521a4274147116126ff549f85f122b6de010fb64fd6f7cf8412947392b1a87f017578058c2b';
    
    // admin username - default is blank (not active) //
    $RAZOR['admin_username'] = '';

    // admin password - default is blank (not active) //
    $RAZOR['admin_password'] = '';
    
    // user username - default is blank (not active) //
    $RAZOR['user_username'] = '';

    // user password - default is blank (not active) //
    $RAZOR['user_password'] = '';

    ///////////////////////////////////////////////
    //  Assigning variables into data constants  //
    //         DO NOT EDIT LINES BELOW           //
    ///////////////////////////////////////////////

    define( 'RAZOR_SADMIN_USER' , $RAZOR['sadmin_username'] );
    define( 'RAZOR_SADMIN_PASS' , $RAZOR['sadmin_password'] );
    define( 'RAZOR_ADMIN_USER' , $RAZOR['admin_username'] );
    define( 'RAZOR_ADMIN_PASS' , $RAZOR['admin_password'] );
    define( 'RAZOR_USER_USER' , $RAZOR['user_username'] );
    define( 'RAZOR_USER_PASS' , $RAZOR['user_password'] );
    define( 'RAZOR_ADMIN_LOGGED' , '');
?>
