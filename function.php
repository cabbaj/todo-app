<?php
/********************************** 
void is define type of value for return
PDO before $conn is type 
***********************************/
require "db.php";

/* CHECK ACTION */
function handleAction(PDO $conn, string $action): void
{
  switch ($action) {
    // ADD
    case "add":
      addTodo($conn, $_POST["todo"]);
      break;

    // set session to prevent re-submission when edit
    case "edit":
      setEditMode($_POST["editId"]);
      break;

    // SAVE after edit
    case "save":
      saveTodo($conn, $_POST["editId"], $_POST["editTodo"]);
      break;

    // CANCEL
    case "cancel":
      cancelEdit();
      break;
  }
}

// ADD TODO
function addTodo(PDO $conn, string $todo): void
{
  if (!empty($todo)) {
    try {
      $stmt = $conn->prepare("INSERT INTO todos (title) VALUES (:title)");
      $stmt->execute(["title" => $todo]);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }
}

// SET MODE
function setEditMode(int $editId): void
{
  $_SESSION["editModeId"] = $editId;
}

// SAVE EDITED TODO
function saveTodo(PDO $conn, int $editId, string $editTodo): void
{
  if (!empty($editTodo)) {
    try {
      $stmt = $conn->prepare("UPDATE todos SET title = :title WHERE id = :id");
      $stmt->execute([
        "title" => $editTodo,
        "id" => $editId
      ]);
    } catch (PDOException $e) {
      echo $e->getMessage();
    }
  }

  // clear session after save
  $_SESSION["editModeId"] = null;
}

function cancelEdit()
{
  // clear session
  $_SESSION["editModeId"] = null;
}
