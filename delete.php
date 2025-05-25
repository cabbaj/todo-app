<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_id"])) {
    $id = $_POST["delete_id"];

    $stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->execute([
        "id" => $id
    ]);

    header("location: index.php");
    exit;
}