<?php
$title = "Product";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

//Check if session and session variables exist else ask user to login again
if (!isset($_SESSION) || $_SESSION["loggedIn"] == false || !isset($_COOKIE['loggedIn']) || !isset($_COOKIE['user']) || !isset($_COOKIE['email'])) {
    header("Location:".URL_BASE."login.php");
}
//If form is submitted
if(!empty($_POST)){

    //get the input
    $pid = $_POST['add-cart'];
    $user_array = _getUser($_COOKIE['email']);
    $cid = $user_array[0]['customer_id'];

    //decrease 1 in product table for selected product
    if(_decreaseQuantity(1,$pid,true)){
        //increase 1 in cart table for that selected product
        if(!_insertCart($cid,$pid)){
            $errors[]="Error in adding item to cart";
        }
    }else{
        $errors[] = "Error in updating product quantity";
    }
    //if error print error
    if (isset($errors)) {
        _printErrors($errors);
    }
    //success is no error
    if (!isset($errors)) {
        echo "<h4 class='text-center green'>Item added to cart</h4>";
    }
}
//For pagination of products in catalog check page number is present else redirect
if(empty($_GET['page'])){
    header("Location:".URL_BASE."product.php/?page=1");
}else{
    //get the nof products
    $notDiscounted =_getDiscountedProduct(false,0,0);
    //get the no of pages
    $pages = ceil(sizeof($notDiscounted)/3);
    //get the current page
    $currentPage = $_GET['page'];
    $start= (($currentPage-1)*3);
    //discounted item no pagination
    _buildCard(_getDiscountedProduct(true),"Discounted Products");

    //Non-discounted item ,paginated
    _buildCard(_getDiscountedProduct(false,$start,3),"Catalog");
    ?>
    <!-- Create pagination links and display on the page -->
    <div class="center">
        <div class="pagination">
            <? for($x=1;$x<=$pages;$x++):?>
                <a class="<?=($currentPage==$x)?"waves-button":""?>" href="<?=URL_BASE?>product.php?page=<?=$x?>"><?=$x?></a></button>
            <?endfor?>
        </div>
    </div><br>
    <?
}



require_once PATH_INC."footer.inc.php";
?>
