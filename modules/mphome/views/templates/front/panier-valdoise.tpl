{foreach $listPanierIdf as $productDto}
    <div id="panier{$productDto->getIdProduct()}-desc" class="panier-desc">
        <div class="desc">
            <a onclick="show('panier{$productDto->getIdProduct()}-desc')" class="close-panier"></a>
            <img class="photo" src="{$link->getImageLink($productDto->getLinkRewrite(), $productDto->getIdImage(), 'medium_default')|escape:'html':'UTF-8'}" alt="{$productDto->getProductName()|escape:'html':'UTF-8'}" title="{$productDto->getProductName()|escape:'html':'UTF-8'}" itemprop="image" />
            <div class="desc-panier">
                <p class="title"><strong>{$productDto->getProductName()} - {convertPrice price=$productDto->getTTCPrice()}</strong></p>
                <p>
                    {$productDto->getDescription()}
                </p>
                <p><a href="" class="addtocart">Ajouter à ma liste de course</a></p>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
{/foreach}
<div id="listepanierlegumes">
    {foreach $listPanierIdf as $productDto}
        <div id="panier{$productDto->getIdProduct()}" class="panier">
            <div class="image"><img src="{$link->getImageLink($productDto->getLinkRewrite(), $productDto->getIdImage(), 'medium_default')|escape:'html':'UTF-8'}" alt="{$productDto->getProductName()|escape:'html':'UTF-8'}" title="{$productDto->getProductName()|escape:'html':'UTF-8'}" itemprop="image" /></div>
            <div class="detail">{$productDto->getProductName()}<br />{convertPrice price=$productDto->getTTCPrice()}<br /><a onclick="show('panier{$productDto->getIdProduct()}-desc');">Découvrir le contenu</a></div>
        </div>
    {/foreach}
    <div class="clearfix"></div>
</div>
<script>
function show(id) {
  $( "#"+id ).toggle( "blind" );
}
</script>