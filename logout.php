<?php
$title = "Logout";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

//If the previous page is from admin,product or cart ,remove session and cookies
if( $previousPage[5]=="product.php" || $previousPage[5]=="admin.php" || $previousPage[5]=="cart.php" ){
    echo "<br><br><h3 class='text-center'>You have been logout .Please wait,you will be redirected</h3>";
    //remove session and cookie
    _sessionDestroy();
    header("refresh:3;url=".URL_BASE);
}else{
    header("Location:".URL_BASE."index.php");
}
?>

<?
require_once PATH_INC."footer.inc.php";
?>