<?php 

final class PDOConnection {

    private PDO $pdo;

    public function __construct($dsn='mysql:dbname=SmsBump;host=127.0.0.1', $user='root', $pass=NULL, $driver_options=NULL)
    {
        $this->pdo = new PDO($dsn, $user, $pass, $driver_options);
    }

    public function getPDO() : PDO {
        return $this->pdo;
    }

    public function deleteOldPassword(string $email, string $telephone) {
        $SQL = "DELETE FROM SMSBUMP.OTP WHERE email = :email AND telephone = :telephone";
        $statment = $this->pdo->prepare($SQL);
        $statment->execute(array(':email' => $email, ':telephone' => $telephone));
    }}