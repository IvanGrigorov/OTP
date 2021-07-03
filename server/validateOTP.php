<?php

require_once('./PDOConnection.php');

$password = $_POST['password'];
$email = $_POST['email'];
$telephone = $_POST['telephone'];

echo json_encode(['data' => workTheAttempt($password, $email, $telephone)]);

function workTheAttempt(string $password, string $email, string $telephone) {
    $pdo = new PDOConnection();
    logAttempt( $password,  $email,  $telephone, $pdo->getPDO());
    $numberOfAttemptsForLastMinute = getNumberOfAttempstForLastMinute($email, $telephone, $pdo->getPDO())[0]['attempts'];
    $isPasswordValid = isPasswordValid($email, $telephone, $password, $pdo->getPDO())[0]['otp'];

    if ($numberOfAttemptsForLastMinute > 2 && !$isPasswordValid) {
        $pdo->deleteOldPassword($email, $telephone);
        return ['message' => "You exceeded the maximum of three unsuccessful attempts. Generate new password after a minute",
                "attempts" => 3];
    }
    else if (!$isPasswordValid) {
        return ['message' => "Your password does not match",
                "attempts" => $numberOfAttemptsForLastMinute++];
    }
    else if ($isPasswordValid) {
        $pdo->deleteOldPassword($email, $telephone);
        return ['message' => "Welcome to SMSBump!",
                "attempts" => 0];
    }

}

function logAttempt(string $pass, string $email, string $telephone, PDO $pdo) {
    $SQL = "INSERT INTO SMSBUMP.Attempts(email, telephone, otp, date_issued) VALUES(:email, :telephone, :otp, NOW())";
    $statment = $pdo->prepare($SQL);
    $statment->execute(array(':email' => $email, ':telephone' => $telephone, 'otp' => $pass));
    return $pass;
}

function getNumberOfAttempstForLastMinute(string $email, string $telephone, PDO $pdo) {
    $SQL = "SELECT COUNT(*) as attempts FROM SMSBUMP.Attempts WHERE email = :email AND telephone = :telephone AND date_issued >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $statment = $pdo->prepare($SQL);
    $statment->execute(array(':email' => $email, ':telephone' => $telephone));
    return $statment->fetchAll();
}

function isPasswordValid(string $email, string $telephone, string $password, PDO $pdo) {
    $SQL = "SELECT COUNT(*) as otp FROM SMSBUMP.OTP WHERE email = :email AND telephone = :telephone AND otp = :password";
    $statment = $pdo->prepare($SQL);
    $statment->execute(array(':email' => $email, ':telephone' => $telephone, ':password' => $password));
    return $statment->fetchAll();
}

