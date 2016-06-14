<div>
    {foreach $listMarket as $market}
        <div>
            <div>{$market->getName()}</div>
            {foreach $market->getListProduits() as $key=>$listeProduits}
                <div>
                    Producteurs : {$key}
                {foreach $listeProduits as $productDto}
                    <div>
                        Nom : {$productDto->getProductName()}
                    </div>
                {/foreach}
                </div>
            {/foreach}
        </div>
    {/foreach}
</div>