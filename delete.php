<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    $stmt = $conn->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->execute([
        "id" => $id
    ]);

    header("location: index.php");
    exit;
}