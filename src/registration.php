<?php

require_once __DIR__ . '/helpers.php';


// Получение данных из формы
$login = $_POST['login'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Подключение к базе данных
$connect = getDB();

// Подготовленный запрос для предотвращения SQL-инъекций
$sql = "INSERT INTO `users` (login, name, email, password) VALUES ('$login', '$name', '$email', '$password')";
if ($connect -> query($sql) === TRUE) {
    header("Location: http://localhost/practic/login.html");
    // echo 'Регистрация прошла успешно!';
} else {
    echo 'Данный пользователь уже зарегистрирован :(';
}

?>
