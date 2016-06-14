<div id="vospaysans" class="mpborder">
    <h3>Vos paysans</h3>
    {assign var=category value=''}
    <dl id="paysans">
    {foreach $listSupplierWithDate as $cat=>$arraySupplier}
        {if $category!=$cat}
            <dt class="supplierCatregory">{$cat}</dt>
            {assign var=category value=$cat}
        {/if}
        {foreach $arraySupplier as $list}
            <dd>
                <a class="detail_paysans" onclick="loadSupplierDetail({$list.supplierDto->getIdSupplier()})"><img src="/img/paysans/bandeau/{$list.supplierDto->getIdSupplier()}.jpg"></a>
                 <div class="detail" id="idSupplier_{$list.supplierDto->getIdSupplier()}">
                     {$list.supplierDto->getDescription()}
                 </div>
            </dd>
        {/foreach}
    {/foreach}
    </dl>
</div>