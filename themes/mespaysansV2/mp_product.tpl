<div id="product-{$productDto->getIdProduct()}" class="produit" itemscope itemtype="http://schema.org/Product">
    <div class="img-product">
        {if $productDto->getDescription()!='' && !$isMobile}
            <a onclick="displayProductDetail('{$productDto->getIdProduct()}')" class="product-description"><span></span><img class="photo" src="{$link->getImageLink($productDto->getLinkRewrite(), $productDto->getIdImage(), 'panier')|escape:'html':'UTF-8'}" alt="{$productDto->getProductName()|escape:'html':'UTF-8'}" title="{$productDto->getProductName()|escape:'html':'UTF-8'}" itemprop="image" /></a>
        {else}
            <img class="photo" src="{$link->getImageLink($productDto->getLinkRewrite(), $productDto->getIdImage(), 'panier')|escape:'html':'UTF-8'}" alt="{$productDto->getProductName()|escape:'html':'UTF-8'}" title="{$productDto->getProductName()|escape:'html':'UTF-8'}" itemprop="image" />
        {/if}
    </div>
    {assign var=productName value=$productDto->getProductName()}
    <div itemprop="name" class="product-name">
        {if $productName|count_characters>=16}<span>{/if}{$productDto->getProductName()|truncate:54:'...'|escape:'html':'UTF-8'}{if $productName|count_characters>=16}</span>{/if}
    </div>
    {if !$isMobile}
        <div class="informations" itemprop="description">
            {assign var=description value=$productDto->getDescription()}
            {assign var=descriptionShort value=$productDto->getDescriptionShort()}
            {if !empty($description) || !empty($descriptionShort)}
                <a onclick="displayProductDetail({$productDto->getIdProduct()});">Plus de détails</a>
                <div id="productdetail-{$productDto->getIdProduct()}" class="details">
                    <div class="name">{$productDto->getProductName()}</div>
                    {if empty($description)}
                        {$descriptionShort}
                    {else}
                        {$description}
                    {/if}
                    <div><a onclick="closeDiv('productdetail-{$productDto->getIdProduct()}');closeDiv('layerOverlay');" class="closeDiv">Fermer la fenêtre</a></div>
                </div>
            {/if}
        </div>
    {/if}
    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        {assign var=pac value=$productDto->getPoidsAuChoix()}
        {assign var=poid value=$productDto->getPoids()}
        <div itemprop="price">
            {assign var=unitPriceRation value=$productDto->getUnitPriceRatio()}
            <div class="prix">Prix : {if empty($unitPriceRation)}{convertPrice price=$productDto->getTTCPrice()} € {if empty($poid)} l'unité{/if}{else}{convertPrice price=$productDto->getTTCPrice()} / {$productDto->getUnity()}{/if}</div>
        </div>
        <meta itemprop="priceCurrency" content="{$currency->iso_code}" />
    </div>
    <div class="button-container">
        Quantité : 
        {assign var=idCombination value=0}
        {assign var=attributes value=$productDto->getListAttribute()}
        {if !empty($attributes)}
            <select name="{$productDto->getIdProduct()}" class="attribsselect">
                {foreach $attributes as $attribute}
                    {if $attribute@first}
                        {assign var=idCombination value=$attribute.id_product_attribute}
                    {/if}
                    <option value="{$attribute.id_product_attribute}" title="{$attribute.name}">{$attribute.name|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        {else}
            <select class="attribsquantity" name="{$productDto->getIdProduct()}">
                {for $foo=1 to 10}
                    <option value="{$foo}">{$foo}</option>
                {/for}
            </select>
        {/if}
        <p class="hidden">
            <input type="hidden" value="{$productDto->getIdProduct()}" name="idproduct" />
            <input type="hidden" value="{$idCombination}" name="id_product_attribute{$productDto->getIdProduct()}" />
            <input type="hidden" value="1" name="quantity{$productDto->getIdProduct()}" />
            <input type="hidden" value="{$static_token}" name="token{$productDto->getIdProduct()}" />
            <input type="hidden" value="{$supplierDto->getIdSupplier()}" name="supplier{$productDto->getIdProduct()}" />
        </p>
        <br />
        {assign var=contentUrl value='add=1&amp;id_product='|cat:$productDto->getIdProduct()|cat:'&token='|cat:$static_token}
        <a id="submit{$productDto->getIdProduct()}" class="ajax_add_to_cart_button bouton" href="{$link->getPageLink('cart',false, NULL, $contentUrl, false)|escape:'html':'UTF-8'}" rel="nofollow" title="Ajouter au panier" data-id-product="{$productDto->getIdProduct()|intval}">
            Je vais en prendre
        </a>
    </div>
</div>