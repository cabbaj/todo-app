<?php
session_start();

require "db.php";
require "function.php";

// use session
// ?? is choose null if not set value for $edit_mode
$editModeId = $_SESSION["editModeId"] ?? null;

// HANDLE ACTION
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST["action"])) {
		handleAction($conn, $_POST["action"]);
		header("location:" . $_SERVER["PHP_SELF"]);
		exit;
	}
}

/* clear mode if refresh the webpage */
if ($_SERVER["REQUEST_METHOD"] === "GET") {
	unset($_SESSION["editModeId"]);
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
				<?php if ($editModeId == $row["id"]): ?>

					<!-- edit mode -->
					<form method="post" style="display:inline;">
						<input type="hidden" name="action" value="save">
						<input type="hidden" name="editId" value="<?php echo $row["id"]; ?>">
						<input type="text" name="editTodo" value="<?php echo $row["title"]; ?>" required>
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
						<form method="post" style="display: inline;">
							<input type="hidden" name="action" value="edit">
							<input type="hidden" name="editId" value="<?php echo $row["id"]; ?>">
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