<?php
function _buildCard($discounted,$title){
    ?>
    <br>
    <h3 class="text-center"><?=$title?></h3>
    <div class="container">
        <div class="row">
            <?
            foreach ($discounted as $discountItem){
                $salePrice = _getPrice($discountItem['product_price'],$discountItem['product_discount'])
                ?>
                <div class="col-md-4">
                    <div id="pad" class="card">
                        <img id="product-img" class="card-img-top" src="<?=$discountItem['product_image']?>" alt="product-image">
                        <div class="card-block">
                            <h4 class="card-title"><?=$discountItem['product_name']?></h4>
                            <p class="card-text"><?=$discountItem['product_description']?></p>
                            <p><strong>In Stock:</strong> <?=$discountItem['product_quantity']?></p>
                            <?
                            if($discountItem['product_discount']!=0) {
                                ?>
                                <p>
                                    <del>
                                        <strong>Original Price:</strong>
                                        $ <?= number_format((float)$discountItem['product_price'], 2, ".", ""); ?>
                                    </del>
                                    &nbsp;
                                    <span id="discount">
                                        <i class="fa fa-arrow-down"></i> <?= $discountItem['product_discount'] ?> % discount
                                    </span>
                                </p>
                                <?
                            }
                            ?>
                            <p>
                                <strong>Sale Price:</strong> $ <?=$salePrice;?>
                            </p>
                        </div>
                        <form class="text-right" method="post" target="_self">
                            <button class="btn btn-circle btn-lg green" name="add-cart" type="submit" value="<?=$discountItem["product_id"]?>"><i class="fa fa-plus fa-2x"></i></button>
                        </form>
                    </div>
                </div>
                <?
            }
            ?>
        </div>
    </div>
    <?
}
?>