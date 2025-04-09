<?php
session_start();
require_once __DIR__ . '/src/helpers.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: http://localhost/practic/login.html");
    exit;
}

$conn = getDB();
$userId = $_SESSION['user']['id'];

// Получаем имя пользователя
$userQuery = $conn->prepare("SELECT login FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userName = ($userResult && $userResult->num_rows > 0)
    ? $userResult->fetch_assoc()['login']
    : "Пользователь";

// Обработка отправки формы резюме
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $experience = $_POST['experience'];
  $skills = $_POST['skills'];
  $education = $_POST['education'];
  
  // Подготовка и выполнение запроса на обновление резюме
  $stmt = $conn->prepare("INSERT INTO Resume (user_id, experience, skills, education) VALUES (?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE experience = ?, skills = ?, education = ?");
  $stmt->bind_param("issssss", $userId, $experience, $skills, $education, $experience, $skills, $education);

  if ($stmt->execute()) {
      echo "<script>alert('Резюме успешно сохранено!');</script>";
  } else {
      echo "<script>alert('Ошибка: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}


// Получение данных резюме для отображения с использованием JOIN
$sql = "
    SELECT u.login AS name, r.experience, r.skills, r.education 
    FROM Resume r
    JOIN users u ON r.user_id = u.id 
    WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId); // Используем актуальный ID пользователя
$stmt->execute();
$result = $stmt->get_result();
$resume = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Профиль</title>
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
      <h1>Профиль</h1>

      <div class="card resume-card">
        <h2>Резюме</h2>
        <form action="profile.php" method="POST">
          <div class="form-group">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" readonly />
          </div>
          <div class="form-group">
            <label for="experience">Опыт работы:</label>
            <textarea id="experience" name="experience" rows="3" required><?php echo isset($resume['experience']) ? htmlspecialchars($resume['experience']) : ''; ?></textarea>
          </div>
          <div class="form-group">
            <label for="skills">Навыки:</label>
            <input type="text" id="skills" name="skills" value="<?php echo isset($resume['skills']) ? htmlspecialchars($resume['skills']) : ''; ?>" required />
          </div>
          <div class="form-group">
            <label for="education">Образование:</label>
            <input type="text" id="education" name="education" value="<?php echo isset($resume['education']) ? htmlspecialchars($resume['education']) : ''; ?>" required />
          </div>
          <button type="submit" class="btn">Сохранить резюме</button>
        </form>
      </div>

      <!-- Включаем таблицу с откликами из другого файла -->
      <?php include 'src/responses.php'; ?>

    </main>
  </div>

</body>
</html>
