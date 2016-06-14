<div class="ajaxproduit" itemscope itemtype="http://schema.org/Product">
    <div class="ajaxphoto">
        <img src="{$MPURI}/img/p/{$image->getImgPath()}-home_default.jpg" alt="{$productDto->getProductName()}" title="{$productDto->getProductName()}" itemprop="image" />
    </div>
    <div class="ajaxcontent">
        <h5 itemprop="name" class="productname">
            {$productDto->getProductName()}
        </h5>
        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <div class="product-description" itemprop="description">
                {$productDto->getDescription()}
            </div>
            {if $productDto->getPoidsAuChoix()!='Non' && !empty($productDto->getPoids())}
                <span itemprop="weight">
                    {$productDto->getPoids()}
                </span>
                <br />
            {/if}
        </div>
        <p itemtype="http://schema.org/Offer" itemscope="" itemprop="offers" class="our_price_display">
            <span itemprop="price">
                {$productDto->getPrice()}
            </span>
            <meta itemprop="priceCurrency" content="EUR" />
        </p>
        <p class="unit-price"><span id="unit_price_display">{$productDto->getUnitPrice()}</span> par {$productDto->getUnity()}</p>
    </div>
    <div class="desc">
        <div class="closeDiv"><a onclick="hiddenDiv();" title="Fermer la fiche"><img src="{$MPURI}/img/icon_del.gif" alt="Fermer la fiche" /></a></div>
        <div class="supplier">
            <img src="{$MPURI}/img/su/{$productDto->getIdSupplier()}-medium_default.jpg" />
            <p class="supplier-info">
                <span class="mespaysanscom">mespaysans.com</span> vous présente : <b>{$productDto->getSupplierName()}</b>
                <br />
                <a href="{$MPURI}/paysans/{$productDto->getIdSupplier()}-{MPTools::rewrite($productDto->getSupplierName())}/detail/">Découvrir ce paysans</a>
            </p>
        </div>
    </div>
</div>