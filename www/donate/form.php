<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Donations</title>
<link href="Spry/SprySlidingPanels.css" rel="stylesheet" type="text/css" />
<script src="Spry/SprySlidingPanels.js" type="text/javascript"></script>
<script src="Spry/SpryEffects.js" type="text/javascript"></script>
<script src="Spry/SpryUtils.js" type="text/javascript"></script>
<script src="Spry/xpath.js" type="text/javascript"></script>
<script src="Spry/SpryData.js" type="text/javascript"></script>
<style type="text/css">
	body
	{
		background:#000000;
		font-family:Arial, Helvetica, sans-serif;
		background-image: url(page-bg.jpg);
		background-repeat: repeat-x;
	}
	
	/*select
	{
		background:url(/parchment-light2.jpg) repeat;
		color:#FFFFFF;
		border:#000000 solid 1px;
	}
	
	legend
	{
		font-weight:bold;
		color:#FFFFFF;
	}*/
	
	.SlidingPanelsContentGroup
	{
		float:left;
		width:850px;
		background:url(/parchment-light2.jpg) repeat;
	}
	.SlidingPanels
	{
		float:left;
		width:204px;
		height:204px;
		border:#000000 1px inset;
	}
	.SlidingPanelsContent
	{
		float:left;
		width:200px;
		height:200px;
	}
	fieldset
	{
		border:#FF9900 solid 1px;
		color:#FFFFFF;
	}
	#apDiv1
	{
		position:inherit;
		left:118px;
		top:210px;
		width:492px;
		height:216px;
		z-index:1;
	}
	#apDiv2
	{
		position:absolute;
		left:319px;
		top:91px;
		width:700px;
		height:500px;
		z-index:2;
	}
	#apDiv3
	{
		position:static;
		left:667px;
		top:164px;
		width:705px;
		height:507px;
		z-index:2;
		background-image: url(bgdonate.gif);
	}
</style>
</head>
<body>
<center>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<div id="apDiv3">
  <p>&nbsp;</p>
    <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p><br />

  <table width="525" border="0" cellspacing="5px" style="text-align:left;">
    <tr valign="top">
      <td>Select Realm:</td>
      <td><select id="realm" name="realm" style="width:150px;" onchange="getRewards(this.value);">
        <option>Your browser does not support this page.</option>
      </select></td>
      <td rowspan="5"><div id="description" class="SlidingPanels">
          <div class="SlidingPanelsContentGroup"> <?php echo $DESCRIPTIONS; ?> </div>
      </div></td>
    </tr>
    <tr valign="top">
      <td>Select Reward:</td>
      <td><select id="reward" name="reward" style="width:150px;" onchange="getPrice(this.value);">
        <option>Your browser does not support this page.</option>
      </select></td>
    </tr>
    <tr valign="top">
      <td>Donation:</td>
      <td><?php echo CURRENCY_CHAR; ?><span id="price"></span></td>
    </tr>
    <tr valign="top">
      <td>Character:</td>
      <td><input type="text" onblur="checkChar(this.value);" name="character" id="character" maxlength="16" style="width:150px;" />
          <img id="status" src="/notok.png" style="margin-left:5px; vertical-align:middle;" alt="Status" /></td>
    </tr>
    <tr valign="top">
      <td><form onsubmit="return prepForm();" action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="post" target="paypal">
          <!--
          Don't bother trying to change anything here,
          you won't get your reward.
          -->
          <input type="hidden" name="add" value="1" />
          <input type="hidden" name="cmd" value="_cart" />
          <input type="hidden" name="notify_url" value="<?php echo SITE_URL.SYS_PATH?>/postback.php" />
          <input type="hidden" id="item_name" name="item_name" value="" />
          <input type="hidden" id="amount" name="amount" value="" />
          <input type="hidden" id="item_number" name="item_number" value="" />
          <input type="hidden" name="quantity" value="1" />
          <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL; ?>" />
          <input type="hidden" name="currency_code" value="<?php echo CURRENCY_CODE; ?>" />
          <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_cart_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
      	  <!--
          Donation system by 1337D00D
          -->
      </form></td>
      <td></td>
    </tr>
  </table>

</div></center>
<script type="text/javascript">

	Description = new Spry.Widget.SlidingPanels("description",{transition:Spry.circleTransition});

	var Realms = <?php echo $REALMS; ?>;
	var Rewards = <?php echo $REWARDS; ?>;
	
	var Status = document.getElementById("status");
	var Realm = document.getElementById("realm");
	var Reward = document.getElementById("reward");
	var Price = document.getElementById("price");
	var Character = document.getElementById("character");
	var CharId = "0";
	
	function setupRealmlist()
	{
		var val;
		Realm.options.length = 0;
		for(val in Realms)
		{
			Realm.options[val] = new Option(Realms[val],val);
		}
	}
	
	function getRewards(realm)
	{
		var val;
		var i = 0;
		Reward.options.length = 0;
		for(val in Rewards)
		{
			if(Rewards[val].realm == realm)
			{
				Reward.options[i] = new Option(Rewards[val].name,val);
				i++;
			}
		}
		getPrice(Reward.value);
		checkChar(Character.value);
	}
	
	function getPrice(id)
	{
		if(Rewards[id].price < 0)
		{
		    Rewards[id].price = 10;
			Price.innerHTML = 10;
		}
		Price.innerHTML = Rewards[id].price;
		
		Description.showPanel(parseInt(id));
	}
	
	function checkChar(name)
	{
		CharId = "0";
		Status.src = "loading.gif";
		Spry.Utils.loadURL("GET","index.php?char="+name+"&realm="+Realm.value,true,function(r)
		{
			var res = r.xhRequest.responseText;
			if(res == "0")
			{
				document.getElementById("status").src = "notok.png";
				CharId = "0";
			}
			else
			{
				CharId = res;
				document.getElementById("status").src = "ok.png";
			}
		}
		);
	}
	
	function prepForm()
	{
		document.getElementById("item_name").value = Rewards[Reward.value].name;
		document.getElementById("amount").value = Rewards[Reward.value].price;
		document.getElementById("item_number").value = (parseInt(Realm.value)+1) +"-"+(parseInt(Reward.value)+1) +"-"+CharId;
		if(CharId == "0")
		{
			return confirm("That character does not exist.\nIf you continue you might not receive your reward.");
		}
		if(isNaN(CharId))
		{
			alert("There is a problem with the donation system.\nIt may not have been properly configured.\nPlease contact the administrator.");
			return false;
		}
		return true;
	}
	setupRealmlist();
	getRewards(0);
	
</script>
</body>

</html>
