<form action="" method="post">
    <label>Numéro de commande :</label>&nbsp;<input type="text" name="idorder" style="width:60px;display:inline;" />&nbsp;<input type="submit" value="Je veux les produits !!" />
</form>
<br /><br />
{if $linkToOrder}
    <div style="width:50%;margin:auto;">
        <span style="font-weight: bold;">Produits récupérés !</span><br /><br />
        <a href="/admin1121/index.php?controller=AdminOrders&id_order={$idOrder}&vieworder&token=fd08dc3cef5c26072bb18776a410fb26">Aller à la commande {$idOrder}</a>
    </div>
{/if}