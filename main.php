<?php
session_start();
require_once __DIR__ . '/src/helpers.php';

if (!isset($_SESSION['user']['id'])) {
  header("Location: http://localhost/practic/login.html");
  exit;
}


$conn = getDB(); // Получаем соединение
$userId = $_SESSION['user']['id'];

// Получаем все вакансии
$sql = "SELECT * FROM vacancy ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Поиск
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = '';
if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $searchSql = "WHERE title LIKE '%$searchEscaped%' OR skills LIKE '%$searchEscaped%'";
}

$sql = "SELECT * FROM vacancy $searchSql ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Вакансии</title>
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
      <h1>Доступные вакансии</h1>

      <!-- Форма поиска -->
      <form class="filters" method="get" action="main.php">
        <input 
          type="text" 
          name="search" 
          placeholder="Поиск по названию или навыкам..." 
          value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="btnn">Поиск</button>
      </form>

      <div class="grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
              <div>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><strong>Локация:</strong> <?= htmlspecialchars($row['location']) ?></p>
                <p><strong>Зарплата:</strong> <?= htmlspecialchars($row['salary']) ?></p>
                <p><strong>Описание:</strong><br> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <div class="tags">
                  <?php 
                    $skills = explode(',', $row['skills']); 
                    foreach ($skills as $skill): ?>
                      <span><?= htmlspecialchars(trim($skill)) ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
              <form action="src/apply.php" method="POST">
                  <input type="hidden" name="vacancy_id" value="ID_ВАКАНСИИ" />
                  <button type="submit" class="btn">Откликнуться</button>
              </form>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>Нет доступных вакансий.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>

</body>
</html>
