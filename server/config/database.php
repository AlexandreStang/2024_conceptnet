<?php
class Database
{
    private $host = "localhost"; // TODO: À remplacer avec www-ens.iro.umontreal.ca (probablement)
    private $db_name = "ift3225db"; // TODO: À renommer (+ celle créée dans les fichiers Python) pour qu'elle fonctionne sur le DIRO
    private $username = "hello";
    private $password = "hello";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
