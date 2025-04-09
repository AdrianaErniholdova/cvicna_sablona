<?php
namespace otazkyodpovede;

error_reporting(E_ALL);
ini_set('display_errors', "ON");

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__ . '/classes/Database.php');

use Database;
use PDO;
use Exception;

class QnA extends Database
{
    protected $connection;

    public function __construct()
    {
        $this->connect();
        $this->connection = $this->getConnection();
    }

    public function getQnA()
    {
        $sql = "SELECT * FROM qna";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            echo '<section class="container">';
            foreach ($data as $row) {
                echo '<div class="accordion">
                         <div class="question">' . $row["otazka"] . '</div>
                         <div class="answer">' . $row["odpoved"] . '</div>
                      </div>';
            }
            echo '</section>';
        } else {
            echo "Neboli nájdené žiadne otázky a odpovede.";
        }
    }

    public function insertQnA()
    {
        try {
            // Načítanie JSON súboru
            $data = json_decode(file_get_contents(__ROOT__ . '/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            $this->connection->beginTransaction();
            $sql_check = "SELECT COUNT(*) FROM qna WHERE otazka = :otazka";
            $statement_check = $this->connection->prepare($sql_check);
            $sql = "INSERT INTO qna (otazka, odpoved) VALUES (:otazka, :odpoved)";
            $statement = $this->connection->prepare($sql);

            for ($i = 0; $i < count($otazky); $i++) {
                // Skontroluj, či otázka už existuje
                $statement_check->bindParam(':otazka', $otazky[$i]);
                $statement_check->execute();
                $count = $statement_check->fetchColumn();

                if ($count == 0) {
                    $statement->bindParam(':otazka', $otazky[$i]);
                    $statement->bindParam(':odpoved', $odpovede[$i]);
                    $statement->execute();
                }
            }

            $this->connection->commit();
            echo "Dáta boli vložené";
        } catch (Exception $e) {
            echo "Chyba pri vkladaní dát do databázy: " . $e->getMessage();
            $this->connection->rollback();
        }
    }
}
?>
