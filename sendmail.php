<?php
declare(strict_types=1);
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Метод не разрешён']);
    exit;
}
header('Content-Type: application/json; charset=UTF-8');

$theme   = trim($_POST['theme']   ?? '');
$name    = trim($_POST['name']    ?? '');
$phone   = trim($_POST['phone']   ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');
$captcha = trim($_POST['captcha'] ?? '');
$agree   = isset($_POST['agree']);

$errors = [];
if ($theme === '')                             $errors['theme']  = 'Выберите тему.';
if ($name === '')                              $errors['name']   = 'Укажите ФИО.';
if ($phone === '')                             $errors['phone']  = 'Укажите телефон.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))$errors['email']  = 'Некорректный e-mail';
if ($message === '')                           $errors['message']= 'Пустое сообщение.';
if ($captcha !== ($_SESSION['captcha_code'] ?? ''))
    $errors['captcha'] = 'Неверная капча.';
if (!$agree)                                   $errors['agree']  = 'Нужно согласие.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['message' => 'Проверьте поля', 'errors' => $errors]);
    exit;
}

$body = <<<HTML
<h1>Новое сообщение с сайта</h1>
<p><strong>Тема:</strong> $theme</p>
<p><strong>ФИО:</strong> $name</p>
<p><strong>Телефон:</strong> $phone</p>
<p><strong>E-mail:</strong> $email</p>
<p><strong>Сообщение:</strong><br>$message</p>
HTML;

try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->setLanguage('ru');
    $mail->isHTML();

    $mail->isSMTP();
    $mail->Host       = 'smtp.yandex.ru';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'log';
    $mail->Password   = 'pass';
    $mail->Port       = 465;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->setFrom('from@example.com', 'Feedback');
    $mail->addAddress('to@example.com');
    $mail->addReplyTo($email, $name);
    $mail->Subject = "Сообщение с сайта: $theme";
    $mail->Body    = $body;

    $mail->send();
    echo json_encode(['message' => 'Сообщение отправлено!']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Не удалось отправить письмо: ' . $mail->ErrorInfo]);
}
