
<?php 
include ("php_script.php");
include ("php_scripts/login.php");
include ("add_product_form.php"); 
include ("add_customer_form.php"); 
?>

<?php
$functions = new functions();
echo '
<!DOCTYPE html>
	<html>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore.js"></script>
		<script type="text/javascript" src="function.js"></script>
		<script type="text/javascript" src="./js/administration.js"></script>
                <link rel="stylesheet" href="./css/style.css">
	<head>
	<style>
	.product_count {
		width: 60px;
		height: 40px;
		padding: 1px;
		border: none;
		border: 1px solid #555;
		font-weight: bold;
	}
	

	</style>
	<script>
	function searchUserInAdminPage(){
		
      var value = document.getElementById("searchInAdminPage").value;
      $(".column").each(function() {
          if ($(this).text().toLowerCase().search(value.toLowerCase()) > -1) {
              $(this).show();
          }
          else {
              $(this).hide();
          }
      });
	}
	function checkInputForNumber(inputPayment,buttonId)
	{
		var input = document.getElementById(inputPayment);
		var payButton = document.getElementById(buttonId);
		//input.style.backgroundColor="#FFF";
		
		var x=input.value;
		
		if (!isInputedAmoundValid(x) && x !="")
		{
		    input.style.backgroundColor="#FF0000";
		}else{
			input.style.backgroundColor="#FFF";
		}
	}
	function updateUserAmound(userId, inputFieldId){

	  var input = document.getElementById(inputFieldId);
	  var amound = input.value;
	  if(isInputedAmoundValid(amound)){
	    $.ajax({
	      url: \'php_scripts/dbutility.php?id=\'+userId+\'&amound=\'+amound,
	      success: function(html) {
	                var obj = JSON.parse(html);
	                var idOfBalanceToBeUpdated = "accountBalance"+userId;
	                document.getElementById(idOfBalanceToBeUpdated).innerHTML = obj["newBalance"]+" €";
	                input.value = "";
	               }
	    });
	  }
	}

	function updateProductNumber(productId, inputFieldId){

	  var input = document.getElementById(inputFieldId);
	  var productNumber = input.value;
	  if (isInteger(productNumber)){
	    $.ajax({
	      url: \'php_scripts/dbutility.php?productId=\'+productId+\'&productNumber=\'+productNumber,
	      success: function(html) {
	                var obj = JSON.parse(html);
	                var idOfBalanceToBeUpdated = "numOfProduct"+productId;
	                var numberOfProduct = obj["newCount"];
	                document.getElementById(idOfBalanceToBeUpdated).innerHTML = numberOfProduct+" Stück";
	                input.value = "";
	               }
	    });
	  }
	}

function isInteger(x) {
   return x % 1 === 0;
}

function isInputedAmoundValid(value){
  var regex=/^\-?([1-9]\d*|0)(\.\d?[1-9])?$/;
  if(value=="" || !value.match(regex)) {
    return false;
  }
  else{
    return true;
  }
}
</script>
</head>
<body>

<div class="flex-container">
<header>
<div class="header_first_column">
  <div style="width: 50px; height: 50px; float: left"> FLEX KITCHEN</div>
</div>
<div class="header_second_column" style="background-color:yellow;color:black"><h4> Aministration Bereich</h4></div>
<div class="header_third_column">
  <form class="search_form">
	<div style="float:right"><img style=" width:50px; float:right;" src="img/adminLogginImg.png" href="#" onclick="closeAdminSite()"></img></div>
	<div style="float:right"><img style=" height:60px;float:right;" src="img/home.png" href="#" onclick="goToAdminHome()"></img></div>
	<div style="float:right"> <input type="text" class="search" id="searchInAdminPage" onkeyup="searchUserInAdminPage()" placeholder="Search for names.." title="Type in a name"></div>
  </form>
</div>
</header>
  
<div class="content" id="contentInAdminPage">
<menu id="admin_home_manu" style="padding-left:0px">';
$functions->getAdminPageContent();

echo '</menu>
</div>';
$functions->getFooter();

?>
