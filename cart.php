<?php

$title = "Cart";
require_once "./assests/inc/page_start.inc.php";
require_once PATH_INC."header.inc.php";

//Get user id
$user_array = _getUser($_COOKIE['email']);
$cid = $user_array[0]['customer_id'];

//get cart items for the user
$items = _getCartItems($cid);
$total = array();

//If button clicked is to clear an item
if(!empty($_POST['clear-item'])){
    //Delete the item that belong to user
    if(_deleteItemInCart($_POST['data'])){
        header("Location:".URL_BASE."cart.php");
    }
}

//if button clicked is to clear all item
if(!empty($_POST['clear-all'])){
    //Get all the item form the data input
    $cart_array = unserialize($_POST['data']);
    //Go through each item and delete it
    foreach($cart_array as $cart_item){
        if(!_deleteItemInCart($cart_item)){
            $errors[]="Error in removing item";
        }
    }
    //Print error if any
    if(!empty($errors)){
        _printErrors($errors);
    }
    //If no error reload the page
    if(empty($errors)){
        header("Location:".URL_BASE."cart.php");
    }
}

//If cart items are empty ,print empty otherwise fetch the product and fill the list
if(empty($items[0]['customer_id'])){
    echo "<div class='container'><h3 class='text-center'> Cart is Empty </h3></div>";
}else {
    ?>
    <div class="container">
        <div class="col-md-8 ">
            <ul id="pad" class="list-group">
                <?
                $cart = array();
                foreach ($items as $item) {
                    $product=_getSelectedProduct($item['product_id']);
                    //form data with customer_id,product_id and quantity which is passed to function to move between the cart
                    $data = $item['customer_id']."_".$item['product_id']."_".$item['quantity'];
                    $cart[] = $data;
                    $temp=_getPrice($product[0]['product_price'],$product[0]['product_discount']);
                    $price = $temp * $item['quantity'];
                    $total[] = $price;
                    ?>
                    <li class="list-group-item">
                        <div class="col-md-6">
                            <p><?=$product[0]['product_name']?></p>
                            <small><?=$product[0]['product_description']?></small>
                        </div>
                        <div class="col-md-6">
                            <strong class="text-left">
                                <?=$item['quantity']?> x <?=$temp?> = <?=$price?>
                            </strong>
                            <form class="text-right" method="post" target="_self">
                                <input hidden value="<?=$data?>" name="data">
                                <button class="btn btn-circle btn-sm blue" name="clear-item" value="1" type="submit">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                    <?
                }
                ?>
                <li class="list-group-item">
                    <div class="col-md-8">
                        <p>Total:</p>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-center">= <?=_getTotalPrice($total)?> $</strong>
                        <form class="text-right" method="post" target="_self">
                            <input hidden value="<?=htmlentities(serialize($cart));?>" name="data">
                            <button class="btn btn-circle btn-sm blue" name="clear-all" value="1" type="submit">
                                <i class="fa fa-minus"></i>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <?
}
require_once PATH_INC."footer.inc.php";
?>

