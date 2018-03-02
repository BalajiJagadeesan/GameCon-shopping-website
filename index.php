<?php

$title = "Home";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";
//If session exists get user details and sign in automatically
_checkSession(session_id());


?>
<!--Main page Design-->
<div id="col" class="row" >
    <div id="img1" class="col-sm-4" >
        <div class="box">
            <h3>Proud Supporters of Inde Games </h3>
        </div>
    </div>

    <div id="img2" class="col-sm-4">
        <div class="box">
            <h3>Offers on Console </h3>
        </div>
    </div>

    <div id="img3" class="col-sm-4">
        <div class="box">
            <h3>Discounts on AAA Titles</h3>
        </div>
    </div>
</div>
<?
require_once PATH_INC."footer.inc.php";
?>