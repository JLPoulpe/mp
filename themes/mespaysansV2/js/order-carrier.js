/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
$(document).ready(function(){

	if (!!$.prototype.fancybox)
		$("a.iframe").fancybox({
			'type': 'iframe',
			'width': 600,
			'height': 600
		});

	if (typeof cart_gift != 'undefined' && cart_gift && $('input#gift').is(':checked'))
		$('p#gift_div').show();

	$(document).on('change', 'input.delivery_option_radio', function(){
		var key = $(this).data('key');
		var id_address = parseInt($(this).data('id_address'));
		if (orderProcess == 'order' && key && id_address)
			updateExtraCarrier(key, id_address);
		else if(orderProcess == 'order-opc' && typeof updateCarrierSelectionAndGift !== 'undefined')
			updateCarrierSelectionAndGift();
	});

	$(document).on('submit', 'form[name=carrier_area]', function(){
		return acceptCGV();
	});
        
        //saveAddress();
});

function acceptCGV()
{
	if (typeof msg_order_carrier != 'undefined' && $('#cgv').length && !$('input#cgv:checked').length)
	{
		if (!!$.prototype.fancybox)
		    $.fancybox.open([
	        {
	            type: 'inline',
	            autoScale: true,
	            minHeight: 30,
	            content: '<p class="fancybox-error">' + msg_order_carrier + '</p>'
	        }],
			{
		        padding: 0
		    });
		else
		    alert(msg_order_carrier);
	}
	else
		return true;
	return false;
}

function addAddress() {
    var idFriendOrNeighbour = 20;
    var val = $("input[type=radio]:checked").val();
    var vir = val.indexOf(',');
    var id = val.substr(0, vir);
    if(parseInt(id)===idFriendOrNeighbour) {
        $("#address_friend").css({'top': '170px'}).fadeIn('fast');
        $("#address_friend").css("display", "block");
    }
    
    return false;
}

function checkForm() {
    var cgv = acceptCGV();
    if(cgv){
        var needAddress = $('#addaddress').val();
        if(needAddress==='') {
            return addAddress();
        }
    } else {
        return cgv;
    }
}

function saveAddress(){
    $('#address_friend input[type=button]').click(function() {
        var firstname = $('#firstname').val();
        var name = $('#name').val();
        var tel = $('#tel').val();
        var mobile = $('#mobile').val();
        var address = $('#address').val();
        var address2 = $('#address2').val();
        var cp = $('#cp').val();
        var city = $('#city').val();
        var infos = $('#infos').val();
        var addressName = $('#addressName').val();
        
        var err = false;
        if(firstname==='') {
            $('#firstname').css('border-color', 'red');
            err = true;
        }
        if(name==='') {
            $('#name').css('border-color', 'red');
            err = true;
        }
        if(tel==='') {
            $('#tel').css('border-color', 'red');
            err = true;
        }
        if(mobile==='') {
            $('#mobile').css('border-color', 'red');
            err = true;
        }
        if(address==='') {
            $('#address').css('border-color', 'red');
            err = true;
        }
        if(cp==='') {
            $('#cp').css('border-color', 'red');
            err = true;
        }
        if(city==='') {
            $('#city').css('border-color', 'red');
            err = true;
        }
        if(addressName==='') {
            $('#addressName').css('border-color', 'red');
            err = true;
        }
        if(!err) {
            sendMPAjax('addAddress', 'customerId='+$('#customerID').val()+'&firstname='+firstname+'&name='+name+'&tel='+tel+'&mobile='+mobile+'&address='+address+'&address2='+address2+'&cp='+cp+'&city='+city+'&infos='+infos+'&addressName='+addressName, function(result){
                if(result) {
                    $('#addaddress').val(1);
                    $('#form').submit();
                }
            });
        }
    });
}