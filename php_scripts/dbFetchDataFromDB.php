<?php
/**
 *
 * @author Kamuran Dogan
 */
class fetchDataFromDB {

	public function __construct(){
    }

	function getDBConnection(){
		require_once("dbConnector.php");
		$db = new dbConnector();
		$conn = $db->getDBConnection();
		return $conn;
	}

	public function getLastPurchases($personId){

		$conn = $this->getDBConnection();
    	$sql = 'SELECT a.name as name, pam.person_id as person_id, pam.buy_date as buy_date 
    			FROM article a, person_article_matrix pam 
    			WHERE a.id = pam.article_id AND pam.person_id = '.$personId.' ORDER BY pam.id DESC LIMIT 1';

    	$result = $conn->query($sql);
		$response['name'] = "Noch nix gekauft";
    	$response['id'] = -1;
		$response['person_id'] = -1;
		$response['buy_date'] = "00.00.00.00";

	    if ($result->num_rows > 0) {
	        while($row = $result->fetch_assoc()) {
	            $response['name'] = $row["name"];
	            $response['person_id'] = $row["person_id"];
				$response['buy_date'] = $row["buy_date"];
	        }
	    }else{
	    	error_log("[dbFechDataFromDB -> getLastPurchases] no last purchases article found");
	    }
	    $conn->close();
	    return $response;
	}

	public function getAllPurchasedArticlesForPersonFromDB($personId){

		$conn = $this->getDBConnection();
    	$sql = 'SELECT article_id, sum(count) as sum
    			FROM person_article_matrix
    			WHERE person_id = '.$personId.' && month(buy_date) = month(now())-1 && year(buy_date) = year(now()) GROUP BY article_id';
    	$result = $conn->query($sql);
    	$purchasedArticlesByArticleId;

	    if ($result->num_rows > 0) {
	        while($row = $result->fetch_assoc()) {
	            $response['article_id'] = $row["article_id"];
	            $response['sum'] = $row["sum"];
	            $purchasedArticlesByArticleId[$row["article_id"]] = $response;
	        }
	    }else{
	    	error_log("[dbFechDataFromDB -> getAllPurchasedArticlesForPersonFromDB] no purchases articles found");
	    	return -1;
	    }
	    $conn->close();
	    return json_encode($purchasedArticlesByArticleId);
	}

