<?php
session_start();
require_once __DIR__ . '/helpers.php'; // путь поправлен

$conn = getDB();
$userId = $_SESSION['user']['id'];
$vacancyId = isset($_POST['vacancy_id']) ? (int)$_POST['vacancy_id'] : 0;

if (!$vacancyId) {
    die("Ошибка: не передан ID вакансии");
}

// Проверим, существует ли такая вакансия
$check = $conn->prepare("SELECT id FROM vacancy WHERE id = ?");
$check->bind_param("i", $vacancyId);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    die("Ошибка: вакансия с ID $vacancyId не найдена");
}

// Всё ок — добавляем отклик
$stmt = $conn->prepare("INSERT INTO responses (user_id, vacancy_id) VALUES (?, ?)");
$stmt->bind_param("ii", $userId, $vacancyId);

if ($stmt->execute()) {
    echo "<script>alert('Вы успешно откликнулись на вакансию'); window.location.href = '../profile.php';</script>";
} else {
    echo "Ошибка при добавлении отклика: " . $stmt->error;
}
