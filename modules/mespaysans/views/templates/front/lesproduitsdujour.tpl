<div id="listProducts">
    {if $isBio}
        <div id="bio" class="active"><div class="content">Afficher tous les produits</div></div>
    {else}
        <div id="bio" class="inactive"><div class="content">N'afficher que les produits BIO</div></div>
    {/if}
    <div class="listCategory">
        <input type="hidden" name="datewithdrawal" value="{$datewithdrawal}">
        <input type="hidden" name="day" value="{$day}">
        <input type="hidden" name="deliveryDep" value="{$deliveryDep}">
        <input type="hidden" name="categoryId" value="{$categoryId}">
        {foreach $listCategory as $categoryDto}
            {if ($isBio && $categoryDto->getIsBio()) || !$isBio}
                <a href="/les-produits-du-{$day}/categorie/{$categoryDto->getIdCategory()}/{$categoryDto->getLinkRewrite()}/" title="{$categoryDto->getName()}" class="linkToCat"><img src="/themes/mespaysansV2/img/categorie/{$categoryDto->getIdCategory()}.jpg" alt="{$categoryDto->getName()}" id="{$categoryDto->getIdCategory()}" class="{if $categoryId==$categoryDto->getIdCategory()}currentId {/if}category" {if $categoryDto->getIsBio()}name="bio"{/if} /></a>
            {/if}
        {/foreach}
        <a href="/les-paniers-du-{$day}/" title="Les paniers composés" class="linkToCat"><img src="/themes/mespaysansV2/img/categorie/37.jpg" alt="Les paniers composés" /></a>
    </div>
    <div class="listSupplier">
        {foreach $listProductsBySupplier as $listProductsWithSupplier}
            {assign var=supplierDto value=$listProductsWithSupplier.supplierDto}
            {assign var=listProducts value=$listProductsWithSupplier.listProducts}
            {assign var=ownerproduct value=false}
            <div class="supplierProducts" {if $isBio && !$supplierDto->getIsBio()}hidden="hidden"{/if}>
                <div id="{$supplierDto->getLinkRewrite()}" class="suppliername">
                    <img onclick="displaySupplierDetail('{$supplierDto->getIdSupplier()}')" class="bandeau_paysans" src="/themes/mespaysansV2/img/paysans/{$supplierDto->getIdSupplier()}.jpg" alt="{$supplierDto->getName()}" title="Producteur - {$supplierDto->getName()}" />
                </div>
                {if !$isMobile}
                    <div id="supplierdetail-{$supplierDto->getIdSupplier()}" class="supplier-detail" style="display:none;">
                        <div id="portrait" class="descriptif">
                            {$supplierDto->getDescription()}
                        </div>
                        <div class="clearfix"></div>
                        <div><a onclick="closeDiv('supplierdetail-{$supplierDto->getIdSupplier()}');closeDiv('layerOverlay');" class="closeDiv">Fermer la fenêtre</a></div>
                    </div>
                {/if}
                {foreach $listProducts as $productDto}
                    {if $productDto->getOwnerProduct() && !$ownerproduct}
                        <div class="ownerproduct">Sa production</div>
                        {assign var=ownerproduct value=true}
                    {/if}
                    {if !$productDto->getOwnerProduct() && ($ownerproduct || $productDto@first)}
                        {assign var=ownerproduct value=false}
                        <div class="clearfix"></div>
                        <div class="ownerproduct">Des produits qu'il revend</div>
                    {/if}
                    <div class="bloc-produit">
                        {include file="$tpl_dir./mp_product.tpl"}
                    </div>
                {/foreach}
            </div>
            <div class="clearfix"></div>
        {/foreach}
    </div>
</div>
<div id="layerOverlay" class="layer_overlay"></div>