	public function getAllPersonsFromDB(){

		$conn = $this->getDBConnection();

		$sql = 'SELECT * FROM person WHERE is_admin="0" AND is_active="1"';
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$counter = 0;
		    $customers;
		    while($row = $result->fetch_assoc()) {
		        $response['id'] = $row["id"];
		        $response['firstname'] = $row["firstname"];
		        $response['lastname'] =$row["lastname"];
		        $response['email'] = $row["email"];
		        $response['img_path'] = $row["img_path"];
		        $response['account_balance'] = $row["account_balance"];

		        $customers[$counter] = $response;
		        $counter++;
		    }
		    $conn->close();
		    return json_encode($customers);
		} else {
			error_log("[dbFechDataFromDB -> getAllPersonsFromDB] no person found from db");
		    return json_encode("-1");
		}
	}

	function getPersonById($personId){
		$conn = $this->getDBConnection();

		$sql = 'SELECT * FROM person WHERE id ='.$personId.' AND is_admin="0" AND is_active=1';
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
		    $person;
		    while($row = $result->fetch_assoc()) {
		        $person['id'] = $row["id"];
		        $person['firstname'] = $row["firstname"];
		        $person['lastname'] =$row["lastname"];
		        $person['email'] = $row["email"];
		        $person['img_path'] = $row["img_path"];
		        $person['account_balance'] = $row["account_balance"];
		    }
		    $conn->close();
		    return json_encode($person);
		} else {
			error_log("[dbFechDataFromDB -> getPersonById] no person found for id ".$personId);
		    return json_encode("-1");
		}
	}

	function getArticleById($articleId){
		$conn = $this->getDBConnection();

		$sql = 'SELECT * FROM article WHERE id ='.$articleId;
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
		    $person;
		    while($row = $result->fetch_assoc()) {
		        $person['id'] = $row["id"];
		        $person['name'] = $row["name"];
		        $person['price'] =$row["price"];
		        $person['count'] = $row["count"];
		        $person['category'] = $row["category"];
		        $person['img_path'] = $row["img_path"];
		    }
		    $conn->close();
		    return json_encode($person);
		} else {
			error_log("[dbFechDataFromDB -> getArticleById] no article found for id ".$articleId);
		    return json_encode("-1");
		}
	}

	function getArticleFromDB($productId){
		$conn = $this->getDBConnection();

		$sql = 'SELECT * FROM article WHERE id ='.$productId;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		        $article['id'] = $row["id"];
		        $article['name'] = $row["name"];
		        $article['price'] = $row["price"];
		        $article['count'] = $row["count"];
		        $article['category'] = $row["category"];
		        $article['img_path'] = $row["img_path"];
		    }
		    return $article;
		} else {
			error_log("[dbFechDataFromDB -> getArticleFromDB] no article found for id ".$productId);
		    return "-1";
		}
		$conn->close();
	}

	public function getAllFromTable($tableName){
        $conn = $this->getDBConnection();
        if (!$conn) {
            die('database connectin fails: ' . mysql_error());
        }
        $sql = "SELECT DISTINCT * FROM ".$tableName;
        $result = $conn->query($sql);
        $conn->close();
        return $result;
	}
	public function getAllPersonFromDB(){
		$conn = $this->getDBConnection();
        if (!$conn) {
            die('database connectin fails: ' . mysql_error());
        }
        $sql = "SELECT DISTINCT * FROM person WHERE is_active = 1";
        $result = $conn->query($sql);
        $conn->close();
        return $result;
	}
	
	public function getAccountBalanceOfCurrentUser(){
		require_once("login.php");

		$conn = $this->getDBConnection();
		$userId = getSessionUserId();
		$person = json_decode($this->getPersonById($userId));
		$account_balance = "account_balance";
		$oldAccountBalance = floatval($person->$account_balance);
		$conn->close(); 
		return $oldAccountBalance;
	}

	public function updateAccountBalanceOfUser ($userId, $amound){
		$conn = $this->getDBConnection();
		$person = json_decode($this->getPersonById($userId));
		$account_balance = 'account_balance';
		$oldAccountBalance = floatval($person->$account_balance);
        $newAccountBalance = $oldAccountBalance + floatval($amound);
        $response = "";
        $sql = 'UPDATE person SET account_balance = '.$newAccountBalance.' WHERE id ='.$userId;
    
        if ($conn->query($sql) === FALSE) {
            $response = "Error updating record: " . $conn->error;
        }else{
            $response = array('newBalance'=>(string)$newAccountBalance);
        }
        return $response;
        $conn->close(); 
	}

	public function updateProductNumber($productId, $productNumber){
		$conn = $this->getDBConnection();

		$article = json_decode($this->getArticleById($productId));
		$count = 'count';
        $oldAccountBalance = floatval($article->$count);
        $newAccountBalance = $oldAccountBalance + $productNumber;
        $response = "";
        $sql = 'UPDATE article SET count = '.$newAccountBalance.' WHERE id ='.$productId;
    
        if ($conn->query($sql) === FALSE) {
            $response = "Error updating record: " . $conn->error;
        }else{
            $response = array('newCount'=>(string)$newAccountBalance);
        }
        $conn->close();
        return $response;
	}

	function getCategoriesFromDB(){
		$conn = $this->getDBConnection();

        $sql = 'SELECT * FROM category';
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $response[$row["id"]] = $row["name"];
            }
            return $response;
        } else {
        	error_log("[dbFechDataFromDB -> getCategoriesFromDB] no categories found ");
            return "-1";
        }
        $conn->close(); 
	}
	
	public function insertUser($firstname, $lastname, $email, $telefon, $img_path){
		$conn = $this->getDBConnection();
        $imaga_name = $img_path;
		$response = "not inserted";
        $sql = 'INSERT INTO person (firstname, lastname, email, tel_no, img_path, account_balance, is_admin, user_pw, is_active) 
				VALUES ("'.$firstname.'","'.$lastname.'","'.$email.'", "'.$telefon.'","'.$imaga_name.'", 0, 0,"cfcd208495d565ef66e7dff9f98764da",1)';
    
        $result = $conn->query($sql);
        if($result){
            $response = "Records added successfully.";
        } else{
            $response = "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
        }
        $conn->close();
        return $response;
	}
	
	public function insertArticle($articleName, $category, $price, $filename){
		$conn = $this->getDBConnection();
		
		$response = "not inserted";
		$sql = 'INSERT INTO article (name, price, count, category, img_path) VALUES ("'.$articleName.'",'.$price.',0,'.$category.',"'.$filename.'")';
    
        $result = $conn->query($sql);
        if($result){
            $response = "Records added successfully.";
        } else{
            $response = "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
        }
        $conn->close();
        return $response;
	}

	public function setUserInActive($personId){
        $conn = $this->getDBConnection();
        $response = 1;
        $sql = 'UPDATE person SET is_active = 0 WHERE id ='.$personId;
    
        if ($conn->query($sql) === FALSE) {
        	error_log("[dbFechDataFromDB -> setUserInActive] can not set person inactive");
            $response = 0;
        }
        $conn->close(); 
        return $response;
    }
}
