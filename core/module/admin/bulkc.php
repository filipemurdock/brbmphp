<?php

if ($user["access"]["bulkc"] != 1) {
    header("Location:" . site_url("admin"));
    exit();
}

if ($_SESSION["client"]["data"]) {
    $data = $_SESSION["client"]["data"];
    foreach ($data as $key => $value) {
        $$key = $value;
    }
    unset($_SESSION["client"]);
}

$services = $conn->prepare("SELECT * FROM categories");
$services->execute();
$services = $services->fetchAll(PDO::FETCH_ASSOC);

require admin_view('bulkc');

if ($_POST) {
    $services = $_POST["service"];

    foreach ($services as $id => $value) {
        $category_name = $_POST["name-$id"];
        $name_lang = $_POST["name_lang-$id"];
        $category_line = $_POST["category_line-$id"];
        $category_type = $_POST["category_type-$id"];
        $category_secret = $_POST["category_secret-$id"];

        $update = $conn->prepare("UPDATE categories SET category_name = :category_name, name_lang = :name_lang, category_line = :category_line, category_type = :category_type, category_secret = :category_secret WHERE category_id = :id");
        $update->execute(array("category_name" => $category_name, "name_lang" => $name_lang, "category_line" => $category_line, "category_type" => $category_type, "category_secret" => $category_secret, "id" => $id));

        if ($update) {
            $_SESSION["client"]["data"]["success"] = 1;
            $_SESSION["client"]["data"]["successText"] = "Successful";
        } else {
            $errorText = "Failed";
            $error = 1;
        }
    }

    header("Location:" . site_url("admin/bulkc"));
}
