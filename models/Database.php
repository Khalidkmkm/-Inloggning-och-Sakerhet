<?php 
require_once('UserDatabase.php');
require_once('Database.php');
require_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');

$dotenv->load();

class Database {
public $pdo;

private $usersDatabase;

function getUsersDatabase() {
    return $this->usersDatabase;
}

function __construct() {
    $host = $_ENV['HOST'];
    $db   = $_ENV['DB'];
    $user = $_ENV['USER'];
    $pass = $_ENV['PASSWORD'];
    $port = $_ENV['PORT'];

    $dsn = "mysql:host=$host;port=$port;dbname=$db";

    $this->pdo = new PDO($dsn, $user, $pass);
    $this->initDatabase();
    $this->usersDatabase = new UserDatabase($this->pdo);
    $this->usersDatabase->setupUsers();
    $this->usersDatabase->seedUsers(); 
    
  }

  function initDatabase() {
    $this->pdo->query('CREATE TABLE IF NOT EXISTS UserDetails(
    id INT PRIMARY KEY,
    name VARCHAR(50),
    streetAddress VARCHAR(40),
    postalCode VARCHAR(20),
    city VARCHAR(100)
    )');
  }
  
  function addUserDetails($id, $name, $streetAddress, $postalCode, $city) {
    $query = $this->pdo->prepare('INSERT INTO UserDetails (id, name, streetAddress, postalCode, city) VALUES (:id, :name, :streetAddress, :postalCode, :city)');
    $query->execute([
        'id' => $id,
        "name" => $name,
        "streetAddress" => $streetAddress,
        "postalCode" => $postalCode,
        "city" => $city
    ]);
  }
}





?>