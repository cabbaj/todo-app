<?php
session_start();

require "db.php";

// use session
$edit_mode = $_SESSION["edit_mode"] ?? null;

/* CHECK ACTION */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {

            // ADD
            case "add":
                $todo = $_POST["todo"];

                if (!empty($todo)) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO todos (title) VALUES (:title)");
                        $stmt->execute(["title" => $todo]);
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                }
                break;

            // set session to prevent re-direct
            case "edit":
                $_SESSION["edit_mode"] = $_POST["edit_id"];
                break;

            // SAVE after edit
            case "save":
                $edit_id = $_POST["edit_id"];
                $edit_todo = $_POST["edit_todo"];

                if (!empty($edit_todo)) {
                    try {
                        $stmt = $conn->prepare("UPDATE todos SET title = :title WHERE id = :id");
                        $stmt->execute([
                            "title" => $edit_todo,
                            "id" => $edit_id
                        ]);
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                }

                // clear session after save
                $_SESSION["edit_mode"] = null;
                break;

            // CANCEL
            case "cancel":
                // clear session
                $_SESSION["edit_mode"] = null;
                break;
        }
    }
    // Redirect to prevent form re-submission
    header("location:" . $_SERVER["PHP_SELF"]);
    exit;
}

/* clear mode after refresh */
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    unset($_SESSION["edit_mode"]);
}

/* FETCH */
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
            <input type="text" name="todo" placeholder="New Task">
            <!-- this for mode -->
            <input type="hidden" name="action" value="add">
            <button type="submit">Add</button>
        </form>

        <!-- Display todos -->
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <!-- check each row -->
                <?php if ($edit_mode == $row["id"]): ?>

                    <!-- edit mode -->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="edit_id" value="<?php echo $row["id"]; ?>">
                        <input type="text" name="edit_todo" value="<?php echo $row["title"]; ?>" required>
                        <button type="submit">Save</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit">Cancel</button>
                    </form>

                <?php else: ?>

                    <!-- display mode -->
                    <div>
                        <span><?php echo htmlspecialchars($row["title"]); ?></span>

                        <!-- edit button -->
                        <!-- <button>
                            <a href="?edit_id=<?php echo $row["id"]; ?>">Edit</a>
                        </button> -->
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="edit_id" value="<?php echo $row["id"]; ?>">
                            <button type="submit">Edit</button>
                        </form>

                        <!-- delete button -->
                        <form action="delete.php" method="post" style="display: inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row["id"]; ?>">
                            <button type="submit" onclick="return confirm('Delete this item?')">Delete</button>
                        </form>
                    </div>

                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>