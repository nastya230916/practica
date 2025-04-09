<?php
session_start();
require_once __DIR__ . '/src/helpers.php';


if (!isset($_SESSION['user']['id'])) {
  header("Location: http://localhost/pr_0704/login.html");
  exit;
}

$conn = getDB();
$userId = $_SESSION['user']['id'];

// Проверяем, была ли форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $salary = $_POST['salary'];
    $location = $_POST['location'];

    // Подготовка и выполнение запроса
    $stmt = $conn->prepare("INSERT INTO vacancy (title, description, skills, salary, location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $skills, $salary, $location);

    if ($stmt->execute()) {
        echo "Вакансия успешно создана!";
    } else {
        echo "Ошибка: " . $stmt->error;
    }

    // Закрываем подготовленный запрос и соединение
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Создать вакансию</title>
  <link rel="stylesheet" href="assets/style.css"/>
</head>
<body>

  <div class="container">
    <aside>
      <h1>Меню</h1>
      <nav>
        <a href="main.php">Все вакансии</a>
        <a href="profile.php">Профиль</a>
        <a href="createVacancy.php">Создать вакансию</a>
        <a href="src/logout.php" class="logout">Выйти</a>
      </nav>
    </aside>

    <main>
      <h1>Создание вакансии</h1>

      <form class="vacancy-form" action="createVacancy.php" method="POST">
        <label>
          Название вакансии
          <input type="text" name="title" required />
        </label>

        <label>
          Описание
          <textarea name="description" rows="5" required></textarea>
        </label>

        <label>
          Навыки
          <input type="text" name="skills" required />
        </label>

        <label>
          Зарплата
          <input type="text" name="salary" required />
        </label>

        <label>
          Местоположение
          <input type="text" name="location" required />
        </label>

        <button type="submit" class="btn">Создать вакансию</button>
      </form>

    </main>
  </div>

</body>
</html>
