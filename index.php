<?php
require "db.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $todo = $_POST["todo"];

    if (!empty($todo)) {
        try {
            $stmt = $conn->prepare("INSERT INTO todos (title) VALUES (:title)");
            $stmt->execute([
                "title" => $todo
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // Redirect to prevent form re-submission
    header("location:" . $_SERVER["PHP_SELF"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM todos");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App</title>
</head>

<body>
    <div>
        <h1>To-Do List</h1>
        <form method="post">
            <input type="text" name="todo">
            <button type="submit">Add</button>
        </form>

        <!-- Display todos -->
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <div>
                    <p><?php echo htmlspecialchars($row["title"]); ?>
                        <a href="edit.php?id=<?php echo $row["id"]; ?>">Edit</a>

                    <form action="delete.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <button type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                    </form>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>