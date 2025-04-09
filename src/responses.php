<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/helpers.php'; // Убедитесь, что путь корректен

if (!isset($_SESSION['user']['id'])) {
    header("Location: http://localhost/practic/login.html");
    exit;
}

$conn = getDB();

// Получение ID пользователя из сессии
$userId = $_SESSION['user']['id'];

// Получение откликов из базы данных
$sql = "
    SELECT v.title AS vacancy_title, res.date AS created_at
    FROM responses res
    JOIN vacancy v ON res.vacancy_id = v.id
    WHERE res.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Создание HTML-таблицы для откликов
?>

<h2>Мои отклики</h2>
<table class="responses-table">
    <thead>
        <tr>
            <th>Вакансия</th>
            <th>Дата отклика</th>
            <th>Пользователь</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Вывод откликов в таблицу
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['vacancy_title']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                  </tr>";
        }

        // Закрываем соединение
        $stmt->close();
        $conn->close();
        ?>
    </tbody>
</table>