<div id="account-menu">
    <a href="/index.php?controller=espace-pro"{if $method=='commande' || $method=='validate'}class="selected"{/if}>Mes commandes en cours</a>
    <a href="/index.php?controller=espace-pro&mod=retrait"{if $method=='retrait' || $method=='valideref'}class="selected"{/if}>Mes retraits</a>
    <a href="/index.php?controller=espace-pro&mod=history"{if $method=='history'}class="selected"{/if}>Mon historique de commande</a>
    <a href="/index.php?controller=espace-pro&mod=banque"{if $method=='banque' || $method=='validebanque'}class="selected"{/if}>Mes coordonnées bancaires</a>
</div>