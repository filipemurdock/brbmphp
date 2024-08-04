
<?php

if ($user["access"]["bulk"] != 1) {
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

$services = $conn->prepare("SELECT * FROM services ");
$services->execute(array());
$services = $services->fetchAll(PDO::FETCH_ASSOC);

require admin_view('bulk');

if ($_POST) {
    $services = $_POST["service"];

    foreach ($services as $id => $value) {
        $service_name = $_POST["name_and_lang-$id"];


        $name_lang_array = array(
            "en" => $service_name,
            "pt-BR" => $service_name
        );

       
        $name_lang_json = json_encode($name_lang_array);


        $update = $conn->prepare("UPDATE services SET 
            service_name=:name, 
            name_lang=:name_lang,
            service_min=:min, 
            service_max=:max, 
            service_price=:price, 
            service_description=:description 
            WHERE service_id=:id ");

        $update->execute(array(
            "name" => $service_name,
            "name_lang" => $name_lang_json,
            "min" => $_POST["min-$id"],
            "max" => $_POST["max-$id"],
            "price" => $_POST["price-$id"],
            "description" => $_POST["desc-$id"],
            "id" => $id
        ));

        if ($update) {
            $_SESSION["client"]["data"]["success"] = 1;
            $_SESSION["client"]["data"]["successText"] = "Atualização bem-sucedida";
        } else {
            $errorText = "Falha na atualização";
            $error = 1;
        }
    }
    header("Location:" . site_url("admin/bulk"));
    exit;
}


?>
