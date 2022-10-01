<?
class Db {
        private $conn;
        function __construct($servername, $username, $password, $db) {
            $conn = new mysqli($servername, $username, $password,$db);
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }
            $this->conn = $conn;
        }

        function getLog($person, $price){
            $sql = "SELECT
                CONCAT(MONTH(`date`), '/',YEAR(`date`)) `Month`, 
                SUM(gallons) `Gas`,
                CONCAT('$', format(SUM(gallons) * ?,2)) `price`,
                CONCAT('$', format(SUM(total),2)) `actualprice`,
	            UNIX_TIMESTAMP(`date`) `sort`
            FROM gas
            WHERE person = ? OR ? = 'all'
            GROUP BY YEAR(`date`), MONTH(`date`)";
            $stmt  = $this->conn->prepare($sql);            
            $stmt ->bind_param("sss", $price, $person, $person);
            $stmt->execute();
            $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); // fetch an array of rows
            return $users;

        }     
        
        function newLog($when, $who, $much, $ppg){
            $gallons = $much / $ppg;
            error_log(print_r([$when, $who, $much, $ppg, $gallons], true), 0);
            $stmt = $this->conn->prepare("INSERT INTO gas (`date`, total, ppg, gallons, person) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $when, $much, $ppg, $gallons, $who);
            $stmt->execute();
            $stmt->close();            
            return true;
        }
    }
?>