
<?php 
include ("php_script.php");
include ("php_scripts/login.php");
?>

<?php
$functions = new functions();
$functions->getSiteHead();

echo '
	<head>
	<script>
	function checkAccessForCurrentUser() {
		if('.isAdmin().'){
			
		}else{
			alert("Access danied for this user! You are not admin");
			if('.hasSession().'){
				window.location.href = "articles.php";
			}else{
				window.location.href = "index.php";
			}
		}
	}
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
	function checkInputForNumber(buttonId)
	{
		var input = document.getElementById("payment_input");
		var payButton = document.getElementById(buttonId);
		input.style.backgroundColor="#FFF";
		payButton.disabled = false;
		var x=document.getElementById("payment_input").value;
		var regex=/^\-?([1-9]\d*|0)(\.\d?[1-9])?$/;
		if(x=="") return;
		if (!x.match(regex))
		{
			payButton.disabled = true;
		    input.style.backgroundColor="#FF0000";
		}
	}
</script>
</head>
<body onLoad="checkAccessForCurrentUser()">

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
<menu id="admin_home_manu">';
$functions->getAdminPageContent();

echo '</menu>
</div>';
$functions->getFooter();

?>
