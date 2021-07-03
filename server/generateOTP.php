<?php

require_once('./PDOConnection.php');
$email = $_POST['email'];
$telephone = $_POST['telephone'];

echo json_encode(['password' => savePassword($email, $telephone)]);

function generateOTP() {
    $bytes = random_bytes(6);
    return bin2hex($bytes);
}

function saveNewPassword(string $email, string $telephone, PDO $pdo) {
    $pass = generateOTP();
    $SQL = "INSERT INTO SMSBUMP.OTP(email, telephone, otp, date_issued) VALUES(:email, :telephone, :otp, NOW())";
    $statment = $pdo->prepare($SQL);
    $statment->execute(array(':email' => $email, ':telephone' => $telephone, 'otp' => $pass));
    return $pass;
}

function savePassword(string $email, string $telephone) {
    $pdo = new PDOConnection();
    $numberOfPasswordsForLastMinute = getNumberOfGeneratedPasswordsForLastMinute($email, $telephone, $pdo->getPDO())[0]['passwords'];
    if ($numberOfPasswordsForLastMinute) {
        return "You can generate new password in a minute";

    }
    $pdo->deleteOldPassword($email, $telephone);
    $pass = saveNewPassword($email, $telephone, $pdo->getPDO());
    return "Your Password Is: " .$pass;
}

function getNumberOfGeneratedPasswordsForLastMinute(string $email, string $telephone, PDO $pdo) {
    $SQL = "SELECT COUNT(*) as passwords FROM SMSBUMP.OTP WHERE email = :email AND telephone = :telephone AND date_issued >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
    $statment = $pdo->prepare($SQL);
    $statment->execute(array(':email' => $email, ':telephone' => $telephone));
    return $statment->fetchAll();
}
