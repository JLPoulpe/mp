<div id="listepaysans">
    {include file="$tpl_dir./top_hp.tpl"}
    <div class="content">
        {assign var=category value=""}
        {foreach $listPaysansByCategory as $row}
            {assign var=categoryDto value=$row.category}
            {assign var=listSupplier value=$row.suppliers}
            {if $category!=$categoryDto->getName()}
                {assign var=category value=$categoryDto->getName()}
                <h2>{$categoryDto->getName()}</h2>
            {/if}
            {foreach $listSupplier as $supplierDto}
                <img onclick="displaySupplierDetail('{$supplierDto->getIdSupplier()}')" class="supplierDetail" src="/themes/mespaysansV2/img/paysans/{$supplierDto->getIdSupplier()}.jpg" alt="{$supplierDto->getName()}" title="{$supplierDto->getName()}" />
                {if !$isMobile}
                    <div id="supplierdetail-{$supplierDto->getIdSupplier()}" class="supplier-detail" style="display:none;">
                        <div id="portrait" class="descriptif">
                            {$supplierDto->getDescription()}
                        </div>
                        <div class="clearfix"></div>
                        <div><a onclick="closeDiv('supplierdetail-{$supplierDto->getIdSupplier()}');closeDiv('layerOverlay');" class="closeDiv">Fermer la fenêtre</a></div>
                    </div>
                {/if}
            {/foreach}
        {/foreach}
    </div>
</div>
