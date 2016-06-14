<div id="listmarkets" class="mpborder">
    {if $marketDto==null}
        <div class="error">Aucun marché trouvé</div>
    {else}
        {assign var=listProduit value=$marketDto->getListProduits()}
        {assign var=idCurrentMarket value=$marketDto->getIdMarket()}
        <div>
            <div class="marketname">{$marketDto->getName()} - {$marketDto->getCity()} ({$marketDto->getPostalCode()})</div>
            {include file="$tpl_dir./mp_detailMarket.tpl"}
            {assign var=supplierName value=''}
            {assign var=nbProduct value=0}
            <input type="hidden" name="market" value="{$marketDto->getIdMarket()}">
            <input type="hidden" name="datewithdrawal" value="{$datewithdrawal}">
            <div class="clearfix"></div>
            {foreach $listProduit as $productDto}
                {assign var=supplierDto value=$marketDto->getSupplier($productDto->getIdSupplier())}
                {if $supplierDto->isDispo()}
                    {if $productDto->getSupplierName()!=$supplierName}
                        {assign var=ownerproduct value=true}
                        {if $nbProduct!=0}
                                <div class="clearfix"></div>
                            </div>
                        {/if}
                        {assign var=supplierName value=$productDto->getSupplierName()}
                        <div id="{$productDto->getSupplierName(false)}" class="suppliername">
                            <a onclick="displaySupplierDetail('{$supplierDto->getIdSupplier()}')" class="pointer"><img class="bandeau_paysans" src="/img/paysans/bandeau/{$productDto->getIdSupplier()}.jpg" alt="{$productDto->getSupplierName()}" title="Producteur - {$productDto->getSupplierName()}" /></a>
                            {if $supplierDto->getWithdrawal()}<div class="withdrawal"><img src="/themes/mespaysans/img/tampon_pointretrait.png" alt="point-retrait" /></div>{/if}
                        </div>
                        {include file="$tpl_dir./mp_paysans.tpl"}
                        <div class="list-produits">
                            {if $productDto->getOwnerProduct()}
                                <div class="ownerproduct">Sa production</div>
                            {/if}
                    {/if}
                    {if !$productDto->getOwnerProduct() && $ownerproduct}
                        {assign var=ownerproduct value=false}
                        <div class="clearfix"></div>
                        <div class="ownerproduct">Des produits qu'il revend</div>
                    {/if}
                    <div class="bloc-produit">
                        {include file="$tpl_dir./mp_product.tpl"}
                        {if $productDto->getDescription()!=''}
                            {include file="$tpl_dir./mp_product-popup.tpl"}
                        {/if}
                    </div>
                    {if $productDto@last}
                            <div class="clearfix"></div>
                        </div>
                    {/if}
                    {assign var=nbProduct value=1}
                {/if}
            {/foreach}
            <div class="clearfix"></div>
        </div>
        {if isset($listOtherMarket)}
            <div class="autres-marches">
                <h3>Les autres marchés du jour</h3>
                {if $listOtherMarket|count==1}
                    Aucun autre marché ce jour
                {else}
                    {foreach $listOtherMarket as $otherMarketDto}
                        {if $otherMarketDto->getIdMarket()!=$idCurrentMarket}
                            <div class="vignette-marche">
                                <a href="/marches-locaux/{$otherMarketDto->getCityRewrite()}/{$otherMarketDto->getPostalCode()}/{$otherMarketDto->getIdMarket()}/{$otherMarketDto->getLinkRewrite()}/jour/{$jour}" class="marche">
                                    <div class="city">{$otherMarketDto->getCity()}</div>
                                    <div class="name">{$otherMarketDto->getName()}</div>
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                    <div class="clearfix"></div>
                {/if}
            </div>
        {/if}
    {/if}
</div>
<div id="layerOverlay" class="layer_overlay"></div>