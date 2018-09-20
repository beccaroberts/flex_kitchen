<?php

$allCustomersAsJSON = getAllPersons();
$allCustomers = json_decode($allCustomersAsJSON);


foreach($allCustomers as $customer){
	$lastMonth= getMonthInGermany(date("M", strtotime("last month")));
    $purchasedArticlesJSON =  getAllPurchasedArticlesForCustomer($customer->id);
    if($purchasedArticlesJSON == "-1") continue;

    $purchasedArticles = json_decode($purchasedArticlesJSON);
    $tr='';
    $totalAmound = 0;

    foreach($purchasedArticles as $purchasedArticle){

    	$articleObject = getArticle($purchasedArticle->article_id);
    	$articleName = $articleObject["name"];
    	$articlePrice = $articleObject["price"];

    	$numberOfPurchasedArticle = $purchasedArticle->sum;

    	$price = $numberOfPurchasedArticle * $articlePrice;
    	$tr= $tr.' <tr style="background-color: #e0e0e0;height:20">
                <td>'.$articleName.'</td><td>'.$numberOfPurchasedArticle.'</td><td>'.$price.' €</td>
            </tr>';
            $totalAmound = $totalAmound + $price;
    }
    $accountBalance = $customer->account_balance;
    $customerMsg = '<p>Bitte bezahle den Betrag vom <strong><span style="background-color:red">'.$accountBalance.' €</span></strong> bei zuständigen Person!</p>';
    if($customer->account_balance >= 0){
    	$customerMsg = '<p>Du hast noch einen Guthaben vom <strong><span style="background-color:#00800075">'.$accountBalance.' € </span></strong> daher musst Du nix bezahlen!</p>';
    }
     $htmlContent = '
    <html>
    <head>
        <title>Welcome to CodexWorld</title>
    </head>
  <body>
      <div>
        <p>Hallo '.$customer->firstname.',</p>
		<p>Im folgenden erhaltest Du eine Getränkeliste und Abrechnung für den Monat <strong>'.$lastMonth.' '.date('Y').'.</strong></p>
      </div>
      <div>
       <table style="width: 100%; max-width:800px;border: 1px slid #FB4314; padding-right:10px;">
            <tr style="background-color: #808080;height:20">
                <th>Getränk</th> <th>Anzahl</th> <th>Preis</th>
           '.$tr.'
           <tr style="border:2;height:20">
        <td></td><td></td><td>Gesamtbetrag: <strong>'.$totalAmound.' € </strong></td>
       </tr>
       </table>
      </div>
    <table>
      '.$customerMsg.'
      <p>Dies ist eine automatisch erstellte E-Mail. Bitte ANTWORTE NICHT auf diese Mail</p>
    </table>
    <br/>
    <p>Viele Grüße </p>
    <p>Flex Kitchen </p>
    </div>
    </body>
    </html>';
    echo $htmlContent;
     //sendEmailToCustomer($customer->email, $htmlContent);
   //sendEmailToCustomer("kamuran1905@yahoo.de", $htmlContent);//REMOVE THIS ONLY TEST

}

function sendEmailToCustomer($to, $htmlContent){
    // Set content-type header for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // Additional headers
    $headers .= 'From: FlexKitchen<flexkitchen@example.com>' . "\r\n";
    $lastMonth= getMonthInGermany(date("M", strtotime("last month")));
    $subject = "Die Getränkeabrechnung ".$lastMonth." ".date('Y');

    // Send email
    if(mail($to,$subject,$htmlContent,$headers)){
        echo 'Email has sent successfully.';
    }else{
        echo 'Email sending fail.';
    }

}
function getAllPersons(){
    require_once("dbFetchDataFromDB.php");
    $fetchDataFromDB = new fetchDataFromDB();
    $result = $fetchDataFromDB->getAllPersonsFromDB();
    return $result;
}
function getAllPurchasedArticlesForCustomer($personId){
  require_once("dbFetchDataFromDB.php");
  $fetchDataFromDB = new fetchDataFromDB();
  $result = $fetchDataFromDB->getAllPurchasedArticlesForPersonFromDB($personId);
  return $result;
}
function getArticle($articleId){
	require_once("dbFetchDataFromDB.php");
    $fetchDataFromDB = new fetchDataFromDB();
    $article = $fetchDataFromDB->getArticleFromDB($articleId);
    return $article;
}
function getMonthInGermany($month){
	$months=array(
		"January"=>"Januar",
		"February"=>"Februar",
		"March"=>"März",
		"April"=>"April",
		"May"=>"Mai",
		"June"=>"Juni",
		"July"=>"Juli",
		"August"=>"August",
		"September"=>"September",
		"October"=>"Oktober",
		"November"=>"November",
		"December"=>"Dezember"
	);
	return $months[$month];
}
?>