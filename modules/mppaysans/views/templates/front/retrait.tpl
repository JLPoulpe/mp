<div id="espacepro" class="container mpborder">
    <div>
        {include file="$menu"}
    </div>
    <div>
        <h2>Mes retraits</h2>
        <fieldset class="legende"><legend>Comment ça marche</legend>
            Vous trouverez ci-dessous la liste des références des commandes qui vous ont été passées et que vous avez validées avant de les livrer.
            <br />
            Pour être payé, vous devez compléter ces références avec les données fournient pas le client.
        </fieldset>
        <form action="/index.php?controller=espace-pro&mod=valideref" method="post">
            {if $listRef|count>0}
                {foreach $listRef as $cartDto}
                    <p><label>{$cartDto->getOrderReference(1)}</label><input type="text" name="reference[]" value="" /></p>
                {/foreach}
                <input type="hidden" name="idCart" value="{$cartDto->getIdCart()}" />
                <input type="submit" value="Valider les références" />
            {else}
                Aucune commande à valider
            {/if}
        </form>
    </div>
</div>