<?php
//initialise everything we need for the site
session_name("gamecon");
if(session_id() == '') {
    session_start();
}
//define constants

//PHP uses path
define("PATH_BASE","/home/bxj9142/Sites/commerce/");
define("PATH_INC", PATH_BASE. "assests/inc/");
define("PATH_JS",PATH_BASE."assests/js/");
define("PATH_CLASS",PATH_BASE."class/");
define("PATH_TEMPLATES",PATH_BASE."assests/templates/");
define("PATH_LIB",PATH_BASE."assests/lib/");
define("PATH_PRODUCT_IMG",PATH_BASE."assests/img/product/");

//HTML uses url
define("URL_BASE","http://kelvin.ist.rit.edu/~bxj9142/commerce/");
define("URL_JS",URL_BASE."assests/js/");
define("URL_CSS",URL_BASE."assests/css/");
define("URL_PRODUCT_IMG",URL_BASE."assests/img/product/");

//establish DB connections

//include function libraries
require_once (PATH_INC."functions.inc.php");
require_once (PATH_INC."function_card.inc.php");

?>