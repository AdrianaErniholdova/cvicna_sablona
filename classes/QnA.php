<?php

namespace otazkyodpovede;
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/db/config.php');
use PDO;

class QnA {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $config = DATABASE;
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        try {
            $this->conn = new PDO(
                'mysql:host=' . $config['HOST'] . ';dbname=' . $config['DBNAME'] . ';port=' . $config['PORT'],
                $config['USER_NAME'],
                $config['PASSWORD'],
                $options
            );
        } catch (PDOException $e) {
            die("Chyba pripojenia: " . $e->getMessage());
        }
    }

    public function insertQnA() {
        try {
            // Načítanie JSON súboru
            $data = json_decode(file_get_contents
            (__ROOT__.'/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            // Vloženie otázok a odpovedí v rámci transakcie
            $this->conn->beginTransaction();
            $sqlInsert = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)"; //tu som pridala k sql Insert lebo inak mi nechcelo ukladat udaje do databazy
            $sqlCheck = "SELECT COUNT(*) FROM qna WHERE otazka = :otazka"; //prikaz na kontrolu ci uz otazka existuje

            $statementInsert = $this->conn->prepare($sqlInsert);
            $statementCheck = $this->conn->prepare($sqlCheck);

            for ($i = 0; $i < count($otazky); $i++) {
                //zistenie ci uz otazka a odpoved su v databaze
                $statementCheck->bindParam(':otazka', $otazky[$i]);
                $statementCheck->execute();
                $exists = $statementCheck->fetchColumn(); //zistuje pocet vyskytov otazky

                if ($exists == 0) { //ak neexistuje tak sa vlozi do databazy
                    $statementInsert->bindParam(':otazka', $otazky[$i]);
                    $statementInsert->bindParam(':odpoved', $odpovede[$i]);
                    $statementInsert->execute();
                }
            }
            $this->conn->commit();
            //echo "Dáta boli vložené"; //toto som zakomentovala aby sa to nevypisovalo na stranke
        } catch (Exception $e) {
            // Zobrazenie chybového hlásenia
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            $this->conn->rollback(); // Vrátenie späť zmien v prípade chyby
        }
    } //uzatvorenie spojenia co bolo vo finally tu nebude lebo este tie otazky z databazy treba vlozit do stranky

    public function vlozQnA() {
        try {
            //tymto ziskame otazky a odpovede z databazy
            $sql = "SELECT otazka, odpoved FROM qna";
            $statement = $this->conn->prepare($sql);
            $statement->execute();

            //dame ich do pola
            $qnaData = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $qnaData;
        } catch (Exception $e) {
            //vypiseme ak by nastala chyba
            echo "Chyba pri načítaní otázok a odpovedí: " . $e->getMessage();
            return [];
        }
    }
}
?>
