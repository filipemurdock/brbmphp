<?php

$action = $_POST["action"];
$languages = $conn->prepare("SELECT * FROM languages WHERE language_type=:type");
$languages->execute(["type" => 2]);
$languages = $languages->fetchAll(PDO::FETCH_ASSOC);
if (!KEY) {
    $return = "<div class=\"modal-body\"><center><h1>VOID PHP VERSION<h1></center></div>";
    echo json_encode(["content" => $return, "title" => "Error"]);
}
if ($action == "providers_list") {
    $smmapi = new SMMApi();
    $provider = $_POST["provider"];
    $api = $conn->prepare("SELECT * FROM service_api WHERE id=:id");
    $api->execute(["id" => $provider]);
    $api = $api->fetch(PDO::FETCH_ASSOC);
    if ($api["api_type"] == 3) {
        echo "<div class=\"service-mode__block\">\r\n\r\n          <div class=\"form-group\">\r\n\r\n           <label>Serviço</label>\r\n\r\n            <input class=\"form-control\" name=\"service\" placeholder=\"Enter Service ID\">\r\n\r\n          </div>\r\n\r\n        </div>";
    } else {
        if ($api["api_type"] == 1) {
            $services = $smmapi->action(["key" => $api["api_key"], "action" => "services"], $api["api_url"]);
            echo "<div class=\"service-mode__block\">\r\n\r\n          <div class=\"form-group\">\r\n\r\n         <label>Serviço</label>\r\n\r\n            <select class=\"form-control\" name=\"service\">";
            foreach ($services as $service) {
                echo "<option value=\"" . $service->service . "\"";
                if ($_SESSION["data"]["service"] == $service->service) {
                    echo "selected";
                }
                echo ">" . $service->service . " - " . $service->name . " - " . priceFormat($service->rate) . "</option>";
            }
            echo "</select>\r\n\r\n          </div>\r\n\r\n        </div>";
        }
    }
    unset($_SESSION["data"]);
} else {
    if ($action == "paymentmethod-sortable") {
        $list = $_POST["methods"];
        foreach ($list as $method) {
            $update = $conn->prepare("UPDATE payment_methods SET method_line=:line WHERE id=:id ");
            $update->execute(["id" => $method["id"], "line" => $method["line"]]);
        }
    } else {
        if ($action == "service-sortable") {
            $list = $_POST["services"];
            foreach ($list as $service) {
                $id = explode("-", $service["id"]);
                $update = $conn->prepare("UPDATE services SET service_line=:line WHERE service_id=:id ");
                $update->execute(["id" => $id[1], "line" => $service["line"]]);
            }
        } else {
            if ($action == "category-sortable") {
                $list = $_POST["categories"];
                foreach ($list as $category) {
                    $update = $conn->prepare("UPDATE categories SET category_line=:line WHERE category_id=:id ");
                    $update->execute(["id" => $category["id"], "line" => $category["line"]]);
                }
            } else {
                if ($action == "secret_user") {
                    $id = $_POST["id"];
                    $services = $conn->prepare("SELECT * FROM services RIGHT JOIN categories ON categories.category_id=services.category_id WHERE services.service_secret='1' || categories.category_secret='1'  ");
                    $services->execute(["id" => $id]);
                    $services = $services->fetchAll(PDO::FETCH_ASSOC);
                    $grouped = array_group_by($services, "category_id");
                    $return = "<form class=\"form\" action=\"" . site_url("admin/clients/export") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n\r\n\r\n        <div class=\"majer\">\r\n\r\n               <div>\r\n\r\n                  <div class=\"services-import__list-wrap services-import__list-active\">\r\n\r\n                     <div class=\"services-import__scroll-wrap\">";
                    foreach ($grouped as $category) {
                        $row = ["table" => "clients_category", "where" => ["client_id" => $id, "category_id" => $category[0]["category_id"]]];
                        $return .= "<span>\r\n\r\n                            <div class=\"services-import__category\">\r\n\r\n                               <div class=\"services-import__category-title\">\r\n\r\n                                 <label> ";
                        if ($category[0]["category_secret"] == 1) {
                            $return .= "<small><i class=\"fa fa-lock\"></i></small> <input type=\"checkbox\"";
                            if (countRow($row)) {
                                $return .= "checked";
                            }
                            $return .= " class=\"tiny-toggle\" data-tt-palette=\"blue\" data-url=\"" . site_url("admin/clients/secret_category/" . $id) . "\" data-id=\"" . $category[0]["category_id"] . "\"> ";
                        }
                        $return .= $category[0]["category_name"] . " </label>\r\n\r\n                               </div>\r\n\r\n                            </div>\r\n\r\n                             <div class=\"services-import__packages\">\r\n\r\n                                <ul>";
                        for ($i = 0; $i < count($category); $i++) {
                            $row = ["table" => "clients_service", "where" => ["client_id" => $id, "service_id" => $category[$i]["service_id"]]];
                            $return .= "<li id=\"service-" . $category[$i]["service_id"] . "\">\r\n\r\n                                     <label>";
                            if ($category[$i]["service_secret"] == 1) {
                                $return .= "<small><i class=\"fa fa-lock\"></i></small> ";
                            }
                            $return .= $category[$i]["service_id"] . " - " . $category[$i]["service_name"] . "\r\n\r\n                                        <span class=\"services-import__packages-price-edit\" >";
                            if ($category[$i]["service_secret"] == 1) {
                                $return .= "<input type=\"checkbox\"";
                                if (countRow($row)) {
                                    $return .= "checked";
                                }
                                $return .= "  class=\"tiny-toggle\" data-tt-palette=\"blue\" data-url=\"" . site_url("admin/clients/secret_service/" . $id) . "\" data-id=\"" . $category[$i]["service_id"] . "\">";
                            }
                            $return .= "</span>\r\n\r\n                                     </label>\r\n\r\n                                    </li>";
                        }
                        $return .= "</ul>\r\n\r\n                             </div>\r\n\r\n                          </span>";
                    }
                    $return .= "</div>\r\n\r\n                  </div>\r\n\r\n               </div>\r\n\r\n            </div>\r\n\r\n            <script src=\"" . site_url("js/admin/") . "jquery.tinytoggle.min.js\"></script>\r\n\r\n            <link rel=\"stylesheet\" type=\"text/css\" href=\"" . site_url("css/admin/") . "tinytoggle.min.css\" rel=\"stylesheet\">\r\n\r\n            <script>\r\n\r\n            \$(\".tiny-toggle\").tinyToggle({\r\n\r\n              onCheck: function() {\r\n\r\n                var id     = \$(this).attr(\"data-id\");\r\n\r\n                var action = \$(this).attr(\"data-url\")+\"?type=on&id=\"+id;\r\n\r\n                  \$.ajax({\r\n\r\n                  url:  action,\r\n\r\n                  type: 'GET',\r\n\r\n                  dataType: 'json',\r\n\r\n                  cache: false,\r\n\r\n                  contentType: false,\r\n\r\n                  processData: false\r\n\r\n                  }).done(function(result){\r\n\r\n                    if( result == 1 ){\r\n\r\n                      \$.toast({\r\n\r\n                          heading: \"success\",\r\n\r\n                          text: \"Transação bem-sucedida\",\r\n\r\n                          icon: \"success\",\r\n\r\n                          loader: true,\r\n\r\n                          loaderBg: \"#9EC600\"\r\n\r\n                      });\r\n\r\n                    }else{\r\n\r\n                      \$.toast({\r\n\r\n                          heading: \"Malsucedido\",\r\n\r\n                          text: \"Operação falhou\",\r\n\r\n                          icon: \"error\",\r\n\r\n                          loader: true,\r\n\r\n                          loaderBg: \"#9EC600\"\r\n\r\n                      });\r\n\r\n                    }\r\n\r\n                  })\r\n\r\n                  .fail(function(){\r\n\r\n                    \$.toast({\r\n\r\n                        heading: \"Malsucedido\",\r\n\r\n                        text: \"Operação falhou\",\r\n\r\n                        icon: \"error\",\r\n\r\n                        loader: true,\r\n\r\n                        loaderBg: \"#9EC600\"\r\n\r\n                    });\r\n\r\n                  });\r\n\r\n              },\r\n\r\n              onUncheck: function() {\r\n\r\n                var id     = \$(this).attr(\"data-id\");\r\n\r\n                var action = \$(this).attr(\"data-url\")+\"?type=off&id=\"+id;\r\n\r\n                  \$.ajax({\r\n\r\n                  url:  action,\r\n\r\n                  type: 'GET',\r\n\r\n                  dataType: 'json',\r\n\r\n                  cache: false,\r\n\r\n                  contentType: false,\r\n\r\n                  processData: false\r\n\r\n                  }).done(function(result){\r\n\r\n                    if( result == 1 ){\r\n\r\n                      \$.toast({\r\n\r\n                          heading: \"success\",\r\n\r\n                          text: \"Transação bem-sucedida\",\r\n\r\n                          icon: \"success\",\r\n\r\n                          loader: true,\r\n\r\n                          loaderBg: \"#9EC600\"\r\n\r\n                      });\r\n\r\n                    }else{\r\n\r\n                      \$.toast({\r\n\r\n                          heading: \"Malsucedido\",\r\n\r\n                          text: \"Operação falhou\",\r\n\r\n                          icon: \"error\",\r\n\r\n                          loader: true,\r\n\r\n                          loaderBg: \"#9EC600\"\r\n\r\n                      });\r\n\r\n                    }\r\n\r\n                  })\r\n\r\n                  .fail(function(){\r\n\r\n                    \$.toast({\r\n\r\n                        heading: \"Malsucedido\",\r\n\r\n                        text: \"Operação falhou\",\r\n\r\n                        icon: \"error\",\r\n\r\n                        loader: true,\r\n\r\n                        loaderBg: \"#9EC600\"\r\n\r\n                    });\r\n\r\n                  });\r\n\r\n              },\r\n\r\n            });\r\n\r\n\r\n\r\n            </script>\r\n\r\n\r\n\r\n        </div>\r\n\r\n\r\n\r\n          <div class=\"modal-footer\">\r\n\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n\r\n          </div>\r\n\r\n          </form>";
                    echo json_encode(["content" => $return, "title" => "User specific services"]);
                } else {
                    if ($action == "new_user") {
                        $return = "<form class=\"form\" action=\"" . site_url("admin/clients/new") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n";
                        if ($settings["name_secret"] == 1) {
                            $return .= "<div style=\"display: none;\">";
                        }
                        $return .= " \r\n          <div class=\"form-group\">\r\n\r\n            <label class=\"form-group__service-name\"> Name</label>\r\n\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"\">\r\n\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label class=\"form-group__service-name\"> Last name</label>\r\n\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"\">\r\n\r\n          </div>";
                        if ($settings["name_secret"] == 1) {
                            $return .= "</div>";
                        }
                        $return .= "  \r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label> E-mail</label>\r\n\r\n            <input type=\"text\" name=\"email\" value=\"\" class=\"form-control\">\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label>Nome de usuário</label>\r\n\r\n            <input type=\"text\" name=\"username\" class=\"form-control\" value=\"\">\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label>Senha</label>\r\n\r\n            <div class=\"input-group\">\r\n\r\n              <input type=\"text\" class=\"form-control\" name=\"password\" value=\"\" id=\"user_password\">\r\n\r\n              <span class=\"input-group-btn\">\r\n\r\n                <button class=\"btn btn-default\" onclick=\"UserPassword()\" type=\"button\">\r\n\r\n                <span class=\"fa fa-random\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" aria-hidden=\"true\" data-original-title=\"Create Password\"></span></button>\r\n\r\n              </span>\r\n\r\n            </div>\r\n\r\n          </div>";
                        if ($settings["skype_area"] == 1) {
                            $return .= "<div style=\"display: none;\">";
                        }
                        $return .= " \r\n          <div class=\"form-group\">\r\n\r\n            <label>Número de telefone</label>\r\n\r\n            <input type=\"text\" name=\"telephone\" class=\"form-control\" value=\"\">\r\n\r\n          </div>";
                        if ($settings["skype_area"] == 1) {
                            $return .= "</div>";
                        }
                        $return .= " \r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Saldo negativo</label>\r\n\r\n              <select class=\"form-control\" id=\"debit\" name=\"balance_type\">\r\n\r\n                    <option value=\"2\">Fechado</option></option>\r\n\r\n                    <option value=\"1\">Ativo</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\" id=\"debit_limit\">\r\n\r\n            <label>Valor máximo da dívida</label>\r\n\r\n            <input type=\"text\" name=\"debit_limit\" class=\"form-control\" value=\"\">\r\n\r\n          </div>";
                        if ($user["access"]["admins"] == 0) {
                            $return .= "<div style=\"display: none;\">";
                        }
                        $return .= "       <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Ele é autorizado?</label>\r\n\r\n              <select class=\"form-control\" id=\"limit\" name=\"access[admin_access]\">\r\n\r\n                    <option value=\"0\" selected>Não</option>\r\n\r\n                    <option value=\"1\">Sim</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\" id=\"admin_limit\">\r\n\r\n            <label>powers</label>\r\n\r\n              <div class=\"form-group\">\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[users]\"  value=\"0\"> Users\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[orders]\"  value=\"0\"> Orders\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[subscriptions]\"  value=\"0\"> Subscriptions\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[dripfeed]\"  value=\"0\"> Drip-feed\r\n\r\n                  </label>\r\n\r\n                     \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                  <input type=\"checkbox\" class=\"access\" name=\"access[tasks]\"  value=\"0\"> Tasks\r\n\r\n                </label>\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[services]\"  value=\"0\"> Serviços\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments]\"  value=\"0\"> Pagamentos\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[tickets]\"  value=\"0\"> Destek\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[reports]\"  value=\"0\"> Estatísticas\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[general_settings]\"  value=\"0\"> Settings\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[pages]\"  value=\"0\"> Páginas\r\n\r\n </label>\r\n                  \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[child_panels]\"  value=\"0\"> Painéis filho\r\n\r\n </label>\r\n                  \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[blog]\"  value=\"0\"> Blog\r\n\r\n                  </label>     <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[seo]\"  value=\"0\"> Seo Configurações\r\n\r\n </label>\r\n                  \r\n                               <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[menu]\"  value=\"0\"> Menu Configurações\r\n\r\n </label>\r\n                  \r\n                               <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[license]\"  value=\"0\"> License Information\r\n\r\n                  </label>\r\n                            \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[subject]\"  value=\"0\"> Títulos da mensagem \r\n\r\n </label>\r\n     \r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments_settings]\"  value=\"0\"> Payment methods\r\n\r\n                    </label>  \r\n  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[bank_accounts]\"  value=\"0\"> Bank Accounts\r\n\r\n                    </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments_bonus]\"  value=\"0\"> Bônus de Pagamento\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[alert_settings]\"  value=\"0\"> Notification Settings\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[providers]\"  value=\"0\"> Provedores\r\n\r\n </label>\r\n                  \r\n                          <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[modules]\"  value=\"0\"> Módulos\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[themes]\"  value=\"0\"> Configurações de tema\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[language]\"  value=\"0\"> Configurações de idioma\r\n\r\n </label>\r\n\r\n             \r\n\r\n\r\n\r\n                        <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[logs]\"  value=\"0\"> Loglar</label>\r\n\r\n                    \r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[admins]\"  value=\"0\"> Authorization Editing\r\n\r\n                  </label>\r\n\r\n              </div>";
                        if ($user["access"]["admins"] == 0) {
                            $return .= "</div>";
                        }
                        $return .= "\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n\r\n\r\n        </div>\r\n\r\n\r\n\r\n          <div class=\"modal-footer\">\r\n\r\n            <button type=\"submit\" class=\"btn btn-primary\">Aprovar</button>\r\n\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n\r\n          </div>\r\n\r\n          </form>\r\n\r\n          <script>\r\n\r\n            var type = \$(\"#debit\").val();\r\n\r\n            if( type == 2 ){\r\n\r\n              \$(\"#debit_limit\").hide();\r\n\r\n            } else{\r\n\r\n              \$(\"#debit_limit\").show();\r\n\r\n            }\r\n\r\n            \$(\"#debit\").change(function(){\r\n\r\n              var type = \$(this).val();\r\n\r\n                if( type == 2 ){\r\n\r\n                  \$(\"#debit_limit\").hide();\r\n\r\n                } else{\r\n\r\n                  \$(\"#debit_limit\").show();\r\n\r\n                }\r\n\r\n            });\r\n\r\n            var type = \$(\"#limit\").val();\r\n\r\n            if( type == 0 ){\r\n\r\n              \$(\"#admin_limit\").hide();\r\n\r\n            } else{\r\n\r\n              \$(\"#admin_limit\").show();\r\n\r\n            }\r\n\r\n            \$(\"#limit\").change(function(){\r\n\r\n              var type = \$(this).val();\r\n\r\n                if( type == 0 ){\r\n\r\n                  \$(\"#admin_limit\").hide();\r\n\r\n                } else{\r\n\r\n                  \$(\"#admin_limit\").show();\r\n\r\n                }\r\n\r\n            });\r\n\r\n          </script>";
                        echo json_encode(["content" => $return, "title" => "criar novo usuário"]);
                    } else {
                        if ($action == "edit_user") {
                            $id = $_POST["id"];
                            $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
                            $user->execute(["id" => $id]);
                            $user = $user->fetch(PDO::FETCH_ASSOC);
                            $access = json_decode($user["access"], true);
                            $user2 = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
                            $user2->execute(["id" => $_COOKIE["u_id"]]);
                            $user2 = $user2->fetch(PDO::FETCH_ASSOC);
                            $access2 = json_decode($user2["access"], true);
                            $return = "<form class=\"form\" action=\"" . site_url("admin/clients/edit/" . $user["username"]) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n";
                            if ($settings["name_secret"] == 2) {
                                $return .= "<div class=\"form-group\">\r\n\r\n            <label class=\"form-group__service-name\">Name</label>\r\n\r\n            <input type=\"text\" class=\"form-control\" name=\"first_name\" value=\"" . $user["first_name"] . "\">\r\n\r\n          </div>\r\n       <div class=\"form-group\">\r\n\r\n            <label class=\"form-group__service-name\">Last name</label>\r\n\r\n            <input type=\"text\" class=\"form-control\" name=\"last_name\" value=\"" . $user["last_name"] . "\">\r\n\r\n          </div>";
                            }
                            $return .= " \r\n        \r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label> E-mail</label>\r\n\r\n            <input type=\"text\" name=\"email\" value=\"" . $user["email"] . "\" class=\"form-control\">\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label>Nome de usuário</label>\r\n\r\n            <input type=\"text\" name=\"username\" class=\"form-control\"  value=\"" . $user["username"] . "\">\r\n\r\n          </div>\r\n\r\n";
                            if ($settings["skype_area"] == 2) {
                                $return .= " \r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label>Número de telefone</label>\r\n\r\n            <input type=\"text\" name=\"telephone\" class=\"form-control\" value=\"" . $user["telephone"] . "\">\r\n\r\n          </div>";
                            }
                            $return .= " \r\n\r\n  <div class=\"form-group\">\r\n\r\n            <label>API Key</label>\r\n\r\n            <div class=\"input-group\">\r\n\r\n              <input type=\"text\" class=\"form-control\" value=\"" . $user["apikey"] . "\" id=\"api_key\" disabled>\r\n\r\n              <span class=\"input-group-btn\">\r\n\r\n               <a href=\"/admin/clients/change_apikey/" . $user["client_id"] . "\" class=\"btn btn-default\">\r\n\r\n                <span class=\"fa fa-random\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" aria-hidden=\"true\" data-original-title=\"Generate API Key\"></span></a>\r\n\r\n              </span>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Saldo negativo</label>\r\n\r\n              <select class=\"form-control\" id=\"debit\" name=\"balance_type\">\r\n\r\n                    <option value=\"2\"";
                            if ($user["balance_type"] == 2) {
                                $return .= "selected";
                            }
                            $return .= ">Fechado</option></option>\r\n\r\n                    <option value=\"1\"";
                            if ($user["balance_type"] == 1) {
                                $return .= "selected";
                            }
                            $return .= ">Open</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\" id=\"debit_limit\">\r\n\r\n            <label>Valor máximo da dívida</label>\r\n\r\n            <input type=\"text\" name=\"debit_limit\" class=\"form-control\" value=\"" . $user["debit_limit"] . "\">\r\n\r\n          </div>";
                            if ($access2["admins"] == 1) {
                                $return .= "\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Conta autorizada?</label>\r\n\r\n              <select class=\"form-control\" id=\"limit\" name=\"access[admin_access]\">\r\n\r\n                    <option value=\"0\"";
                                if ($access["admin_access"] == 0) {
                                    $return .= "selected";
                                }
                                $return .= ">Não</option>\r\n\r\n                    <option value=\"1\"";
                                if ($access["admin_access"] == 1) {
                                    $return .= "selected";
                                }
                                $return .= ">Sim</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\" id=\"admin_limit\">\r\n\r\n            <label>Powers</label>\r\n\r\n              <div class=\"form-group \">\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[users]\"";
                                if ($access["users"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= " value=\"1\"> Usuarios\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[orders]\"";
                                if ($access["orders"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Pedidos\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[subscriptions]\"";
                                if ($access["subscriptions"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Subscriptions\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[dripfeed]\"";
                                if ($access["dripfeed"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Drip-feed\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                  <input type=\"checkbox\" class=\"access\" name=\"access[tasks]\"";
                                if ($access["tasks"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Tasks\r\n\r\n                </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[services]\"";
                                if ($access["services"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Serviços\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments]\"";
                                if ($access["payments"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Pagamentos\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[tickets]\"";
                                if ($access["tickets"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Solicitações de suporte\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[reports]\"";
                                if ($access["reports"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Estatísticas\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[general_settings]\"";
                                if ($access["general_settings"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Configurações\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[pages]\"";
                                if ($access["pages"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Páginas\r\n\r\n </label>   \r\n                  \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[blog]\"";
                                if ($access["blog"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Blog\r\n\r\n                  </label>   <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[seo]\"";
                                if ($access["seo"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Seo Configurações\r\n\r\n </label>\r\n                  \r\n                          <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[menu]\"";
                                if ($access["menu"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Menu Configurações\r\n\r\n </label>\r\n                  \r\n                     \r\n                  \r\n                          <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[subject]\"";
                                if ($access["subject"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Títulos da mensagem \r\n\r\n </label>\r\n                  \r\n        <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[child_panels]\"";
                                if ($access["child_panels"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Painéis filho\r\n\r\n </label>\r\n\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments_settings]\"";
                                if ($access["payments_settings"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Configurações de pagamento\r\n\r\n </label> \r\n                  \r\n                     <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[bank_accounts]\"";
                                if ($access["bank_accounts"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\">Contas Bancárias\r\n\r\n </label>   \r\n                  \r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[payments_bonus]\"";
                                if ($access["payments_bonus"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Bônus de Pagamento\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[alert_settings]\"";
                                if ($access["alert_settings"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Configurações de notificação\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[providers]\"";
                                if ($access["providers"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Provedores\r\n\r\n </label>\r\n\r\n    <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[modules]\"";
                                if ($access["modules"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Módulos\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[themes]\"";
                                if ($access["themes"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Configurações de tema\r\n\r\n </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[language]\"";
                                if ($access["language"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Configurações de idioma\r\n\r\n </label>\r\n\r\n             \r\n\r\n                         <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[logs]\"";
                                if ($access["logs"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Loglar\r\n\r\n                  </label>\r\n                  \r\n                  \r\n                         <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[provider_logs]\"";
                                if ($access["provider_logs"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Provider Logs\r\n\r\n                  </label>\r\n\r\n                         <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[guard_logs]\"";
                                if ($access["guard_logs"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Protection Logs\r\n\r\n                  </label>\r\n\r\n                  <label class=\"checkbox-inline\">\r\n\r\n                    <input type=\"checkbox\" class=\"access\" name=\"access[admins]\"";
                                if ($access["admins"] == 1) {
                                    $return .= "checked";
                                }
                                $return .= "  value=\"1\"> Yetki Configurações\r\n\r\n </label>\r\n\r\n              </div>";
                            }
                            $return .= "</div>\r\n\r\n          </div>\r\n\r\n        </div>\r\n\r\n\r\n\r\n          <div class=\"modal-footer\">\r\n\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar</button>\r\n\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n\r\n          </div>\r\n\r\n          </form>\r\n\r\n          <script>\r\n\r\n            var type = \$(\"#debit\").val();\r\n\r\n            if( type == 2 ){\r\n\r\n              \$(\"#debit_limit\").hide();\r\n\r\n            } else{\r\n\r\n              \$(\"#debit_limit\").show();\r\n\r\n            }\r\n\r\n            \$(\"#debit\").change(function(){\r\n\r\n              var type = \$(this).val();\r\n\r\n                if( type == 2 ){\r\n\r\n                  \$(\"#debit_limit\").hide();\r\n\r\n                } else{\r\n\r\n                  \$(\"#debit_limit\").show();\r\n\r\n                }\r\n\r\n            });\r\n\r\n            var type = \$(\"#limit\").val();\r\n\r\n            if( type == 0 ){\r\n\r\n              \$(\"#admin_limit\").hide();\r\n\r\n            } else{\r\n\r\n              \$(\"#admin_limit\").show();\r\n\r\n            }\r\n\r\n            \$(\"#limit\").change(function(){\r\n\r\n              var type = \$(this).val();\r\n\r\n                if( type == 0 ){\r\n\r\n                  \$(\"#admin_limit\").hide();\r\n\r\n                } else{\r\n\r\n                  \$(\"#admin_limit\").show();\r\n\r\n                }\r\n\r\n            });\r\n\r\n          </script>\r\n\r\n          ";
                            echo json_encode(["content" => $return, "title" => "Editar usuário"]);
                        } else {
                            if ($action == "pass_user") {
                                $id = $_POST["id"];
                                $user = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
                                $user->execute(["id" => $id]);
                                $user = $user->fetch(PDO::FETCH_ASSOC);
                                $return = "<form class=\"form\" action=\"" . site_url("admin/clients/pass/" . $user["username"]) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n\r\n\r\n          <div class=\"form-group\">\r\n\r\n            <label>new password</label>\r\n\r\n            <div class=\"input-group\">\r\n\r\n              <input type=\"text\" class=\"form-control\" name=\"password\" value=\"\" id=\"user_password\">\r\n\r\n              <span class=\"input-group-btn\">\r\n\r\n                <button class=\"btn btn-default\" onclick=\"UserPassword()\" type=\"button\">\r\n\r\n                <span class=\"fa fa-random\" data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"\" aria-hidden=\"true\" data-original-title=\"Create Password\"></span></button>\r\n\r\n              </span>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n        </div>\r\n\r\n\r\n\r\n          <div class=\"modal-footer\">\r\n\r\n            <button type=\"submit\" class=\"btn btn-primary\">Aprovar</button>\r\n\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n\r\n          </div>\r\n\r\n          </form>";
                                echo json_encode(["content" => $return, "title" => "Edit Password"]);
                            } else {
                                if ($action == "alert_user") {
                                    $return = "<form class=\"form\" action=\"" . site_url("admin/clients/alert") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Todos / Apenas 1</label>\r\n\r\n              <select class=\"form-control\" id=\"user_type\" name=\"user_type\">\r\n\r\n                    <option value=\"all\">Todos os membros</option>\r\n\r\n                    <option value=\"secret\">Notificar apenas 1</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n          \r\n          <div class=\"form-group\" id=\"username\">\r\n\r\n            <label>Nome de usuário</label>\r\n\r\n            <input type=\"text\" name=\"username\" class=\"form-control\" value=\"\">\r\n\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n            <label>Tipo de notificação</label>\r\n\r\n              <select class=\"form-control\" id=\"alert_type\" name=\"alert_type\">\r\n\r\n                    <option value=\"email\">E-mail</option>\r\n\r\n                    <option value=\"sms\">SMS</option>\r\n\r\n                </select>\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div id=\"email\">\r\n\r\n            <div class=\"form-group\">\r\n\r\n              <label>Cabeçalho do e-mail</label>\r\n\r\n              <input type=\"text\" name=\"subject\" class=\"form-control\" value=\"\">\r\n\r\n            </div>\r\n\r\n          </div>\r\n\r\n\r\n\r\n          <div class=\"form-group\" id=\"username\">\r\n\r\n            <label>Mensagem </label>\r\n\r\n            <textarea type=\"text\" name=\"message\" class=\"form-control\" rows=\"5\"></textarea>\r\n\r\n          </div>\r\n\r\n        </div>\r\n\r\n        <script type=\"text/javascript\">\r\n\r\n          \$(\"#username\").hide();\r\n\r\n          \$(\"#user_type\").change(function(){\r\n\r\n            var type = \$(this).val();\r\n\r\n            if( type == \"secret\" ){\r\n\r\n              \$(\"#username\").show();\r\n\r\n            } else{\r\n\r\n              \$(\"#username\").hide();\r\n\r\n            }\r\n\r\n          });\r\n          \$(\"#alert_type\").change(function(){\r\n            var type = \$(this).val();\r\n            if( type == \"email\" ){\r\n              \$(\"#email\").show();\r\n            } else{\r\n\r\n              \$(\"#email\").hide();\r\n            }\r\n          });\r\n        </script>\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Notify users</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                    echo json_encode(["content" => $return, "title" => "Notificação aos usuários"]);
                                } else {
                                    if ($action == "new_service") {
                                        $categories = $conn->prepare("SELECT * FROM categories ORDER BY category_line ");
                                        $categories->execute([]);
                                        $categories = $categories->fetchAll(PDO::FETCH_ASSOC);
                                        $providers = $conn->prepare("SELECT * FROM service_api");
                                        $providers->execute([]);
                                        $providers = $providers->fetchAll(PDO::FETCH_ASSOC);
                                        $return = "<form class=\"form\" action=\"" . site_url("admin/services/new-service") . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">";
                                        if (1 < count($languages)) {
                                            $translationList = "<a class=\"other_services\"> Translations (" . (count($languages) - 1) . ") </a>";
                                        } else {
                                            $translationList = "";
                                        }
                                        foreach ($languages as $language) {
                                            if ($language["default_language"]) {
                                                $return .= "<div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> " . $translationList . " </label>\r\n              <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n            </div>";
                                                if (1 < count($languages)) {
                                                    $return .= "<div class=\"hidden\" id=\"translationsList\">";
                                                }
                                            } else {
                                                $return .= "<div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> </label>\r\n              <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n            </div>";
                                            }
                                        }
                                        if (1 < count($languages)) {
                                            $return .= "</div>";
                                        }
                                        $return .= "<div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Categoria de serviço</label>\r\n              <select class=\"form-control\" name=\"category\">\r\n                    <option value=\"0\">Selecione a categoria..</option>";
                                        foreach ($categories as $category) {
                                            $return .= "<option value=\"" . $category["category_id"] . "\">" . $category["category_name"] . "</option>";
                                        }
                                        $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__wrapper\">\r\n            <div class=\"service-mode__block\">\r\n              <div class=\"form-group\">\r\n             <label>Tipo de serviço</label>\r\n                <select class=\"form-control\" name=\"package\">\r\n                      <option value=\"1\">Serviço</option>\r\n                      <option value=\"2\">Pacote</option>\r\n                      <option value=\"3\">Comentário especial</option>\r\n                      <option value=\"4\">Pacote de comentário </option>\r\n                  </select>\r\n              </div>\r\n            </div>\r\n            <div class=\"service-mode__block\">\r\n              <div class=\"form-group\">\r\n              <label>Modo</label>\r\n                <select class=\"form-control\" name=\"mode\" id=\"serviceMode\">\r\n                      <option value=\"1\">Manual</option>\r\n                      <option value=\"2\">Automático (API)</option>\r\n                  </select>\r\n              </div>\r\n            </div>\r\n\r\n            <div id=\"autoMode\" style=\"display: none\">\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Provedor de Serviços</label>\r\n                  <select class=\"form-control\" name=\"provider\" id=\"provider\">\r\n                        <option value=\"0\">Selecione o provedor de serviços...</option>";
                                        foreach ($providers as $provider) {
                                            $return .= "<option value=\"" . $provider["id"] . "\">" . $provider["api_name"] . "</option>";
                                        }
                                        $return .= "</select>\r\n                </div>\r\n              </div>\r\n              <div id=\"provider_service\">\r\n              </div>\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Dripfeed</label>\r\n                  <select class=\"form-control\" name=\"dripfeed\">\r\n                    <option value=\"1\">Desativado</option>\r\n                    <option value=\"2\">Ativo</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Valor de quantidade 1000</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"price\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"row\">\r\n            <div class=\"col-md-6 form-group\">\r\n              <label class=\"form-group__service-name\">Mínimo</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"min\" value=\"\">\r\n            </div>\r\n\r\n            <div class=\"col-md-6 form-group\">\r\n              <label class=\"form-group__service-name\">Máximo</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"max\" value=\"\">\r\n            </div>\r\n          </div>\r\n<hr>\r\n          <div class=\"row\">\r\n          <div class=\"form-group col-md-6\">\r\n          <label>Botão de Cancelar</label>\r\n            <select class=\"form-control\" name=\"cancel_type\">\r\n                <option value=\"2\">Ativo</option>\r\n                <option value=\"1\" selected>Desativado</option>\r\n            </select>\r\n          </div>\r\n          \r\n          \r\n          <div class=\"form-group col-md-6\">\r\n          <label>Botão de Refill</label>\r\n            <select id=\"refill\" class=\"form-control\" name=\"refill_type\">\r\n                <option value=\"2\">Ativo</option>\r\n                <option value=\"1\" selected>Desativado</option>\r\n            </select>\r\n          </div>\r\n          </div>\r\n          \r\n          <div id=\"refill_day\" class=\"form-group\">\r\n          <label>Refill Maximum Day <small>(If lifetime, write 0)</small></label>\r\n            <input type=\"number\" class=\"form-control\" name=\"refill_time\">\r\n          </div>\r\n       \r\n\r\n          <hr>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Link do pedido<small>(Mostrado na nova página do pedido)</small></label>\r\n              <select class=\"form-control\" name=\"want_username\">\r\n                  <option value=\"1\">Link</option>\r\n                  <option value=\"2\">Nome de usuário</option>\r\n              </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Serviço pessoal <small>(Apenas as pessoas que você escolher podem ver)</small></label>\r\n              <select class=\"form-control\" name=\"secret\">\r\n                  <option value=\"2\">Não</option>\r\n                  <option value=\"1\">Sim</option>\r\n              </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Velocidade de serviço <small>(Exibido como símbolo e cor na lista de serviço)</small></label>\r\n              <select class=\"form-control\" name=\"speed\">\r\n                  <option value=\"1\">Lento</option>\r\n                  <option value=\"2\">Às vezes Lento</option>\r\n                  <option value=\"3\">Normal</option>\r\n                  <option value=\"4\">Rápido</option>\r\n              </select>\r\n            </div>\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Adicionar novo serviço</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>\r\n          <script src=\"";
                                        $return .= site_url("js/admin/");
                                        $return .= "script.js\"></script>\r\n          <script>\r\n\r\n          var type = \$(\"#refill\").val();\r\n\r\n          if( type == 1 ){\r\n\r\n            \$(\"#refill_day\").hide();\r\n\r\n          } else{\r\n\r\n            \$(\"#refill_day\").show();\r\n\r\n          }\r\n\r\n          \$(\"#refill\").change(function(){\r\n\r\n            var type = \$(this).val();\r\n\r\n              if( type == 1 ){\r\n\r\n                \$(\"#refill_day\").hide();\r\n\r\n              } else{\r\n\r\n                \$(\"#refill_day\").show();\r\n\r\n              }\r\n\r\n          });\r\n\r\n          \$(\".other_services\").click(function(){\r\n            var control = \$(\"#translationsList\");\r\n            if( control.attr(\"class\") == \"hidden\" ){\r\n              control.removeClass(\"hidden\");\r\n            } else{\r\n              control.addClass(\"hidden\");\r\n            }\r\n          });\r\n          </script>\r\n          ";
                                        echo json_encode(["content" => $return, "title" => "Adicionar novo serviço"]);
                                    } else {
                                        if ($action == "edit_service") {
                                            $id = $_POST["id"];
                                            $smmapi = new SMMApi();
                                            $categories = $conn->prepare("SELECT * FROM categories ORDER BY category_line ");
                                            $categories->execute([]);
                                            $categories = $categories->fetchAll(PDO::FETCH_ASSOC);
                                            $serviceInfo = $conn->prepare("SELECT * FROM services LEFT JOIN service_api ON service_api.id=services.service_api WHERE services.service_id=:id ");
                                            $serviceInfo->execute(["id" => $id]);
                                            $serviceInfo = $serviceInfo->fetch(PDO::FETCH_ASSOC);
                                            $providers = $conn->prepare("SELECT * FROM service_api");
                                            $providers->execute([]);
                                            $providers = $providers->fetchAll(PDO::FETCH_ASSOC);
                                            $multiName = json_decode($serviceInfo["name_lang"], true);
                                            if (in_array($serviceInfo["service_package"], ["11", "12", "13", "14", "15"])) {
                                                $return = "<form class=\"form\" action=\"" . site_url("admin/services/edit-subscription/" . $serviceInfo["service_id"]) . "\" method=\"post\" data-xhr=\"true\">\r\n            <div class=\"modal-body\">";
                                                if (1 < count($languages)) {
                                                    $translationList = "<a class=\"other_services\"> Translations (" . (count($languages) - 1) . ") </a>";
                                                } else {
                                                    $translationList = "";
                                                }
                                                foreach ($languages as $language) {
                                                    if ($language["default_language"]) {
                                                        $return .= "\r\n          <div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> " . $translationList . " </label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n                  </div>";
                                                        if (1 < count($languages)) {
                                                            $return .= "<div class=\"hidden\" id=\"translationsList\">";
                                                        }
                                                    } else {
                                                        $return .= "<div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> </label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n                  </div>";
                                                    }
                                                }
                                                if (1 < count($languages)) {
                                                    $return .= "</div>";
                                                }
                                                $return .= "<div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Categoria de serviço</label>\r\n                  <select class=\"form-control\" name=\"category\">\r\n                        <option value=\"0\">Selecione a categoria..</option>";
                                                foreach ($categories as $category) {
                                                    $return .= "<option value=\"" . $category["category_id"] . "\"";
                                                    if ($serviceInfo["category_id"] == $category["category_id"]) {
                                                        $return .= "selected";
                                                    }
                                                    $return .= ">" . $category["category_name"] . "</option>";
                                                }
                                                $return .= "</select>\r\n                </div>\r\n              </div>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Tipo de assinatura</label>\r\n                  <select class=\"form-control\" disabled  id=\"subscription_package\">\r\n                        <option value=\"11\"";
                                                if ($serviceInfo["service_package"] == 11) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Instagram Auto Likes - Unlimited</option>\r\n                        <option value=\"12\"";
                                                if ($serviceInfo["service_package"] == 12) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Instagram Auto Views - Unlimited</option>\r\n                        <option value=\"14\"";
                                                if ($serviceInfo["service_package"] == 14) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Instagram Auto Like - Timed</option>\r\n                        <option value=\"15\"";
                                                if ($serviceInfo["service_package"] == 15) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Instagram Auto Watch - Timed</option>\r\n                    </select>\r\n                </div>\r\n              </div>\r\n\r\n              \r\n\r\n              <div class=\"service-mode__wrapper\">\r\n\r\n                <div class=\"service-mode__block\">\r\n                  <div class=\"form-group\">\r\n                  <label>Modo</label>\r\n                    <select class=\"form-control\" name=\"mode\" id=\"serviceMode\">\r\n                          <option value=\"2\"";
                                                if ($serviceInfo["service_api"] != 0) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Automático (API)</option>\r\n                      </select>\r\n                  </div>\r\n                </div>\r\n\r\n\r\n                <div id=\"autoMode\" style=\"display: none\">\r\n                  <div class=\"service-mode__block\">\r\n                    <div class=\"form-group\">\r\n                    <label>Provedor de Serviços</label>\r\n                      <select class=\"form-control\" name=\"provider\" id=\"provider\">\r\n                            <option value=\"0\">Selecione o provedor de serviços...</option>";
                                                foreach ($providers as $provider) {
                                                    $return .= "<option value=\"" . $provider["id"] . "\"";
                                                    if ($serviceInfo["service_api"] == $provider["id"]) {
                                                        $return .= "selected";
                                                    }
                                                    $return .= ">" . $provider["api_name"] . "</option>";
                                                }
                                                $return .= "</select>\r\n                    </div>\r\n                  </div>\r\n                  <div id=\"provider_service\">";
                                                $services = $smmapi->action(["key" => $serviceInfo["api_key"], "action" => "services"], $serviceInfo["api_url"]);
                                                if ($serviceInfo["api_type"] == 1) {
                                                    $return .= "<div class=\"service-mode__block\">\r\n                      <div class=\"form-group\">\r\n                      <label>Serviços</label>\r\n                        <select class=\"form-control\" name=\"service\">";
                                                    foreach ($services as $service) {
                                                        $return .= "<option value=\"" . $service->service . "\"";
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= "selected";
                                                        }
                                                        $return .= ">" . $service->service . " - " . $service->name . " - " . $service->rate . "</option>";
                                                    }
                                                    $return .= "</select>\r\n                      </div>\r\n                    </div>";
                                                } else {
                                                    if ($serviceInfo["api_type"] == 3) {
                                                        $return .= "<div class=\"service-mode__block\">\r\n                      <div class=\"form-group\">\r\n                      <label>Serviços</label>\r\n                        <input class=\"form-control\" value=\"" . $serviceInfo["api_service"] . "\" name=\"service\">\r\n                      </div>\r\n                    </div>";
                                                    }
                                                }
                                                $return .= "</div>\r\n                </div>\r\n              </div>\r\n\r\n              <div id=\"unlimited\">\r\n                <div class=\"form-group\">\r\n                  <label class=\"form-group__service-name\">Preço do serviço (1000 unidades)</label>\r\n                  <input type=\"text\" class=\"form-control\" name=\"price\" value=\"" . $serviceInfo["service_price"] . "\">\r\n                </div>\r\n\r\n                <div class=\"row\">\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Pedido mínimo</label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $serviceInfo["service_min"] . "\">\r\n                  </div>\r\n\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Pedido máximo</label></label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $serviceInfo["service_max"] . "\">\r\n                  </div>\r\n                </div>\r\n              </div>\r\n\r\n              <div id=\"limited\">\r\n                <div class=\"form-group\">\r\n                  <label class=\"form-group__service-name\">service price</label>\r\n                  <input type=\"text\" class=\"form-control\" name=\"limited_price\" value=\"" . $serviceInfo["service_price"] . "\">\r\n                </div>\r\n\r\n\r\n\r\n                <div class=\"row\">\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Shipment amount</label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"autopost\" value=\"" . $serviceInfo["service_autopost"] . "\">\r\n                  </div>\r\n\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Order amount</label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"limited_min\" value=\"" . $serviceInfo["service_min"] . "\">\r\n                  </div>\r\n                </div>\r\n                <div class=\"form-group\">\r\n                  <label class=\"form-group__service-name\">Package Time <small>(day)</small></label>\r\n                  <input type=\"text\" class=\"form-control\" name=\"autotime\" value=\"" . $serviceInfo["service_autotime"] . "\">\r\n                </div>\r\n              </div>\r\n\r\n              <hr>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Personal Service (Only the people you choose can see it.)</label>\r\n                  <select class=\"form-control\" name=\"secret\">\r\n                      <option value=\"2\"";
                                                if ($serviceInfo["service_secret"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Não</option>\r\n                      <option value=\"1\"";
                                                if ($serviceInfo["service_secret"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Sim</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Velocidade de serviço (exibido como símbolo e cor na lista de serviços.)</label>\r\n                  <select class=\"form-control\" name=\"speed\">\r\n                      <option value=\"1\"";
                                                if ($serviceInfo["service_speed"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Lento</option>\r\n                      <option value=\"2\"";
                                                if ($serviceInfo["service_speed"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Às vezes Lento</option>\r\n                      <option value=\"3\"";
                                                if ($serviceInfo["service_speed"] == 3) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Normal</option>\r\n                      <option value=\"4\"";
                                                if ($serviceInfo["service_speed"] == 4) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Rápido</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n\r\n            </div>\r\n\r\n              <div class=\"modal-footer\">\r\n                <button type=\"submit\" class=\"btn btn-primary\">Atualizar informações de assinatura</button>\r\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n              </div>\r\n              </form>\r\n              <script type=\"text/javascript\">\r\n\r\n              var type = \$(\"#refill\").val();\r\n\r\n              if( type == 1 ){\r\n    \r\n                \$(\"#refill_day\").hide();\r\n    \r\n              } else{\r\n    \r\n                \$(\"#refill_day\").show();\r\n    \r\n              }\r\n    \r\n              \$(\"#refill\").change(function(){\r\n    \r\n                var type = \$(this).val();\r\n    \r\n                  if( type == 1 ){\r\n    \r\n                    \$(\"#refill_day\").hide();\r\n    \r\n                  } else{\r\n    \r\n                    \$(\"#refill_day\").show();\r\n    \r\n                  }\r\n    \r\n              });\r\n\r\n              \$(\".other_services\").click(function(){\r\n                var control = \$(\"#translationsList\");\r\n                if( control.attr(\"class\") == \"hidden\" ){\r\n                  control.removeClass(\"hidden\");\r\n                } else{\r\n                  control.addClass(\"hidden\");\r\n                }\r\n              });\r\n              var site_url  = \$(\"head base\").attr(\"href\");\r\n                \$(\"#provider\").change(function(){\r\n                  var provider = \$(this).val();\r\n                  getProviderServices(provider,site_url);\r\n                });\r\n\r\n                getProvider();\r\n                \$(\"#serviceMode\").change(function(){\r\n                  getProvider();\r\n                });\r\n\r\n                getSalePrice();\r\n                \$(\"#saleprice_cal\").change(function(){\r\n                  getSalePrice();\r\n                });\r\n\r\n                getSubscription();\r\n                \$(\"#subscription_package\").change(function(){\r\n                  getSubscription();\r\n                });\r\n                function getProviderServices(provider,site_url){\r\n                  if( provider == 0 ){\r\n                    \$(\"#provider_service\").hide();\r\n                  }else{\r\n                    \$.post(site_url+\"admin/ajax_data\",{action:\"providers_list\",provider:provider}).done(function( data ) {\r\n                      \$(\"#provider_service\").show();\r\n                      \$(\"#provider_service\").html(data);\r\n                    }).fail(function(){\r\n                      alert(\"An error occurred!\");\r\n                    });\r\n                  }\r\n                }\r\n\r\n                function getProvider(){\r\n                  var mode = \$(\"#serviceMode\").val();\r\n                    if( mode == 1 ){\r\n                      \$(\"#autoMode\").hide();\r\n                    }else{\r\n                      \$(\"#autoMode\").show();\r\n                    }\r\n                }\r\n\r\n                function getSalePrice(){\r\n                  var type = \$(\"#saleprice_cal\").val();\r\n                    if( type == \"normal\" ){\r\n                      \$(\"#saleprice\").hide();\r\n                      \$(\"#servicePrice\").show();\r\n                    }else{\r\n                      \$(\"#saleprice\").show();\r\n                      \$(\"#servicePrice\").hide();\r\n                    }\r\n                }\r\n\r\n                function getSubscription(){\r\n                  var type = \$(\"#subscription_package\").val();\r\n                    if( type == \"11\" || type == \"12\" ){\r\n                      \$(\"#unlimited\").show();\r\n                      \$(\"#limited\").hide();\r\n                    }else{\r\n                      \$(\"#unlimited\").hide();\r\n                      \$(\"#limited\").show();\r\n                    }\r\n                }\r\n              </script>\r\n              ";
                                                echo json_encode(["content" => $return, "title" => "Abonelik düzenle (ID: " . $serviceInfo["service_id"] . ")"]);
                                            } else {
                                                $return = "\r\n\r\n        <form class=\"form\" action=\"" . site_url("admin/services/edit-service/" . $serviceInfo["service_id"]) . "\" method=\"post\" data-xhr=\"true\">\r\n            <div class=\"modal-body\">";
                                                if (1 < count($languages)) {
                                                    $translationList = "<a class=\"other_services\"> Translations (" . (count($languages) - 1) . ") </a>";
                                                } else {
                                                    $translationList = "";
                                                }
                                                foreach ($languages as $language) {
                                                    if ($language["default_language"]) {
                                                        $return .= "\r\n\t\t\t\t  <div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> " . $translationList . " </label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n                  </div>";
                                                        if (1 < count($languages)) {
                                                            $return .= "<div class=\"hidden\" id=\"translationsList\">";
                                                        }
                                                    } else {
                                                        $return .= "<div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> </label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n                  </div>";
                                                    }
                                                }
                                                if (1 < count($languages)) {
                                                    $return .= "</div>";
                                                }
                                                $return .= "<div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Categoria de serviço</label>\r\n                  <select class=\"form-control\" name=\"category\">\r\n                        <option value=\"0\">Selecione a categoria..</option>";
                                                foreach ($categories as $category) {
                                                    $return .= "<option value=\"" . $category["category_id"] . "\"";
                                                    if ($serviceInfo["category_id"] == $category["category_id"]) {
                                                        $return .= "selected";
                                                    }
                                                    $return .= ">" . $category["category_name"] . "</option>";
                                                }
                                                $return .= "</select>\r\n                </div>\r\n              </div>\r\n\r\n              <div class=\"service-mode__wrapper\">\r\n                <div class=\"service-mode__block\">\r\n                  <div class=\"form-group\">\r\n                 <label>Tipo de serviço</label>\r\n                    <select class=\"form-control\" name=\"package\">\r\n                          <option value=\"1\"";
                                                if ($serviceInfo["service_package"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Serviços</option>\r\n                          <option value=\"2\"";
                                                if ($serviceInfo["service_package"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Pacote</option>\r\n                          <option value=\"3\"";
                                                if ($serviceInfo["service_package"] == 3) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Comentário especial</option>\r\n                          <option value=\"4\"";
                                                if ($serviceInfo["service_package"] == 4) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Pacote de comentário </option>\r\n                      </select>\r\n                  </div>\r\n                </div>\r\n                <div class=\"service-mode__block\">\r\n                  <div class=\"form-group\">\r\n                  <label>Modo</label>\r\n                    <select class=\"form-control\" name=\"mode\" id=\"serviceMode\">\r\n                          <option value=\"1\"";
                                                if ($serviceInfo["service_api"] == 0) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Manual</option>\r\n                          <option value=\"2\"";
                                                if ($serviceInfo["service_api"] != 0) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Automático (API)</option>\r\n                      </select>\r\n                  </div>\r\n                </div>\r\n\r\n                <div id=\"autoMode\" style=\"display: none\">\r\n                  <div class=\"service-mode__block\">\r\n                    <div class=\"form-group\">\r\n                    <label>Provedor de Serviços</label>\r\n                      <select class=\"form-control\" name=\"provider\" id=\"provider\">\r\n                            <option value=\"0\">Selecione o provedor de serviços...</option>";
                                                foreach ($providers as $provider) {
                                                    $return .= "<option value=\"" . $provider["id"] . "\"";
                                                    if ($serviceInfo["service_api"] == $provider["id"]) {
                                                        $return .= "selected";
                                                    }
                                                    $return .= ">" . $provider["api_name"] . "</option>";
                                                }
                                                $return .= "</select>\r\n                    </div>\r\n                  </div>\r\n                  <div id=\"provider_service\">";
                                                $services = $smmapi->action(["key" => $serviceInfo["api_key"], "action" => "services"], $serviceInfo["api_url"]);
                                                if ($serviceInfo["api_type"] == 1) {
                                                    $return .= "<div class=\"service-mode__block\">\r\n                        <div class=\"form-group\">\r\n                        <label>Serviços</label>\r\n                          <select class=\"form-control\" name=\"service\">";
                                                    foreach ($services as $service) {
                                                        $return .= "<option value=\"" . $service->service . "\"";
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= "selected";
                                                        }
                                                        $return .= ">" . $service->service . " - " . $service->name . " - " . $service->rate . "</option>";
                                                    }
                                                    $return .= "</select>\r\n                        </div>\r\n                      </div>";
                                                } else {
                                                    if ($serviceInfo["api_type"] == 3) {
                                                        $return .= "<div class=\"service-mode__block\">\r\n                        <div class=\"form-group\">\r\n                        <label>Serviços</label>\r\n                          <input class=\"form-control\" value=\"" . $serviceInfo["api_service"] . "\" name=\"service\">\r\n                        </div>\r\n                      </div>";
                                                    }
                                                }
                                                $return .= "</div>\r\n                  <div class=\"service-mode__block\">\r\n                    <div class=\"form-group\">\r\n                    <label>Dripfeed</label>\r\n                      <select class=\"form-control\" name=\"dripfeed\">\r\n                        <option value=\"1\"";
                                                if ($serviceInfo["service_dripfeed"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Desativado</option>\r\n                        <option value=\"2\"";
                                                if ($serviceInfo["service_dripfeed"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Ativo</option>\r\n                      </select>\r\n                    </div>\r\n                  </div>\r\n                </div>\r\n              </div>\r\n\r\n";
                                                if ($serviceInfo["service_api"] == 0) {
                                                    $return .= "\r\n    \r\n    \r\n                <div class=\"form-group\">\r\n                  <label class=\"form-group__service-name\">Preço do serviço (1000 unidades)</label>\r\n                  <input type=\"text\" class=\"form-control\" name=\"price\" value=\"" . $serviceInfo["service_price"] . "\">\r\n                </div>\r\n\r\n                <div class=\"row\">\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Pedido mínimo</label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $serviceInfo["service_min"] . "\">\r\n                  </div>\r\n\r\n                  <div class=\"col-md-6 form-group\">\r\n                    <label class=\"form-group__service-name\">Pedido máximo</label></label>\r\n                    <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $serviceInfo["service_max"] . "\">\r\n                  </div>\r\n                </div>\r\n          \r\n\r\n    \r\n    \r\n    ";
                                                } else {
                                                    $return .= "\r\n    \r\n    \r\n       <div class=\"form-group\">\r\n                                        <label class=\"form-group__service-name\" >";
                                                    if ($serviceInfo["sync_price"] == 1) {
                                                        $return .= "% Kaç arttırılsın?";
                                                    } else {
                                                        $return .= "Valor por 1000 ";
                                                    }
                                                    $return .= "</label>\r\n\r\n                  <div class=\"form-group\">\r\n                        <div class=\"input-group\">\r\n                            <input type=\"text\" ";
                                                    if ($serviceInfo["sync_price"] == 1) {
                                                        $return .= "style=\"display:none\"";
                                                    }
                                                    $return .= " class=\"form-control\" id=\"priceInput\" name=\"price\" value=\"" . $serviceInfo["service_price"] . "\">\r\n                            <div id=\"priceThreeInput\" ";
                                                    if ($serviceInfo["sync_price"] == 0) {
                                                        $return .= "style=\"display:none\"";
                                                    }
                                                    $return .= ">\r\n                            <div class=\"col-md-6\">\r\n                             <input type=\"text\" class=\"form-control\" style=\"border-radius:5px;\" id=\"priceInput\" name=\"sync_rate\" value=\"" . $serviceInfo["sync_rate"] . "\">\r\n                             </div>\r\n                             <div class=\"col-md-6\">\r\n                             \r\n                             <div class = \"input-group\">\r\n                            \t <span class = \"input-group-addon\">" . $settings["site_currency"] . "</span>\r\n                             \t<input type = \"text\" value=\"" . $serviceInfo["service_price"] . "\" readonly class =\" form-control\" placeholder=\"MajerPanel\">\r\n                             \r\n                     \t        <span class = \"input-group-addon\">";
                                                    foreach ($services as $service) {
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= $service->rate;
                                                        }
                                                    }
                                                    $return .= "</span>\r\n                     \t    \r\n                     \t  </div>\r\n                     \t  \r\n                     \t  \r\n                                <input type=\"hidden\" name=\"price_api\" value=\"";
                                                    foreach ($services as $service) {
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= $service->rate;
                                                        }
                                                    }
                                                    $return .= "\">\r\n                                \r\n                             </div>\r\n\r\n                         </div>\r\n\r\n                            <div class=\"input-group-addon\">\r\n                            <label class=\"switch\"><input  id=\"priceCheckbox\"  type=\"checkbox\" name=\"auto_price\" ";
                                                    if ($serviceInfo["sync_price"] == 1) {
                                                        $return .= "checked";
                                                    }
                                                    $return .= "/>\r\n                            <span class=\"slider round\"></span>\r\n                        </label>\r\n                         \r\n                     </div>\r\n                    </div>\r\n                    </div>\r\n                    \r\n              </div>\r\n\r\n              <div class=\"row\">\r\n                <div class=\"col-md-6 form-group\">\r\n                  <label class=\"form-group__service-name\">Pedido mínimo</label>\r\n                \r\n                <div id=\"minText\" style=\"padding: 11px;margin-left: 4px;\" class=\"form-group__provider-value\">";
                                                    foreach ($services as $service) {
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= $service->min;
                                                        }
                                                    }
                                                    $return .= "</div>\r\n                \r\n                     <div class=\"form-group\">\r\n                        <div class=\"input-group\">\r\n                            <input type=\"number\" id=\"minPriceInput\" style=\"height:43px;\" class=\"form-control\" name=\"min\" value=\"" . $serviceInfo["service_min"] . "\" ";
                                                    if ($serviceInfo["sync_min"] == 1) {
                                                        $return .= "readonly";
                                                    }
                                                    $return .= ">\r\n                            <div class=\"input-group-addon\" >\r\n                            <label class=\"switch\"><input  id=\"minPriceCheckbox\"  type=\"checkbox\" name=\"auto_min\" ";
                                                    if ($serviceInfo["sync_min"] == 1) {
                                                        $return .= "checked";
                                                    }
                                                    $return .= " />\r\n                            <span  class=\"slider round\"></span>\r\n                        </label>\r\n                     </div>\r\n                    </div>\r\n                    </div>\r\n                  </div>\r\n\r\n          \r\n                    \r\n                <div class=\"col-md-6 form-group\">\r\n                  <label class=\"form-group__service-name\">Pedido máximo</label></label>\r\n                  \r\n                  <div id=\"maxText\" style=\"padding: 11px;margin-left: 4px;\" class=\"form-group__provider-value\">";
                                                    foreach ($services as $service) {
                                                        if ($serviceInfo["api_service"] == $service->service) {
                                                            $return .= $service->max;
                                                        }
                                                    }
                                                    $return .= "</div>\r\n                \r\n                    <div class=\"form-group\">\r\n                        <div class=\"input-group\">\r\n                            <input type=\"number\" id=\"maxPriceInput\" style=\"height:43px;\" class=\"form-control\" name=\"max\" value=\"" . $serviceInfo["service_max"] . "\" ";
                                                    if ($serviceInfo["sync_max"] == 1) {
                                                        $return .= "readonly";
                                                    }
                                                    $return .= ">\r\n                            <div class=\"input-group-addon\" >\r\n                            <label class=\"switch\"><input id=\"maxPriceCheckbox\"  type=\"checkbox\" name=\"auto_max\" ";
                                                    if ($serviceInfo["sync_max"] == 1) {
                                                        $return .= "checked";
                                                    }
                                                    $return .= "/>\r\n                            <span   class=\"slider round\"></span>\r\n                        </label>\r\n                    </div>\r\n                </div>\r\n              </div>\r\n              </div>\r\n              </div>\r\n\r\n    \r\n    \r\n    \r\n    ";
                                                }
                                                $return .= "\r\n    \r\n<hr>\r\n\r\n<div class=\"row\">\r\n<div class=\"form-group col-md-6\">\r\n<label>Botão de Cancelar</label>\r\n  <select class=\"form-control\" name=\"cancel_type\">\r\n      <option value=\"2\"";
                                                if ($serviceInfo["cancel_type"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Ativo</option>\r\n      <option value=\"1\"";
                                                if ($serviceInfo["cancel_type"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Desativado</option>\r\n  </select>\r\n</div>\r\n\r\n\r\n<div class=\"form-group col-md-6\">\r\n<label>Botão de Refill</label>\r\n  <select class=\"form-control\" id=\"refill\" name=\"refill_type\">\r\n      <option value=\"2\"";
                                                if ($serviceInfo["refill_type"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Ativo</option>\r\n      <option value=\"1\"";
                                                if ($serviceInfo["refill_type"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Desativado</option>\r\n  </select>\r\n</div>\r\n</div>\r\n\r\n<div id=\"refill_day\" class=\"form-group\">\r\n<label>Refill Maximum Day <small>(If lifetime, write 0)</small></label>\r\n  <input type=\"number\" class=\"form-control\" name=\"refill_time\" value=\"" . $serviceInfo["refill_time"] . "\">\r\n</div>\r\n\r\n\r\n              <hr>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Link do pedido<small>(Mostrado na nova página do pedido)</small></label>\r\n                  <select class=\"form-control\" name=\"want_username\">\r\n                      <option value=\"1\"";
                                                if ($serviceInfo["want_username"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Link</option>\r\n                      <option value=\"2\"";
                                                if ($serviceInfo["want_username"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Nome de usuário</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Serviço pessoal <small>(Apenas as pessoas que você escolher podem ver)</small></label>\r\n                  <select class=\"form-control\" name=\"secret\">\r\n                      <option value=\"2\"";
                                                if ($serviceInfo["service_secret"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Não</option>\r\n                      <option value=\"1\"";
                                                if ($serviceInfo["service_secret"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Sim</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Velocidade de serviço <small>(Exibido como símbolo e cor na lista de serviço)</small></label>\r\n                  <select class=\"form-control\" name=\"speed\">\r\n                      <option value=\"1\"";
                                                if ($serviceInfo["service_speed"] == 1) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Lento</option>\r\n                      <option value=\"2\"";
                                                if ($serviceInfo["service_speed"] == 2) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Às vezes Lento</option>\r\n                      <option value=\"3\"";
                                                if ($serviceInfo["service_speed"] == 3) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Normal</option>\r\n                      <option value=\"4\"";
                                                if ($serviceInfo["service_speed"] == 4) {
                                                    $return .= "selected";
                                                }
                                                $return .= ">Rápido</option>\r\n                  </select>\r\n                </div>\r\n              </div>\r\n\r\n            </div>\r\n\r\n              <div class=\"modal-footer\">\r\n                <button type=\"submit\" class=\"btn btn-primary\">Atualizar informações do serviço</button>\r\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n              </div>\r\n              </form>\r\n              <script type=\"text/javascript\">\r\n\r\n              var type = \$(\"#refill\").val();\r\n\r\n              if( type == 1 ){\r\n    \r\n                \$(\"#refill_day\").hide();\r\n    \r\n              } else{\r\n    \r\n                \$(\"#refill_day\").show();\r\n    \r\n              }\r\n    \r\n              \$(\"#refill\").change(function(){\r\n    \r\n                var type = \$(this).val();\r\n    \r\n                  if( type == 1 ){\r\n    \r\n                    \$(\"#refill_day\").hide();\r\n    \r\n                  } else{\r\n    \r\n                    \$(\"#refill_day\").show();\r\n    \r\n                  }\r\n    \r\n              });\r\n       \r\n              /* It is a minprice checkbox event. When it is clicked, the minpriceinput is readonly. When it is checked, the input remains open when it is not.*/\r\n               \$(\"#minPriceCheckbox\").click(function(){\r\n                    var minPriceInput = \$(\"#minPriceInput\");  \r\n                    var minText = \$(\"#minText\");\r\n                   if(!this.checked){\r\n                    minPriceInput.removeAttr(\"readonly\",\"readonly\");\r\n                   }else{\r\n                    minPriceInput.attr(\"readonly\",\"readonly\");\r\n                    minPriceInput.val(minText.text());\r\n                   }\r\n                });\r\n       \r\n       \r\n              /* Maxprice is a checkbox event. When it is clicked, the minpriceinput is readonly. When it is checked, the input remains open when it is not checked. */\r\n               \$(\"#maxPriceCheckbox\").click(function(){\r\n                    var maxPriceInput = \$(\"#maxPriceInput\");  \r\n                    var maxText = \$(\"#maxText\");\r\n                   if(!this.checked){\r\n                    maxPriceInput.removeAttr(\"readonly\",\"readonly\");\r\n                   }else{\r\n                    maxPriceInput.attr(\"readonly\",\"readonly\");\r\n                    maxPriceInput.val(maxText.text());\r\n                   }\r\n                });\r\n\r\n       \r\n              /* Maxprice is a checkbox event. When it is clicked, the minpriceinput is readonly. When it is checked, the input remains open when it is not checked. */\r\n               \$(\"#priceCheckbox\").click(function(){\r\n                    var priceInput = \$(\"#priceInput\");  \r\n                    var priceThree = \$(\"#priceThreeInput\");\r\n                   if(this.checked){\r\n                        priceInput.css(\"display\",\"none\");\r\n                        priceThree.css(\"display\",\"block\");\r\n                   }else{\r\n                        priceThree.css(\"display\",\"none\");\r\n                        priceInput.css(\"display\",\"block\");\r\n                   }\r\n                });\r\n                \r\n               \$(\".other_services\").click(function(){\r\n                 var control = \$(\"#translationsList\");\r\n                 if( control.attr(\"class\") == \"hidden\" ){\r\n                   control.removeClass(\"hidden\");\r\n                 } else{\r\n                   control.addClass(\"hidden\");\r\n                 }\r\n               });\r\n              var site_url  = \$(\"head base\").attr(\"href\");\r\n                \$(\"#provider\").change(function(){\r\n                  var provider = \$(this).val();\r\n                  getProviderServices(provider,site_url);\r\n                });\r\n\r\n                getProvider();\r\n                \$(\"#serviceMode\").change(function(){\r\n                  getProvider();\r\n                });\r\n\r\n                getSalePrice();\r\n                \$(\"#saleprice_cal\").change(function(){\r\n                  getSalePrice();\r\n                });\r\n\r\n                getSubscription();\r\n                \$(\"#subscription_package\").change(function(){\r\n                  getSubscription();\r\n                });\r\n                function getProviderServices(provider,site_url){\r\n                  if( provider == 0 ){\r\n                    \$(\"#provider_service\").hide();\r\n                  }else{\r\n                    \$.post(site_url+\"admin/ajax_data\",{action:\"providers_list\",provider:provider}).done(function( data ) {\r\n                      \$(\"#provider_service\").show();\r\n                      \$(\"#provider_service\").html(data);\r\n                    }).fail(function(){\r\n                      alert(\"An error occurred!\");\r\n                    });\r\n                  }\r\n                }\r\n\r\n                function getProvider(){\r\n                  var mode = \$(\"#serviceMode\").val();\r\n                    if( mode == 1 ){\r\n                      \$(\"#autoMode\").hide();\r\n                    }else{\r\n                      \$(\"#autoMode\").show();\r\n                    }\r\n                }\r\n\r\n                function getSalePrice(){\r\n                  var type = \$(\"#saleprice_cal\").val();\r\n                    if( type == \"normal\" ){\r\n                      \$(\"#saleprice\").hide();\r\n                      \$(\"#servicePrice\").show();\r\n                    }else{\r\n                      \$(\"#saleprice\").show();\r\n                      \$(\"#servicePrice\").hide();\r\n                    }\r\n                }\r\n\r\n                function getSubscription(){\r\n                  var type = \$(\"#subscription_package\").val();\r\n                    if( type == \"11\" || type == \"12\" ){\r\n                      \$(\"#unlimited\").show();\r\n                      \$(\"#limited\").hide();\r\n                    }else{\r\n                      \$(\"#unlimited\").hide();\r\n                      \$(\"#limited\").show();\r\n                    }\r\n                }\r\n              </script>\r\n              ";
                                                echo json_encode(["content" => $return, "title" => "Editar serviço (ID: " . $serviceInfo["service_id"] . ")"]);
                                            }
                                        } else {
                                            if ($action == "edit_description") {
                                                $id = $_POST["id"];
                                                $smmapi = new SMMApi();
                                                $serviceInfo = $conn->prepare("SELECT * FROM services WHERE service_id=:id ");
                                                $serviceInfo->execute(["id" => $id]);
                                                $serviceInfo = $serviceInfo->fetch(PDO::FETCH_ASSOC);
                                                $multiDesc = json_decode($serviceInfo["description_lang"], true);
                                                $return = "<form class=\"form\" action=\"" . site_url("admin/services/edit-description/" . $serviceInfo["service_id"]) . "\" method=\"post\" data-xhr=\"true\">\r\n            <div class=\"modal-body\">";
                                                if (1 < count($languages)) {
                                                    $translationList = "<a class=\"other_services\"> Translations (" . (count($languages) - 1) . ") </a>";
                                                } else {
                                                    $translationList = "";
                                                }
                                                foreach ($languages as $language) {
                                                    if ($language["default_language"]) {
                                                        $return .= "<div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Explicação <span class=\"badge\">" . $language["language_name"] . "</span> " . $translationList . " </label>\r\n                    <textarea class=\"form-control\" rows=\"5\" name=\"description[" . $language["language_code"] . "]\">" . $multiDesc[$language["language_code"]] . "</textarea>\r\n                  </div>";
                                                        if (1 < count($languages)) {
                                                            $return .= "<div class=\"hidden\" id=\"translationsList\">";
                                                        }
                                                    } else {
                                                        $return .= "<div class=\"form-group\">\r\n                    <label class=\"form-group__service-name\">Explicação <span class=\"badge\">" . $language["language_name"] . "</span> </label>\r\n                    <textarea class=\"form-control\" rows=\"5\"  name=\"description[" . $language["language_code"] . "]\">" . $multiDesc[$language["language_code"]] . "</textarea>\r\n                  </div>";
                                                    }
                                                }
                                                if (1 < count($languages)) {
                                                    $return .= "</div>";
                                                }
                                                $return .= "\r\n\r\n            </div>\r\n\r\n              <div class=\"modal-footer\">\r\n                <button type=\"submit\" class=\"btn btn-primary\">Atualizar descrição</button>\r\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n              </div>\r\n              </form>\r\n              <script type=\"text/javascript\">\r\n\r\n              \$(\".other_services\").click(function(){\r\n                var control = \$(\"#translationsList\");\r\n                if( control.attr(\"class\") == \"hidden\" ){\r\n                  control.removeClass(\"hidden\");\r\n                } else{\r\n                  control.addClass(\"hidden\");\r\n                }\r\n              });\r\n\r\n              </script>\r\n              ";
                                                echo json_encode(["content" => $return, "title" => "Editar Descrição (ID:" . $serviceInfo["service_id"] . ")"]);
                                            } else {
                                                if ($action == "new_subscriptions") {
                                                    $categories = $conn->prepare("SELECT * FROM categories ORDER BY category_line ");
                                                    $categories->execute([]);
                                                    $categories = $categories->fetchAll(PDO::FETCH_ASSOC);
                                                    $providers = $conn->prepare("SELECT * FROM service_api");
                                                    $providers->execute([]);
                                                    $providers = $providers->fetchAll(PDO::FETCH_ASSOC);
                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/services/new-subscription") . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">";
                                                    if (1 < count($languages)) {
                                                        $translationList = "<a class=\"other_services\"> Translations (" . (count($languages) - 1) . ") </a>";
                                                    } else {
                                                        $translationList = "";
                                                    }
                                                    foreach ($languages as $language) {
                                                        if ($language["default_language"]) {
                                                            $return .= "<div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> " . $translationList . " </label>\r\n              <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n            </div>";
                                                            if (1 < count($languages)) {
                                                                $return .= "<div class=\"hidden\" id=\"translationsList\">";
                                                            }
                                                        } else {
                                                            $return .= "<div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Nome do serviço <span class=\"badge\">" . $language["language_name"] . "</span> </label>\r\n              <input type=\"text\" class=\"form-control\" name=\"name[" . $language["language_code"] . "]\" value=\"" . $multiName[$language["language_code"]] . "\">\r\n            </div>";
                                                        }
                                                    }
                                                    if (1 < count($languages)) {
                                                        $return .= "</div>";
                                                    }
                                                    $return .= "<div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Categoria de serviço</label>\r\n              <select class=\"form-control\" name=\"category\">\r\n                    <option value=\"0\">Selecione a categoria..</option>";
                                                    foreach ($categories as $category) {
                                                        $return .= "<option value=\"" . $category["category_id"] . "\">" . $category["category_name"] . "</option>";
                                                    }
                                                    $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Tipo de assinatura</label>\r\n              <select class=\"form-control\" name=\"package\" id=\"subscription_package\">\r\n                    <option value=\"11\">Instagram Auto Likes - Unlimited</option>\r\n                    <option value=\"12\">Instagram Auto Views - Unlimited</option>\r\n                    <option value=\"11\">Instagram AutoSave - Unlimited</option>\r\n                    <option value=\"11\">Instagram Auto Interaction - Unlimited</option>\r\n                    <option value=\"14\">Instagram Auto Like - Timed</option>\r\n                    <option value=\"15\">Instagram Auto Watch - Timed</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__wrapper\">\r\n\r\n            <div class=\"service-mode__block\">\r\n              <div class=\"form-group\">\r\n              <label>Modo</label>\r\n                <select class=\"form-control\" name=\"mode\" id=\"serviceMode\">\r\n                      <option value=\"2\">Automático (API)</option>\r\n                  </select>\r\n              </div>\r\n            </div>\r\n\r\n            <div id=\"autoMode\" style=\"display: none\">\r\n              <div class=\"service-mode__block\">\r\n                <div class=\"form-group\">\r\n                <label>Provedor de Serviços</label>\r\n                  <select class=\"form-control\" name=\"provider\" id=\"provider\">\r\n                        <option value=\"0\">Selecione o provedor de serviços...</option>";
                                                    foreach ($providers as $provider) {
                                                        $return .= "<option value=\"" . $provider["id"] . "\">" . $provider["api_name"] . "</option>";
                                                    }
                                                    $return .= "</select>\r\n                </div>\r\n              </div>\r\n              <div id=\"provider_service\">\r\n              </div>\r\n            </div>\r\n          </div>\r\n\r\n          <div id=\"unlimited\">\r\n            <div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Preço do serviço (1000 unidades)</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"price\" value=\"\">\r\n            </div>\r\n\r\n            <div class=\"row\">\r\n              <div class=\"col-md-6 form-group\">\r\n                <label class=\"form-group__service-name\">Pedido mínimo</label>\r\n                <input type=\"text\" class=\"form-control\" name=\"min\" value=\"\">\r\n              </div>\r\n\r\n              <div class=\"col-md-6 form-group\">\r\n                <label class=\"form-group__service-name\">Pedido máximo</label></label>\r\n                <input type=\"text\" class=\"form-control\" name=\"max\" value=\"\">\r\n              </div>\r\n            </div>\r\n          </div>\r\n\r\n          <div id=\"limited\">\r\n            <div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">service price</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"limited_price\" value=\"\">\r\n            </div>\r\n\r\n\r\n\r\n            <div class=\"row\">\r\n              <div class=\"col-md-6 form-group\">\r\n                <label class=\"form-group__service-name\">Shipment amount</label>\r\n                <input type=\"text\" class=\"form-control\" name=\"autopost\" value=\"\">\r\n              </div>\r\n\r\n              <div class=\"col-md-6 form-group\">\r\n                <label class=\"form-group__service-name\">Order amount</label>\r\n                <input type=\"text\" class=\"form-control\" name=\"limited_min\" value=\"\">\r\n              </div>\r\n            </div>\r\n            <div class=\"form-group\">\r\n              <label class=\"form-group__service-name\">Package Time <small>(day)</small></label>\r\n              <input type=\"text\" class=\"form-control\" name=\"autotime\" value=\"\">\r\n            </div>\r\n          </div>\r\n\r\n          <hr>\r\n\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Atendimento Personalizado (Apenas as pessoas que você escolher podem ver.)</label>\r\n              <select class=\"form-control\" name=\"secret\">\r\n                  <option value=\"2\">Não</option>\r\n                  <option value=\"1\">Sim</option>\r\n              </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n           <label>Velocidade do serviço</label>\r\n              <select class=\"form-control\" name=\"speed\">\r\n                  <option value=\"1\">Lento</option>\r\n                  <option value=\"2\">Às vezes Lento</option>\r\n                  <option value=\"3\">Normal</option>\r\n                  <option value=\"4\">Rápido</option>\r\n              </select>\r\n            </div>\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Adicionar nova assinatura</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>\r\n          <script type=\"text/javascript\">\r\n\r\n          \$(\".other_services\").click(function(){\r\n            var control = \$(\"#translationsList\");\r\n            if( control.attr(\"class\") == \"hidden\" ){\r\n              control.removeClass(\"hidden\");\r\n            } else{\r\n              control.addClass(\"hidden\");\r\n            }\r\n          });\r\n\r\n          var site_url  = \$(\"head base\").attr(\"href\");\r\n            \$(\"#provider\").change(function(){\r\n              var provider = \$(this).val();\r\n              getProviderServices(provider,site_url);\r\n            });\r\n\r\n            getProvider();\r\n            \$(\"#serviceMode\").change(function(){\r\n              getProvider();\r\n            });\r\n\r\n            getSalePrice();\r\n            \$(\"#saleprice_cal\").change(function(){\r\n              getSalePrice();\r\n            });\r\n\r\n            getSubscription();\r\n            \$(\"#subscription_package\").change(function(){\r\n              getSubscription();\r\n            });\r\n            function getProviderServices(provider,site_url){\r\n              if( provider == 0 ){\r\n                \$(\"#provider_service\").hide();\r\n              }else{\r\n                \$.post(site_url+\"admin/ajax_data\",{action:\"providers_list\",provider:provider}).done(function( data ) {\r\n                  \$(\"#provider_service\").show();\r\n                  \$(\"#provider_service\").html(data);\r\n                }).fail(function(){\r\n                  alert(\"An error occurred!\");\r\n                });\r\n              }\r\n            }\r\n\r\n            function getProvider(){\r\n              var mode = \$(\"#serviceMode\").val();\r\n                if( mode == 1 ){\r\n                  \$(\"#autoMode\").hide();\r\n                }else{\r\n                  \$(\"#autoMode\").show();\r\n                }\r\n            }\r\n\r\n            function getSalePrice(){\r\n              var type = \$(\"#saleprice_cal\").val();\r\n                if( type == \"normal\" ){\r\n                  \$(\"#saleprice\").hide();\r\n                  \$(\"#servicePrice\").show();\r\n                }else{\r\n                  \$(\"#saleprice\").show();\r\n                  \$(\"#servicePrice\").hide();\r\n                }\r\n            }\r\n\r\n            function getSubscription(){\r\n              var type = \$(\"#subscription_package\").val();\r\n                if( type == \"11\" || type == \"12\" ){\r\n                  \$(\"#unlimited\").show();\r\n                  \$(\"#limited\").hide();\r\n                }else{\r\n                  \$(\"#unlimited\").hide();\r\n                  \$(\"#limited\").show();\r\n                }\r\n            }\r\n          </script>\r\n          ";
                                                    echo json_encode(["content" => $return, "title" => "Adicionar nova assinatura"]);
                                                } else {
                                                    if ($action == "new_category") {
                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/services/new-category") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome da categoria</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Categoria pessoal (Apenas as pessoas que você escolher podem vê-la.)</label>\r\n              <select class=\"form-control\" name=\"secret\">\r\n                    <option value=\"2\">Não</option>\r\n                    <option value=\"1\">Sim</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Criar categoria</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                        echo json_encode(["content" => $return, "title" => "Criar nova categoria"]);
                                                    } else {
                                                        if ($action == "edit_category") {
                                                            $id = $_POST["id"];
                                                            $category = $conn->prepare("SELECT * FROM categories WHERE category_id=:id ");
                                                            $category->execute(["id" => $id]);
                                                            $category = $category->fetch(PDO::FETCH_ASSOC);
                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/services/edit-category/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome da categoria</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $category["category_name"] . "\">\r\n          </div>\r\n \r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Categoria pessoal (Apenas as pessoas que você escolher podem vê-la.)</label>\r\n              <select class=\"form-control\" name=\"secret\">\r\n                    <option value=\"2\"";
                                                            if ($category["category_secret"] == 2) {
                                                                $return .= "selected";
                                                            }
                                                            $return .= ">Não</option>\r\n                    <option value=\"1\"";
                                                            if ($category["category_secret"] == 1) {
                                                                $return .= "selected";
                                                            }
                                                            $return .= ">Sim</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar categoria</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                            echo json_encode(["content" => $return, "title" => "Editar categoria (ID: " . $id . ")"]);
                                                        } else {
                                                            if ($action == "import_services") {
                                                                $providers = $conn->prepare("SELECT * FROM service_api");
                                                                $providers->execute([]);
                                                                $providers = $providers->fetchAll(PDO::FETCH_ASSOC);
                                                                $category = $conn->prepare("SELECT * FROM categories");
                                                                $category->execute([]);
                                                                $category = $category->fetchAll(PDO::FETCH_ASSOC);
                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/services/get_services_add/") . "\" method=\"post\" data-xhr=\"true\">\r\n    \r\n        <div class=\"modal-body\">\r\n\r\n          <div id=\"firstStep\">\r\n            <div class=\"service-mode__block\">\r\n              <div class=\"form-group\">\r\n              <label>Provedor de Serviços</label>\r\n                <select class=\"form-control\" name=\"provider\" id=\"provider\">\r\n                      <option value=\"0\">Selecione o provedor de serviços...</option>";
                                                                foreach ($providers as $provider) {
                                                                    $return .= "<option value=\"" . $provider["id"] . "\">" . $provider["api_name"] . "</option>";
                                                                }
                                                                $return .= "</select>\r\n              </div>\r\n            </div>\r\n            <div class=\"service-mode__block\">\r\n              <div class=\"form-group\">\r\n              <label>Selecione a categoria para adicionar os serviços</label>\r\n                <select class=\"form-control\" name=\"selector\" id=\"selector\">\r\n                      <option value=\"0\">Escolha a categoria...</option>";
                                                                foreach ($category as $cat) {
                                                                    $return .= "<option value=\"" . $cat["category_id"] . "\">" . $cat["category_name"] . "</option>";
                                                                }
                                                                $return .= "</select>\r\n              </div>\r\n            </div>\r\n          </div>\r\n\r\n          \r\n          <div id=\"secondStep\">\r\n          </div>\r\n\r\n          <div id=\"thirdStep\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n            <button type=\"button\" class=\"btn btn-primary\" id=\"nextStep\" data-step=\"first\">Próxima etapa</button>\r\n            <button type=\"submit\" class=\"btn btn-primary\" id=\"submitStep\">Adicionar serviços</button>\r\n          </div>\r\n\r\n        </form>\r\n           <script>\r\n            \$(\"#submitStep\").hide();\r\n            \$(\"#nextStep\").click(function(){\r\n              var now_step = \$(this).attr(\"data-step\");\r\n              var provider = \$(\"#provider\").val();\r\n              var category = \$(\"#selector\").val();\r\n              \$(\"#secondStep\").hide();\r\n                if( now_step == \"first\" ){\r\n                \r\n                  if( provider == 0 || category == 0  ){\r\n                    \$.toast({\r\n                        heading: \"Malsucedido\",\r\n                        text: \"Por favor, não deixe espaço vazio\",\r\n                        icon: \"error\",\r\n                        loader: true,\r\n                        loaderBg: \"#9EC600\"\r\n                    });\r\n                  }\r\n                  \r\n                  else{\r\n                    \$(\"#firstStep\").hide();\r\n                    \$(\"#secondStep\").show();\r\n                    \$.post(\"admin/ajax_data\", {provider:provider,category:category,action:\"import_services_list\" }, function(data){\r\n                      \$(\"#secondStep\").html(data);\r\n                    });\r\n                    \$(\"#nextStep\").attr(\"data-step\",\"second\");\r\n                  }\r\n                }else if( now_step == \"second\" ){\r\n                    var array     = [];\r\n                       \$('[class^=\"selectServices-\"]').each(function () {\r\n                            var id    = \$(this).val();\r\n                            var check = \$(this).prop(\"checked\");\r\n                            var provider  =  \$(this).attr(\"data-provider\");\r\n                              if( check == true ){\r\n                                var params = {};\r\n                                params[\"id\"]            = id;\r\n                                params[\"category\"]      = \$(this).attr(\"data-category\");\r\n                                array.push(params);\r\n                              }\r\n                       });\r\n                       var count = array.length;\r\n                     if( count ){\r\n                       \$.post(\"admin/ajax_data\", {provider:provider,action:\"import_services_last\",services:array }, function(data){\r\n                         \$(\"#thirdStep\").html(data);\r\n                       });\r\n                       \$(\"#nextStep\").hide();\r\n                       \$(\"#submitStep\").show();\r\n                     }else{\r\n                       \$(\"#nextStep\").attr(\"data-step\",\"second\");\r\n                       \$(\"#firstStep\").hide();\r\n                       \$(\"#secondStep\").show();\r\n                       \$(\"#nextStep\").show();\r\n                       \$(\"#submitStep\").hide();\r\n                       \$.toast({\r\n                           heading: \"Malsucedido\",\r\n                           text: \"Selecione pelo menos um serviço que você gostaria de adicionar\",\r\n                           icon: \"error\",\r\n                           loader: true,\r\n                           loaderBg: \"#9EC600\"\r\n                       });\r\n                     }\r\n\r\n                }\r\n            });\r\n          </script>\r\n          ";
                                                                echo json_encode(["content" => $return, "title" => "Importar serviço sem categoria"]);
                                                            } else {
                                                                if ($action == "import_services_list") {
                                                                    $provider_id = $_POST["provider"];
                                                                    $category_id2 = $_POST["category"];
                                                                    $smmapi = new SMMApi();
                                                                    $provider = $conn->prepare("SELECT * FROM service_api WHERE id=:id");
                                                                    $provider->execute(["id" => $provider_id]);
                                                                    $provider = $provider->fetch(PDO::FETCH_ASSOC);
                                                                    if ($provider["api_type"] == 1) {
                                                                        
                                                                        $services = $smmapi->action(["key" => $provider["api_key"], "action" => "services"], $provider["api_url"]);
                                                                        if ($services) {
                                                                            $grouped = array_group_by($services, "category");
                                                                            echo "<div class=\"\">\r\n            <div class=\"majer\">\r\n                 <div>\r\n                    <div class=\"services-import__list-wrap\">\r\n                       <div class=\"services-import__scroll-wrap\">";
                                                                            foreach ($grouped as $category) {
                                                                                $category_id++;
                                                                                echo "\r\n                          <span>\r\n                             <div class=\"services-import__category\">\r\n                                <div class=\"services-import__category-title\">\r\n                                  <label><input type=\"checkbox\" data-id=\"" . $category_id . "\" id=\"checkAll-" . $category_id . "\">" . $category[0]->category . "\r\n                                   \r\n                                  </label>\r\n                                    <input type=\"hidden\" name=\"category\" value=\"" . $category_id2 . "\">\r\n                                </div>\r\n                             </div>\r\n                             <div class=\"services-import__packages\">\r\n                                <ul>";
                                                                                for ($i = 0; $i < count($category); $i++) {
                                                                                    echo "<li><label>\r\n                                  <input data-service=\"" . $category[$i]->name . "\" data-provider=\"" . $provider["id"] . "\"  data-category=\"" . $category_id . "\"  class=\"selectServices-" . $category_id . "\" type=\"checkbox\" value=\"" . $category[$i]->service . "\" name=\"services[]\">" . $category[$i]->service . " - " . $category[$i]->name . "<span class=\"services-import__packages-price\">" . priceFormat($category[$i]->rate) . "</span></label></li>";
                                                                                }
                                                                                echo "</ul>\r\n                             </div>\r\n                          </span>";
                                                                            }
                                                                            echo "\r\n                       </div>\r\n                    </div>\r\n                 </div>\r\n              </div>\r\n              <script>\r\n              \$('[id^=\"checkAll-\"]').click(function () {\r\n                var id = \$(this).attr(\"data-id\");\r\n                 if ( \$(this).prop(\"checked\") == true ) {\r\n                   \$(\".selectServices-\"+id).not(this).prop(\"checked\", true);\r\n                 }else{\r\n                   \$(\".selectServices-\"+id).not(this).prop(\"checked\", false);\r\n                 }\r\n               });\r\n              </script>\r\n              </div>";
                                                                        } else {
                                                                            echo "An error occurred, please try later.";
                                                                        }
                                                                    }
                                                                } 
                                                                
                                                                elseif($action == "import_service"){
                                                                    
                                                                    $providers  = $conn->prepare("SELECT * FROM service_api");
    $providers->execute([]);
    $providers  = $providers->fetchAll(PDO::FETCH_ASSOC);

      $category  = $conn->prepare("SELECT * FROM categories");
      $category->execute(array());
      $category  = $category->fetchAll(PDO::FETCH_ASSOC);
    
      $return = '<form class="form" action="'.site_url("admin/services/get_service_add/").'" method="post" data-xhr="true">
    
        <div class="modal-body">
          <div id="firstStep">
            <div class="service-mode__block">
              <div class="form-group">
              <label>Provedor de Serviços</label>
                <select class="form-control" name="provider" id="provider">
                      <option value="0">Selecione o provedor de serviços...</option>';
                      foreach( $providers as $provider ):
                        $return.='<option value="'.$provider["id"].'">'.$provider["api_name"].'</option>';
                      endforeach;
                    $return.='</select>
              </div>
            </div>
          </div>

          
          <div id="secondStep">
          </div>

          <div id="thirdStep">
          </div>


        </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" id="nextStep" data-step="first">Próxima etapa</button>
            <button type="submit" class="btn btn-primary" id="submitStep">Adicionar serviços</button>
          </div>

        </form>
           <script>
            $("#submitStep").hide();
            $("#nextStep").click(function(){
              var now_step = $(this).attr("data-step");
              var provider = $("#provider").val();
              var category = $("#selector").val();
              $("#secondStep").hide();
                if( now_step == "first" ){
                  if( provider == 0 ){
                    $.toast({
                        heading: "Malsucedido",
                        text: "Selecione o provedor de serviços",
                        icon: "error",
                        loader: true,
                        loaderBg: "#9EC600"
                    });
                  }else{
                    $("#firstStep").hide();
                    $("#secondStep").show();
                    $.post("admin/ajax_data", {provider:provider,category:category,action:"import_services_list" }, function(data){
                      $("#secondStep").html(data);
                    });
                    $("#nextStep").attr("data-step","second");
                  }
                }else if( now_step == "second" ){
                    var array     = [];
                       $(\'[class^="selectServices-"]\').each(function () {
                            var id    = $(this).val();
                            var check = $(this).prop("checked");
                            var provider  =  $(this).attr("data-provider");
                              if( check == true ){
                                var params = {};
                                params["id"]            = id;
                                params["category"]      = $(this).attr("data-category");
                                array.push(params);
                              }
                       });
                       var count = array.length;
                     if( count ){
                       $.post("admin/ajax_data", {provider:provider,action:"import_services_last",services:array }, function(data){
                         $("#thirdStep").html(data);
                       });
                       $("#nextStep").hide();
                       $("#submitStep").show();
                     }else{
                       $("#nextStep").attr("data-step","second");
                       $("#firstStep").hide();
                       $("#secondStep").show();
                       $("#nextStep").show();
                       $("#submitStep").hide();
                       $.toast({
                           heading: "Malsucedido",
                           text: "Selecione pelo menos um serviço que deseja adicionar",
                           icon: "error",
                           loader: true,
                           loaderBg: "#9EC600"
                       });
                     }

                }
            });
          </script>
          ';
    echo json_encode(["content"=>$return,"title"=>" importar Serviços junto com a categoria"]);
  
                                                                
                                                                    
                                                                    
                                                                }
                                                                
                                                        
                                                                else {
                                                                    if ($action == "import_services_last") {
                                                                        $provider_id = $_POST["provider"];
                                                                        $services = json_decode(json_encode($_POST["services"]));
                                                                        $smmapi = new SMMApi();
                                                                        $provider = $conn->prepare("SELECT * FROM service_api WHERE id=:id");
                                                                        $provider->execute(["id" => $provider_id]);
                                                                        $provider = $provider->fetch(PDO::FETCH_ASSOC);
                                                                        $apiServices = $smmapi->action(["key" => $provider["api_key"], "action" => "services"], $provider["api_url"]);
                                                                        $grouped = array_group_by($services, "category");
                                                                        echo "\r\n      <div class=\"majer\">\r\n             <div>\r\n                <div class=\"services-import__fields\">\r\n                   <div class=\"services-import__step3-field\">\r\n                      <div class=\"services-import__placeholder-title\">fixo (1,00)</div>\r\n                      <input type=\"number\" placeholder=\"0\" id=\"raise-fixed\" name=\"fixed\" value=\"\">\r\n                   </div>\r\n                   <div class=\"services-import__step3-plus\">+</div>\r\n                   <div class=\"services-import__step3-field\">\r\n                      <div class=\"services-import__placeholder-title\">percentage(%)</div>\r\n                      <input type=\"number\" placeholder=\"0\" id=\"raise-percent\" name=\"percent\" value=\"\">\r\n                   </div>\r\n                   <div class=\"services-import__step3-actions\"><span class=\"btn btn-default\">Redefinir cálculos</span></div>\r\n                </div>\r\n                \r\n                <div class=\"services-import__list-wrap services-import__list-active\">\r\n                   <div class=\"services-import__scroll-wrap\">";
                                                                        $category_id = 0;
                                                                        $c = 0;
                                                                        foreach ($grouped as $category) {
                                                                            foreach ($apiServices as $key => $value) {
                                                                                if ($category[$category_id]->id == $value->service) {
                                                                                    $categoryName = $value->category;
                                                                                }
                                                                            }
                                                                            $category_id = $category_id++;
                                                                            $c++;
                                                                            echo "<span class=\"providerCategory\" id=\"providerCategory-" . $c . "\">\r\n                           <div class=\"services-import__category\">\r\n                              <div class=\"services-import__category-title\"><label>" . $categoryName . "</label></div>\r\n                           </div>\r\n                           <div class=\"services-import__packages\">\r\n                              <ul>";
                                                                            for ($i = 0; $i < count($category); $i++) {
                                                                                foreach ($apiServices as $apiService) {
                                                                                    if ($apiService->service == $category[$i]->id) {
                                                                                        echo "<li id=\"providerService-" . $apiService->service . "\">\r\n                                         <label class=\"row\">        \r\n                                            <div class=\"col-md-8\">" . $apiService->service . " - " . $apiService->name . "</div>\r\n                                            <div class=\"col-md-3\">\r\n                                               <input style=\"\r\n                                                        width: 85px;\r\n                                                        border: solid 1px rgba(103,107,118,.5);\r\n                                                        padding-left: 0;\r\n                                                        padding-right: 22px;\r\n                                                        border-radius: 3px;\r\n                                                        background: 0 0;\r\n                                                        \" id=\"servicePriceCal" . $apiService->service . "\" type=\"text\" class=\"services-import__price\" data-rate=\"" . priceFormat($apiService->rate) . "\" data-service=\"" . $apiService->service . "\" name=\"servicesList[" . $apiService->service . "]\" value=\"" . priceFormat($apiService->rate) . "\">\r\n                                               <div class=\"services-import__packages-price-lock\" data-category=\"" . $c . "\"  data-id=\"servicedelete-" . $apiService->service . "\" data-service=\"" . $apiService->service . "\">\r\n                                                 <span class=\"fa fa-trash\"></span>\r\n                                               </div>\r\n                                               <div class=\"services-import__packages-price-lock\"  data-id=\"servicelock-" . $apiService->service . "\" data-service=\"" . $apiService->service . "\">\r\n                                                 <span class=\"fa fa-unlock\"></span>\r\n                                               </div>\r\n                                            </div>\r\n                                            <span class=\"services-import__provider-price\">" . priceFormat($apiService->rate) . "</span>\r\n                                         </label>\r\n                                      </li>";
                                                                                    }
                                                                                }
                                                                            }
                                                                            echo "</ul>\r\n                           </div>\r\n                        </span>";
                                                                        }
                                                                        echo "</div>     \r\n                </div>\r\n             </div>\r\n          </div>\r\n          <script>\r\n          function formatCurrency(total) {\r\n              var neg = false;\r\n              if(total < 0) {\r\n                  neg = true;\r\n                  total = Math.abs(total);\r\n              }\r\n              return parseFloat(total, 10).toFixed(2).replace(/(\\d)(?=(\\d{3})+\\.)/g, \"\$1,\").toString();\r\n          }\r\n          function sum(input){\r\n           if (toString.call(input) !== \"[object Array]\")\r\n              return false;\r\n\r\n                      var total =  0;\r\n                      for(var i=0;i<input.length;i++)\r\n                        {\r\n                          if(isNaN(input[i])){\r\n                          continue;\r\n                           }\r\n                      total += Number(input[i]);\r\n                   }\r\n             return total;\r\n            }\r\n          function chargeService(){\r\n            var add_fixed       = \$(\"#raise-fixed\").val();\r\n            var add_percent     = \$(\"#raise-percent\").val();\r\n            \$(\".services-import__price\").each(function(){\r\n              if( \$(this).attr(\"readonly\") != \"readonly\" ){\r\n                var rate        = \$(this).attr(\"data-rate\");\r\n                var service     = \$(this).attr(\"data-service\");\r\n                var total = sum([rate,(rate*add_percent/100),add_fixed]);\r\n                \$(\"#servicePriceCal\"+service).val(total);\r\n\r\n              }\r\n            });\r\n          }\r\n            \$('[data-id^=\"servicedelete-\"]').click(function(){\r\n              var id        = \$(this).attr(\"data-service\");\r\n              var category  = \$(this).attr(\"data-category\");\r\n              \$(\"li#providerService-\"+id).remove();\r\n                if( \$(\"#providerCategory-\"+category+\" > .services-import__packages > ul > li\").length == 0 ){\r\n                  \$(\"#providerCategory-\"+category).remove();\r\n                }\r\n            });\r\n            \$('[data-id^=\"servicelock-\"]').click(function(){\r\n              var service_id  = \$(this).attr(\"data-service\");\r\n              var lock        = \$(this).find(\"span\").attr(\"class\");\r\n              if( lock == \"fa fa-unlock\" ){\r\n                \$(this).find(\"span\").removeClass(\"fa fa-unlock\");\r\n                \$(this).find(\"span\").addClass(\"fa fa-lock\");\r\n                \$('[data-service=\"'+service_id+'\"]').attr(\"readonly\",true);\r\n              } else{\r\n                \$(this).find(\"span\").removeClass(\"fa fa-lock\");\r\n                \$(this).find(\"span\").addClass(\"fa fa-unlock\");\r\n                \$('[data-service=\"'+service_id+'\"]').attr(\"readonly\",false);\r\n              }\r\n            });\r\n\r\n            \$(\".services-import__step3-actions\").click(function(){\r\n              var add_fixed       = \$(\"#raise-fixed\").val(\"\");\r\n              var add_percent     = \$(\"#raise-percent\").val(\"\");\r\n              \$(\".services-import__price\").each(function(){\r\n                if( \$(this).attr(\"readonly\") != \"readonly\" ){\r\n                  var rate        = \$(this).attr(\"data-rate\");\r\n                  var service     = \$(this).attr(\"data-service\");\r\n                    \$(\"#servicePriceCal\"+service).val(rate);\r\n                }\r\n              });\r\n            });\r\n\r\n            \$(\"#raise-fixed\").on(\"keyup\", function(){\r\n              chargeService();\r\n            });\r\n\r\n            \$(\"#raise-percent\").on(\"keyup\", function(){\r\n              chargeService();\r\n            });\r\n\r\n          </script>\r\n          ";
                                                                    } else {
                                                                        if ($action == "price_providerCal") {
                                                                            $fixed = $_POST["fixed"];
                                                                            $percent = $_POST["percent"];
                                                                            $rate = $_POST["rate"];
                                                                            $total = $rate;
                                                                            if (is_numeric($percent) && 0 < $percent) {
                                                                                $total = $total + $rate * $percent / 100;
                                                                            }
                                                                            if (is_numeric($fixed) && 0 < $fixed) {
                                                                                $total = $total + $fixed;
                                                                            }
                                                                            echo $total;
                                                                        } else {
                                                                            if ($action == "new_ticket") {
                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/tickets/new") . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome de usuário</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"username\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Assunto</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"subject\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Sua mensagem</label>\r\n            <textarea class=\"form-control\" name=\"message\" rows=\"4\"></textarea>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Nova solicitação de suporte</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                echo json_encode(["content" => $return, "title" => "Nova solicitação de suporte"]);
                                                                            } else {
                                                                                if ($action == "edit_category") {
                                                                                    $id = $_POST["id"];
                                                                                    $subject = $conn->prepare("SELECT * FROM ticket_subject WHERE subject_id=:id ");
                                                                                    $subject->execute(["id" => $id]);
                                                                                    $category = $category->fetch(PDO::FETCH_ASSOC);
                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/services/edit-category/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome da categoria</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $category["category_name"] . "\">\r\n          </div>\r\n \r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Categoria pessoal (Apenas as pessoas que você escolher podem vê-la.)</label>\r\n              <select class=\"form-control\" name=\"secret\">\r\n                    <option value=\"2\"";
                                                                                    if ($category["category_secret"] == 2) {
                                                                                        $return .= "selected";
                                                                                    }
                                                                                    $return .= ">Não</option>\r\n                    <option value=\"1\"";
                                                                                    if ($category["category_secret"] == 1) {
                                                                                        $return .= "selected";
                                                                                    }
                                                                                    $return .= ">Sim</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Edit Support Title</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                    echo json_encode(["content" => $return, "title" => "Edit Support Title (ID:" . $id . ")"]);
                                                                                } else {
                                                                                    if ($action == "edit_paymentmethod" && $_POST["id"] == "paytr") {
                                                                                        $id = $_POST["id"];
                                                                                        $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                        $method->execute(["id" => $id]);
                                                                                        $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                        $extra = json_decode($method["method_extras"], true);
                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                        if ($method["method_type"] == 2) {
                                                                                            $return .= "selected";
                                                                                        }
                                                                                        $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                        if ($method["method_type"] == 1) {
                                                                                            $return .= "selected";
                                                                                        }
                                                                                        $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                        $return .= site_url("payment/" . $method["method_get"]);
                                                                                        $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant id</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_id\" value=\"" . $extra["merchant_id"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant key</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_key\" value=\"" . $extra["merchant_key"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant salt</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_salt\" value=\"" . $extra["merchant_salt"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                        echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                    } else {
                                                                                        if ($action == "edit_paymentmethod" && $_POST["id"] == "paytr_havale") {
                                                                                            $id = $_POST["id"];
                                                                                            $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                            $method->execute(["id" => $id]);
                                                                                            $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                            $extra = json_decode($method["method_extras"], true);
                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                            if ($method["method_type"] == 2) {
                                                                                                $return .= "selected";
                                                                                            }
                                                                                            $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                            if ($method["method_type"] == 1) {
                                                                                                $return .= "selected";
                                                                                            }
                                                                                            $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                            $return .= site_url("payment/paytr");
                                                                                            $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant id</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_id\" value=\"" . $extra["merchant_id"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant key</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_key\" value=\"" . $extra["merchant_key"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Merchant salt</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"merchant_salt\" value=\"" . $extra["merchant_salt"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                            echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                        } else {
                                                                                            if ($action == "edit_paymentmethod" && $_POST["id"] == "paywant") {
                                                                                                $id = $_POST["id"];
                                                                                                $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                $method->execute(["id" => $id]);
                                                                                                $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                $extra = json_decode($method["method_extras"], true);
                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                if ($method["method_type"] == 2) {
                                                                                                    $return .= "selected";
                                                                                                }
                                                                                                $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                if ($method["method_type"] == 1) {
                                                                                                    $return .= "selected";
                                                                                                }
                                                                                                $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                $return .= site_url("payment/" . $method["method_get"]);
                                                                                                $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiKey</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiKey\" value=\"" . $extra["apiKey"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiSecret</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiSecret\" value=\"" . $extra["apiSecret"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Paywant Commission</label>\r\n              <select class=\"form-control\" name=\"commissionType\">\r\n                    <option value=\"2\"";
                                                                                                if ($extra["commissionType"] == 2) {
                                                                                                    $return .= "selected";
                                                                                                }
                                                                                                $return .= ">Project to user</option>\r\n                    <option value=\"1\"";
                                                                                                if ($extra["commissionType"] == 1) {
                                                                                                    $return .= "selected";
                                                                                                }
                                                                                                $return .= ">Mirroring to the user</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label>Payment methods</label>\r\n              <div class=\"form-group col-md-12\">\r\n                  <div class=\"row\">\r\n                    <label class=\"checkbox-inline col-md-3\">\r\n                      <input type=\"checkbox\" class=\"access\" name=\"payment_type[]\" value=\"1\"";
                                                                                                if (in_array(1, $extra["payment_type"])) {
                                                                                                    $return .= " checked";
                                                                                                }
                                                                                                $return .= "> Mobil Payment\r\n                    </label>\r\n                    <label class=\"checkbox-inline col-md-3\">\r\n                      <input type=\"checkbox\" class=\"access\" name=\"payment_type[]\" value=\"2\"";
                                                                                                if (in_array(2, $extra["payment_type"])) {
                                                                                                    $return .= " checked";
                                                                                                }
                                                                                                $return .= "> Credit/Debit Card\r\n                    </label>\r\n                    <label class=\"checkbox-inline col-md-3\">\r\n                      <input type=\"checkbox\" class=\"access\" name=\"payment_type[]\" value=\"3\"";
                                                                                                if (in_array(3, $extra["payment_type"])) {
                                                                                                    $return .= " checked";
                                                                                                }
                                                                                                $return .= "> Wire Transfer/EFT\r\n                    </label>\r\n                  </div>\r\n              </div>\r\n            </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                            } else {
                                                                                                if ($action == "edit_paymentmethod" && $_POST["id"] == "coinpayments") {
                                                                                                    $id = $_POST["id"];
                                                                                                    $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                    $method->execute(["id" => $id]);
                                                                                                    $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                    $extra = json_decode($method["method_extras"], true);
                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                    if ($method["method_type"] == 2) {
                                                                                                        $return .= "selected";
                                                                                                    }
                                                                                                    $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                    if ($method["method_type"] == 1) {
                                                                                                        $return .= "selected";
                                                                                                    }
                                                                                                    $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                    $return .= site_url("payment/" . $method["method_get"]);
                                                                                                    $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Coinpayments Public Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"coinpayments_public_key\" value=\"" . $extra["coinpayments_public_key"] . "\">\r\n </div>\r\n\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Coinpayments Private Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"coinpayments_private_key\" value=\"" . $extra["coinpayments_private_key"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Coinpayments Crypto Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"coinpayments_currency\" value=\"" . $extra["coinpayments_currency"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Merchant ID</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"merchant_id\" value=\"" . $extra["merchant_id"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">IPN Secret</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"ipn_secret\" value=\"" . $extra["ipn_secret"] . "\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                    echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                } else {
                                                                                                    if ($action == "edit_paymentmethod" && $_POST["id"] == "2checkout") {
                                                                                                        $id = $_POST["id"];
                                                                                                        $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                        $method->execute(["id" => $id]);
                                                                                                        $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                        $extra = json_decode($method["method_extras"], true);
                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                        if ($method["method_type"] == 2) {
                                                                                                            $return .= "selected";
                                                                                                        }
                                                                                                        $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                        if ($method["method_type"] == 1) {
                                                                                                            $return .= "selected";
                                                                                                        }
                                                                                                        $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                        $return .= site_url("payment/" . $method["method_get"]);
                                                                                                        $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Seller ID</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"seller_id\" value=\"" . $extra["seller_id"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Private Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"private_key\" value=\"" . $extra["private_key"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"currency\" value=\"" . $extra["currency"] . "\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                        echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'easypaisa')) {
$id = $_POST['id'];
  $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
  $method->execute(['id' => $id]);
  $method = $method->fetch(PDO::FETCH_ASSOC);
  $extra = json_decode($method['method_extras'], true);
  $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

  if ($method['method_type'] == 2) {
    $return .= 'selected';
  }

  $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

  if ($method['method_type'] == 1) {
    $return .= 'selected';
  }

  $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
  $return .= site_url('payment/' . $method['method_get']);
  $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" .  '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Email</label>' . "\r\n" . '  <input type="text" class="form-control" name="email" value="' . $extra['email'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">App Pass</label>' . "\r\n" . '  <input type="text" class="form-control" name="pass" value="' . $extra['pass'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">How To Get App Password</label>' . "\r\n" . '  <input type="text" class="form-control" name="fees" value="Visit https://youtu.be/xhQxxZyhyis"disabled>' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
  
echo json_encode(['content' => $return, 'title' => '']);

}
    
    
    
    
    
    
    
                                                                                                        if ($action == "edit_paymentmethod" && $_POST["id"] == "cardlink") {
                                                                                                            $id = $_POST["id"];
                                                                                                            $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                            $method->execute(["id" => $id]);
                                                                                                            $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                            $extra = json_decode($method["method_extras"], true);
                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                            if ($method["method_type"] == 2) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                            if ($method["method_type"] == 1) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                            $return .= site_url("payment/" . $method["method_get"]);
                                                                                                            $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Shop ID</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"shop_id\" value=\"" . $extra["shop_id"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Private Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"private_key\" value=\"" . $extra["private_key"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"currency\" value=\"" . $extra["currency"] . "\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                            echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                        }
                                                                                                        if ($action == "edit_paymentmethod" && $_POST["id"] == "paypal") {
                                                                                                            $id = $_POST["id"];
                                                                                                            $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                            $method->execute(["id" => $id]);
                                                                                                            $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                            $extra = json_decode($method["method_extras"], true);
                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                            if ($method["method_type"] == 2) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                            if ($method["method_type"] == 1) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n     <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Modo</label>\r\n              <select class=\"form-control\" name=\"mode\">\r\n                    <option value=\"live\"";
                                                                                                            if ($extra["mode"] == "live") {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Live</option>\r\n                    <option value=\"test\"";
                                                                                                            if ($extra["mode"] == "test") {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Test</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                            $return .= site_url("payment/" . $method["method_get"]);
                                                                                                            $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Client Id</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"clientId\" value=\"" . $extra["clientId"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Client Secret</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"clientSecret\" value=\"" . $extra["clientSecret"] . "\">\r\n </div>\r\n \r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"currency\" value=\"" . $extra["currency"] . "\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                            echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                        }
                                                                                                    else {
                                                                                                        if ($action == "edit_paymentmethod" && $_POST["id"] == "paytm") {
                                                                                                            $id = $_POST["id"];
                                                                                                            $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                            $method->execute(["id" => $id]);
                                                                                                            $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                            $extra = json_decode($method["method_extras"], true);
                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                            if ($method["method_type"] == 2) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                            if ($method["method_type"] == 1) {
                                                                                                                $return .= "selected";
                                                                                                            }
                                                                                                            $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                            $return .= site_url("payment/" . $method["method_get"]);
                                                                                                            $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Merchant Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"merchant_key\" value=\"" . $extra["merchant_key"] . "\">\r\n </div>\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Merchant MID</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"merchant_mid\" value=\"" . $extra["merchant_mid"] . "\">\r\n </div>\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Merchant Website</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"merchant_website\" value=\"" . $extra["merchant_website"] . "\">\r\n </div>\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"currency\" value=\"INR\" readonly=\"\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                            echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                        } 
                                                                                                        
                                                                  elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'paytmqr')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Paytm QR Image Link</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_key" value="' . $extra['merchant_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant MID</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_mid" value="' . $extra['merchant_mid'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant Website</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_website" value="' . $extra['merchant_website'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '                        <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '                                  </div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);

}                                        
                                                                                elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'razorpay')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Public Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="public_key" value="' . $extra['public_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Key Secret</label>' . "\r\n" . '  <input type="text" class="form-control" name="key_secret" value="' . $extra['key_secret'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant Website</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_website" value="' . $extra['merchant_website'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
} 

    elseif( $action == "edit_paymentmethod" && $_POST["id"] == "webmoney" ){
      $id = $_POST['id'];
          $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
          $method->execute(['id' => $id]);
          $method = $method->fetch(PDO::FETCH_ASSOC);
          $extra = json_decode($method['method_extras'], true);
          $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';
      
          if ($method['method_type'] == 2) {
              $return .= 'selected';
          }
      
          $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';
      
          if ($method['method_type'] == 1) {
              $return .= 'selected';
          }
      
          $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
          $return .= site_url('payment/' . $method['method_get']);
          $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">WMID</label>' . "\r\n" . '  <input type="text" class="form-control" name="wmid" value="' . $extra['wmid'] . '">' . "\r\n".'<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">MERCHANT PURSE</label>' . "\r\n" . '  <input type="text" class="form-control" name="purse" value="' . $extra['purse'] . '">' . "\r\n" . ' </div>'. "\r\n".'<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Secret key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">' . "\r\n" . ' </div>'
          .'<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">COMMISSION</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . ' </div>' . "\r\n\r\n\r\n" . '</div>'. ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
          echo json_encode(['content' => $return, 'title' => '']);
                                                                                                    }




elseif( $action == "edit_paymentmethod" && $_POST["id"] == "payeer" ){
 $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

   
 if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

  $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Account</label>' . "\r\n" . '  <input type="text" class="form-control" name="account" value="' . $extra['account'] . '">'. '  <label class="form-group__service-name">Client Secret</label>' . "\r\n" . '  <input type="text" class="form-control" name="client_secret" value="' . $extra['client_secret'] . '">'. '  <label class="form-group__service-name">User id</label>' . "\r\n" . '  <input type="text" class="form-control" name="user_id" value="' . $extra['user_id'] . '">'. '  <label class="form-group__service-name">User pass</label>' . "\r\n" . '  <input type="text" class="form-control" name="user_pass" value="' . $extra['user_pass'] . '">' .'<label class="form-group__service-name">M Shop</label>' . "\r\n" . '  <input type="text" class="form-control" name="m_shop" value="' . $extra['m_shop'] . '">'  . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '                           <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '                                </div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    
    echo json_encode(['content' => $return, 'title' => '']);
}





elseif( $action == "edit_paymentmethod" && $_POST["id"] == "gbprimepay" ){
    $id = $_POST['id'];
  $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
  $method->execute(['id' => $id]);
  $method = $method->fetch(PDO::FETCH_ASSOC);
  $extra = json_decode($method['method_extras'], true);
  $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

  if ($method['method_type'] == 2) {
    $return .= 'selected';
  }

  $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

  if ($method['method_type'] == 1) {
    $return .= 'selected';
  }

  $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
  $return .= site_url('payment/' . $method['method_get']);
  $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" .  '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Token(Customer key)</label>' . "\r\n" . '  <input type="text" class="form-control" name="token" value="' . $extra['token'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
      echo json_encode(['content' => $return, 'title' => '']);
}





elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'flutterwave')) {
$id = $_POST['id'];
$method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
$method->execute(['id' => $id]);
$method = $method->fetch(PDO::FETCH_ASSOC);
$extra = json_decode($method['method_extras'], true);
$return = '
<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">
   ' . "\r\n\r\n" . '
   <div class="modal-body">
      ' . "\r\n\r\n" . ' 
      <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>
      ' . "\r\n\r\n" . ' 
      <div class="service-mode__block">
         ' . "\r\n" . '  
         <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';
            if ($method['method_type'] == 2) {
            $return .= 'selected';
            }
            $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';
            if ($method['method_type'] == 1) {
            $return .= 'selected';
            }
            $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  
         </div>
         ' . "\r\n" . ' 
      </div>
      ' . "\r\n\r\n" . ' 
      <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>
      ' . "\r\n\r\n" . ' 
       <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '" >' . "\r\n" . ' </div>
      ' . "\r\n\r\n" . ' 
      <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>
      ' . "\r\n\r\n" . ' 
      <hr>
      ' . "\r\n" . '  
      <p class="card-description">' . "\r\n" . '
      <ul>
         ' . "\r\n" . '
         <li>' . "\r\n" . ' API callback address: <code>';
            $return .= site_url('payment/' . $method['method_get']);
            $return .= '</code>' . "\r\n" . '
         </li>
         ' . "\r\n" . '
      </ul>
      ' . "\r\n" . '  </p>' . "\r\n" . ' 
      <hr>
      ' . "\r\n\r\n" . ' 
      <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Public Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="public_key" value="' . $extra['public_key'] . '">' . "\r\n" . ' </div>
      ' . "\r\n" . ' 
     
      
      <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Currency</label>' . "\r\n" . '  <input type="text" class="form-control" name="currency" value="' . $extra['currency'] . '">' . "\r\n" . ' </div>
      ' . "\r\n\r\n\r\n" . '
   </div>
   ' . "\r\n\r\n" . ' 
   <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>
   ' . "\r\n" . ' 
</form>
';
echo json_encode(['content' => $return, 'title' => '']);
}





elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'toyyibpay')) {   $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Secret_Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Category_Code</label>' . "\r\n" . '  <input type="text" class="form-control" name="category_code" value="' . $extra['category_code'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant Website</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_website" value="' . $extra['merchant_website'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
   echo json_encode(['content' => $return, 'title' => '']);
}





elseif( $action == "edit_paymentmethod" && $_POST["id"] == "mollie" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($extra['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($extra['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Api key</label>' . "\r\n" . '  <input type="text" class="form-control" name="api_key" value="' . $extra['api_key'] . '">'.  '  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
} 









elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'mercadopago')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativado</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Desativado</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento minimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' Endereço de retorno de API: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Acesso Token (Produção)</label>' . "\r\n" . '  <input type="text" class="form-control" name="live_access_token" value="' . $extra['live_access_token'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Comissão, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Moeda</label>' . "\r\n" . '  <input type="text" class="form-control" name="currency" value="' . $extra['currency'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
	
	
	
}
elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'pix_auto3')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="eXPay Brasil">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visivel</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Desativado</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . ' ' . "\r\n" . '  <input hidden type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Minimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Maximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' Cadastre-se na EXPAY BRASIL para receber uma Merchant Key: <code>';
    $return .= ('https://expaybrasil.com/pt/register');
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . ' ' . "\r\n" . '  <input hidden type="text" class="form-control" name="passphrase" value="' . $extra['passphrase'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  ' . "\r\n" . '  <input type="text" hidden class="form-control" name="usd" value="' . $extra['usd'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_website" value="' . $extra['merchant_website'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Bônus, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Update</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
    
}
    
elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'pix_auto')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativado</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Desativado</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Visible name</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento minimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '' . "\r\n" . '' . "\r\n" . ' ';
    //$return .= site_url('payment/' . $method['method_get']);
    $return .= '' . "\r\n" . '' . "\r\n" . '' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '' . "\r\n" . ' ' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Access Token Mercado Pago</label>' . "\r\n" . '  <input type="text" class="form-control" name="access_token" value="' . $extra['access_token'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . ' ' . "\r\n" . ' ' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Comissão, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'pix_auto_pg')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Active</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }
} 





	
elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'pix_auto2')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativado</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Desativado</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Visible name</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento minimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '' . "\r\n" . '' . "\r\n" . ' ';
    //$return .= site_url('payment/' . $method['method_get']);
    $return .= '' . "\r\n" . '' . "\r\n" . '' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '' . "\r\n" . ' ' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Access Token Mercado Pago</label>' . "\r\n" . '  <input type="text" class="form-control" name="access_token" value="' . $extra['access_token'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . ' ' . "\r\n" . ' ' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Comissão, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'pix_auto_pg')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Active</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }
} 


















elseif( $action == "yeni_kupon" ){
    $return = '<form class="form" action="'.site_url("admin/kuponlar/new").'" method="post" data-xhr="true">
        <div class="modal-body">

          <div class="form-group">
            <label class="form-group__service-name">Coupon Code</label>
            <input type="text" class="form-control" name="kuponadi" value="">
          </div>

          <div class="form-group">
            <label class="form-group__service-name">Piece</label>
            <input type="text" class="form-control" name="adet" value="">
          </div>

          <div class="form-group">
            <label class="form-group__service-name">Valor</label>
            <input type="text" class="form-control" name="tutar" value="">
          </div>


        </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Create new coupon</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
          </form>
          ';
    echo json_encode(["content"=>$return,"title"=>"Create new coupon"]);}
    
    
    



elseif( $action == "edit_paymentmethod" && $_POST["id"] == "wish_money" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="mode">' . "\r\n" . '  <option value="test"';

    if ($extra['mode'] == 'test') {
        $return .= 'selected';
    }

    $return .= '>Test</option>' . "\r\n" . '  <option value="live"';

    if ($extra['mode'] == 'live') {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Channel</label>' . "\r\n" . '  <input type="text" class="form-control" name="channel" value="' . $extra['channel'] . '">'. '  <label class="form-group__service-name"> Secret</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret" value="' . $extra['secret'] . '">'. '  <label class="form-group__service-name">Website</label>' . "\r\n" . '  <input type="text" class="form-control" name="website" value="' . $extra['website'] . '">'. '  <label class="form-group__service-name">Fee</label>' . "\r\n" . '  <input type="number" class="form-control" name="fee" value="' . $extra['fee'] . '">'.  "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}
elseif( $action == "edit_paymentmethod" && $_POST["id"] == "opay" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($method['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($method['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant id</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_id" value="' . $extra['merchant_id'] . '">'. '  <label class="form-group__service-name"> Secret Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">'. '  <label class="form-group__service-name">Public key</label>' . "\r\n" . '  <input type="text" class="form-control" name="public_key" value="' . $extra['public_key'] . '">'. '  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}


elseif( $action == "edit_paymentmethod" && $_POST["id"] == "thawani" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($extra['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($extra['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" .'  <label class="form-group__service-name"> Secret Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">'. '  <label class="form-group__service-name">Publishable key</label>' . "\r\n" . '  <input type="text" class="form-control" name="publishable_key" value="' . $extra['publishable_key'] . '">'. '  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="0" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">'.'  <label class="form-group__service-name">Fee  (%)</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}


elseif( $action == "edit_paymentmethod" && $_POST["id"] == "youcan" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($extra['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($extra['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" .'  <label class="form-group__service-name"> Private Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="private_key" value="' . $extra['private_key'] . '">'. '  <label class="form-group__service-name">Public key</label>' . "\r\n" . '  <input type="text" class="form-control" name="public_key" value="' . $extra['public_key'] . '">'.'  <label class="form-group__service-name">Fee  (%)</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}


elseif( $action == "edit_paymentmethod" && $_POST["id"] == "esewa" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($extra['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($extra['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" .'  <label class="form-group__service-name"> Merchant id</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_id" value="' . $extra['merchant_id'] . '">'. '  <label class="form-group__service-name">Fee  (%)</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="fee" value="' . $extra['fee'] . '">' . '  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}



elseif( $action == "edit_paymentmethod" && $_POST["id"] == "khalti" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' .  "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" .'  <label class="form-group__service-name">Public key</label>' . "\r\n" . '  <input type="text" class="form-control" name="public_key" value="' . $extra['public_key'] . '">'.'  <label class="form-group__service-name">Secret key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">'. '  <label class="form-group__service-name">Fee  (%)</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="fee" value="' . $extra['fee'] . '">' . '  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}


elseif( $action == "edit_paymentmethod" && $_POST["id"] == "kashier" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Modo</label>' . "\r\n" . '<select class="form-control" name="is_demo">' . "\r\n" . '  <option value="1"';

    if ($method['is_demo'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Demo</option>' . "\r\n" . '  <option value="0"';

    if ($method['is_demo'] == 0) {
        $return .= 'selected';
    }

    $return .= '>Live</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant id</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_id" value="' . $extra['merchant_id'] . '">'. '  <label class="form-group__service-name"> Secret Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="secret_key" value="' . $extra['secret_key'] . '">'. '  <label class="form-group__service-name">Api key</label>' . "\r\n" . '  <input type="text" class="form-control" name="api_key" value="' . $extra['api_key'] . '">'.'  <label class="form-group__service-name">Taxa do dólar</label>' . "\r\n" . '  <input type="number" step="0.01" min="1" class="form-control" name="dollar_rate" value="' . $extra['dollar_rate'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}



elseif ($action == "edit_paymentmethod" && $_POST["id"] == "stripe"){
  $id = $_POST['id'];
  $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
  $method->execute(['id' => $id]);
  $method = $method->fetch(PDO::FETCH_ASSOC);
  $extra = json_decode($method['method_extras'], true);
  $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

  if ($method['method_type'] == 2) {
    $return .= 'selected';
  }

  $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

  if ($method['method_type'] == 1) {
    $return .= 'selected';
  }

  $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
  $return .= site_url('payment/' . $method['method_get']);
  $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Stripe Publishable Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="stripe_publishable_key" value="' . $extra['stripe_publishable_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Stripe Secret Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="stripe_secret_key" value="' . $extra['stripe_secret_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Stripe Webhooks Secret</label>' . "\r\n" . '  <input type="text" class="form-control" name="stripe_webhooks_secret" value="' . $extra['stripe_webhooks_secret'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Currency</label>' . "\r\n" . '  <input type="text" class="form-control" name="currency" value="' . $extra['currency'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '             <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '                      </div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
  echo json_encode(['content' => $return, 'title' => '']);
}

elseif( $action == "edit_paymentmethod" && $_POST["id"] == "coinbase" ){
            $id = $_POST['id'];
                $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
                $method->execute(['id' => $id]);
                $method = $method->fetch(PDO::FETCH_ASSOC);
                $extra = json_decode($method['method_extras'], true);
                $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';
            
                if ($method['method_type'] == 2) {
                    $return .= 'selected';
                }
            
                $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';
            
                if ($method['method_type'] == 1) {
                    $return .= 'selected';
                }
            
                $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
                $return .= site_url('payment/' . $method['method_get']);
                $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">API KEY</label>' . "\r\n" . '  <input type="text" class="form-control" name="api_key" value="' . $extra['api_key'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '' . "\r\n\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">WEBHOOK SHARED API KEY</label>' . "\r\n" . '  <input type="text" class="form-control" name="webhook_api" value="' . $extra['webhook_api'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" .'<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">COMMISSION</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . '         <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '                 </div>'. ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
                echo json_encode(['content' => $return, 'title' => '']);
                
}
            


elseif ($action == "edit_paymentmethod" && $_POST["id"] == "payumoney") {
  $id = $_POST['id'];
  $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
  $method->execute(['id' => $id]);
  $method = $method->fetch(PDO::FETCH_ASSOC);
  $extra = json_decode($method['method_extras'], true);
  $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

  if ($method['method_type'] == 2) {
    $return .= 'selected';
  }

  $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

  if ($method['method_type'] == 1) {
    $return .= 'selected';
  }

  $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
  $return .= site_url('payment/' . $method['method_get']);
  $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Merchant Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_key" value="' . $extra['merchant_key'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Salt Key</label>' . "\r\n" . '  <input type="text" class="form-control" name="salt" value="' . $extra['salt'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Currency</label>' . "\r\n" . '  <input type="text" class="form-control" name="currency" value="' . $extra['currency'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '         <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '             </div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
  echo json_encode(['content' => $return, 'title' => '']);
}














elseif( $action == "edit_paymentmethod" && $_POST["id"] == "custom3" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n";
    $return .= '' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}




elseif( $action == "edit_paymentmethod" && $_POST["id"] == "custom2" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n";
    $return .= '' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}







elseif( $action == "edit_paymentmethod" && $_POST["id"] == "custom" ){
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

    $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n";
    $return .= '' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '</div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
}
elseif (($action == 'edit_paymentmethod') && ($_POST['id'] == 'perfectmoney')) {
    $id = $_POST['id'];
    $method = $conn->prepare('SELECT * FROM payment_methods WHERE method_get=:id ');
    $method->execute(['id' => $id]);
    $method = $method->fetch(PDO::FETCH_ASSOC);
    $extra = json_decode($method['method_extras'], true);
    $return = '<form class="form" action="' . site_url('admin/settings/payment-methods/edit/' . $id) . '" method="post" data-xhr="true">' . "\r\n\r\n" . '<div class="modal-body">' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do método</label>' . "\r\n" . '  <input type="text" class="form-control" readonly value="' . $method['method_name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="service-mode__block">' . "\r\n" . '  <div class="form-group">' . "\r\n" . '  <label>Visibilidade</label>' . "\r\n" . '<select class="form-control" name="method_type">' . "\r\n" . '  <option value="2"';

    if ($method['method_type'] == 2) {
        $return .= 'selected';
    }

    $return .= '>Ativo</option>' . "\r\n" . '  <option value="1"';

    if ($method['method_type'] == 1) {
        $return .= 'selected';
    }

     $return .= '>Inativo</option>' . "\r\n" . '</select>' . "\r\n" . '  </div>' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome visível</label>' . "\r\n" . '  <input type="text" class="form-control" name="name" value="' . $extra['name'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Mínimo</label>' . "\r\n" . '  <input type="text" class="form-control" name="min" value="' . $extra['min'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Pagamento Máximo</label>' . "\r\n" . '  <input type="text" class="form-control" name="max" value="' . $extra['max'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n" . ' <hr>' . "\r\n" . '  <p class="card-description">' . "\r\n" . '<ul>' . "\r\n" . '<li>' . "\r\n" . ' API callback address: <code>';
    $return .= site_url('payment/' . $method['method_get']);
    $return .= '</code>' . "\r\n" . '</li>' . "\r\n" . '</ul>' . "\r\n" . '  </p>' . "\r\n" . ' <hr>' . "\r\n\r\n" . ' <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Senha alternativa</label>' . "\r\n" . '  <input type="text" class="form-control" name="passphrase" value="' . $extra['passphrase'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">USD ID</label>' . "\r\n" . '  <input type="text" class="form-control" name="usd" value="' . $extra['usd'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Nome do site do comerciante</label>' . "\r\n" . '  <input type="text" class="form-control" name="merchant_website" value="' . $extra['merchant_website'] . '">' . "\r\n" . ' </div>' . "\r\n" . '<div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Commission, %</label>' . "\r\n" . '  <input type="text" class="form-control" name="fee" value="' . $extra['fee'] . '">' . "\r\n" . ' </div>' . "\r\n\r\n\r\n" . '            <div class="form-group">' . "\r\n" . '  <label class="form-group__service-name">Conteúdo</label>' . "\r\n" . '  <textarea  class="form-control summernote" name="content" id="custom-payment-content">' . $extra['content'] . '</textarea>' . "\r\n" . '</div>' . "\r\n\r\n" . '                    </div>' . "\r\n\r\n" . ' <div class="modal-footer">' . "\r\n" . '  <button type="submit" class="btn btn-primary">Atualizar</button>' . "\r\n" . '  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>' . "\r\n" . ' </div>' . "\r\n" . ' </form>';
    echo json_encode(['content' => $return, 'title' => '']);
} 
                                                                                                        else {
                                                                                                            if ($action == "edit_paymentmethod" && $_POST["id"] == "weepay") {
                                                                                                                $id = $_POST["id"];
                                                                                                                $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                                $method->execute(["id" => $id]);
                                                                                                                $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                                $extra = json_decode($method["method_extras"], true);
                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                                if ($method["method_type"] == 2) {
                                                                                                                    $return .= "selected";
                                                                                                                }
                                                                                                                $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                                if ($method["method_type"] == 1) {
                                                                                                                    $return .= "selected";
                                                                                                                }
                                                                                                                $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>There is no need for a return address.</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Dealer ID</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"bayi_id\" value=\"" . $extra["bayi_id"] . "\">\r\n </div>\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">API Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"api_key\" value=\"" . $extra["api_key"] . "\">\r\n </div>\r\n<div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Secret Key</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"secret_key\" value=\"" . $extra["secret_key"] . "\">\r\n </div>\r\n\r\n <div class=\"form-group\">\r\n  <label class=\"form-group__service-name\">Currency</label>\r\n  <input type=\"text\" class=\"form-control\" name=\"currency\" value=\"" . $extra["currency"] . "\">\r\n </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                            } else {
                                                                                                                if ($action == "new_bankaccount") {
                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/bank-accounts/new") . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">The name of the Bank</label>\r\n            <input type=\"text\" name=\"bank_name\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Recipient name</label>\r\n            <input type=\"text\" name=\"bank_alici\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Número da filial</label>\r\n            <input type=\"text\" name=\"bank_sube\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Account number</label>\r\n            <input type=\"text\" name=\"bank_hesap\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">IBAN</label>\r\n            <input type=\"text\" name=\"bank_iban\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Add new bank account</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                    echo json_encode(["content" => $return, "title" => "new bank account"]);
                                                                                                                } else {
                                                                                                                    if ($action == "edit_bankaccount") {
                                                                                                                        $id = $_POST["id"];
                                                                                                                        $bank = $conn->prepare("SELECT * FROM bank_accounts WHERE id=:id ");
                                                                                                                        $bank->execute(["id" => $id]);
                                                                                                                        $bank = $bank->fetch(PDO::FETCH_ASSOC);
                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/bank-accounts/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">The name of the Bank</label>\r\n            <input type=\"text\" name=\"bank_name\" class=\"form-control\" value=\"" . $bank["bank_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Recipient name</label>\r\n            <input type=\"text\" name=\"bank_alici\" class=\"form-control\" value=\"" . $bank["bank_alici"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Número da filial</label>\r\n            <input type=\"text\" name=\"bank_sube\" class=\"form-control\" value=\"" . $bank["bank_sube"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Account number</label>\r\n            <input type=\"text\" name=\"bank_hesap\" class=\"form-control\" value=\"" . $bank["bank_hesap"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">IBAN</label>\r\n            <input type=\"text\" name=\"bank_iban\" class=\"form-control\" value=\"" . $bank["bank_iban"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n        <div class=\"modal-footer\">\r\n          <a id=\"delete-row\" data-url=\"" . site_url("admin/settings/bank-accounts/delete/" . $bank["id"]) . "\" class=\"btn btn-danger pull-left\">remove account</a>\r\n          <button type=\"submit\" class=\"btn btn-primary\">Update bank account</button>\r\n          <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n        </div>\r\n        </form>\r\n        <script src=\"https://unpkg.com/sweetalert/dist/sweetalert.min.js\"></script>\r\n        <script>\r\n        \$(\"#delete-row\").click(function(){\r\n          var action = \$(this).attr(\"data-url\");\r\n          swal({\r\n            title: \"Tem certeza de que deseja excluir?\",\r\n            text: \"Se você confirmar, este conteúdo será excluído e talvez não seja possível restaurá-lo.\",\r\n            icon: \"warning\",\r\n            buttons: true,\r\n            dangerMode: true,\r\n            buttons: [\"Close\", \"Yes, I am sure!\"],\r\n          })\r\n          .then((willDelete) => {\r\n            if (willDelete) {\r\n              \$.ajax({\r\n                url:  action,\r\n                type: \"GET\",\r\n                dataType: \"json\",\r\n                cache: false,\r\n                contentType: false,\r\n                processData: false\r\n              })\r\n              .done(function(result){\r\n                if( result.s == \"error\" ){\r\n                  var heading = \"Malsucedido\";\r\n                }else{\r\n                  var heading =\"Bem-sucedido\";\r\n                }\r\n                  \$.toast({\r\n                      heading: heading,\r\n                      text: result.m,\r\n                      icon: result.s,\r\n                      loader: true,\r\n                      loaderBg: \"#9EC600\"\r\n                  });\r\n                  if (result.r!=null) {\r\n                    if( result.time ==null ){ result.time = 3; }\r\n                    setTimeout(function(){\r\n                      window.location.href  = result.r;\r\n                    },result.time*1000);\r\n                  }\r\n              })\r\n              .fail(function(){\r\n                \$.toast({\r\n                    heading: \"Malsucedido\",\r\n                    text: \"A solicitação não pôde ser atendida\",\r\n                    icon: \"error\",\r\n                    loader: true,\r\n                    loaderBg: \"#9EC600\"\r\n                });\r\n              });\r\n              /* Content deletion confirmed */\r\n            } else {\r\n              \$.toast({\r\n                  heading: \"Malsucedido\",\r\n                  text: \"Solicitação de exclusão negada\",\r\n                  icon: \"error\",\r\n                  loader: true,\r\n                  loaderBg: \"#9EC600\"\r\n              });\r\n            }\r\n          });\r\n        });\r\n        </script>\r\n          </form>";
                                                                                                                        echo json_encode(["content" => $return, "title" => "Update bank account"]);
                                                                                                                    } else {
                                                                                                                        if ($action == "edit_paymentmethod" && $_POST["id"] == "shopier") {
                                                                                                                            $id = $_POST["id"];
                                                                                                                            $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                                            $method->execute(["id" => $id]);
                                                                                                                            $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                                            $extra = json_decode($method["method_extras"], true);
                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                                            if ($method["method_type"] == 2) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                                            if ($method["method_type"] == 1) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                                            $return .= site_url("payment/" . $method["method_get"]);
                                                                                                                            $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiKey</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiKey\" value=\"" . $extra["apiKey"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiSecret</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiSecret\" value=\"" . $extra["apiSecret"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n          <label>Return</label>\r\n            <select class=\"form-control\" name=\"website_index\">\r\n                  <option value=\"1\"";
                                                                                                                            if ($extra["website_index"] == 1) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Return URL (1)</option>\r\n                  <option value=\"2\"";
                                                                                                                            if ($extra["website_index"] == 2) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Return URL (2)</option>\r\n                  <option value=\"3\"";
                                                                                                                            if ($extra["website_index"] == 3) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Return URL (3)</option>\r\n                  <option value=\"4\"";
                                                                                                                            if ($extra["website_index"] == 4) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Return URL (4)</option>\r\n                  <option value=\"5\"";
                                                                                                                            if ($extra["website_index"] == 5) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Return URL (5)</option>\r\n              </select>\r\n          </div>\r\n          <div class=\"form-group\">\r\n          <label>Transaction fee (0.49)</label>\r\n            <select class=\"form-control\" name=\"processing_fee\">\r\n                  <option value=\"1\"";
                                                                                                                            if ($extra["processing_fee"] == 1) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">reflect</option>\r\n                  <option value=\"0\"";
                                                                                                                            if ($extra["processing_fee"] == 0) {
                                                                                                                                $return .= "selected";
                                                                                                                            }
                                                                                                                            $return .= ">Projection</option>\r\n              </select>\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                            echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                                        } else {
                                                                                                                            if ($action == "edit_paymentmethod" && $_POST["id"] == "shoplemo") {
                                                                                                                                $id = $_POST["id"];
                                                                                                                                $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                                                $method->execute(["id" => $id]);
                                                                                                                                $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                                                $extra = json_decode($method["method_extras"], true);
                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                                                if ($method["method_type"] == 2) {
                                                                                                                                    $return .= "selected";
                                                                                                                                }
                                                                                                                                $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                                                if ($method["method_type"] == 1) {
                                                                                                                                    $return .= "selected";
                                                                                                                                }
                                                                                                                                $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Pagamento Mínimo</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"min\" value=\"" . $extra["min"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Maximum Payout</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"max\" value=\"" . $extra["max"] . "\">\r\n          </div>\r\n\r\n          <hr>\r\n            <p class=\"card-description\">\r\n              <ul>\r\n                <li>\r\n                  API Return Address: <code>";
                                                                                                                                $return .= site_url("payment/" . $method["method_get"]);
                                                                                                                                $return .= "</code>\r\n                </li>\r\n              </ul>\r\n            </p>\r\n          <hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiKey</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiKey\" value=\"" . $extra["apiKey"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">apiSecret</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apiSecret\" value=\"" . $extra["apiSecret"] . "\">\r\n          </div>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Commission, %</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"fee\" value=\"" . $extra["fee"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                                            } else {
                                                                                                                                if ($action == "edit_paymentmethod" && $_POST["id"] == "havale-eft") {
                                                                                                                                    $id = $_POST["id"];
                                                                                                                                    $method = $conn->prepare("SELECT * FROM payment_methods WHERE method_get=:id ");
                                                                                                                                    $method->execute(["id" => $id]);
                                                                                                                                    $method = $method->fetch(PDO::FETCH_ASSOC);
                                                                                                                                    $extra = json_decode($method["method_extras"], true);
                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-methods/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do método</label>\r\n            <input type=\"text\" class=\"form-control\" readonly value=\"" . $method["method_name"] . "\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Visibilidade</label>\r\n              <select class=\"form-control\" name=\"method_type\">\r\n                    <option value=\"2\"";
                                                                                                                                    if ($method["method_type"] == 2) {
                                                                                                                                        $return .= "selected";
                                                                                                                                    }
                                                                                                                                    $return .= ">Ativo</option>\r\n                    <option value=\"1\"";
                                                                                                                                    if ($method["method_type"] == 1) {
                                                                                                                                        $return .= "selected";
                                                                                                                                    }
                                                                                                                                    $return .= ">Desativado</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome visível</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $extra["name"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                    echo json_encode(["content" => $return, "title" => "Edit payment method (Method: " . $method["method_name"] . ")"]);
                                                                                                                                } else {
                                                                                                                                    if ($action == "new_paymentbonus") {
                                                                                                                                        $methodList = $conn->prepare("SELECT * FROM payment_methods WHERE id!='7' ");
                                                                                                                                        $methodList->execute([]);
                                                                                                                                        $methodList = $methodList->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-bonuses/new") . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n          <label>Método</label>\r\n            <select class=\"form-control\" name=\"method_type\">";
                                                                                                                                        foreach ($methodList as $method) {
                                                                                                                                            $return .= "<option value=\"" . $method["id"] . "\">" . $method["method_name"] . "</option>";
                                                                                                                                        }
                                                                                                                                        $return .= "</select>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Valor do bônus (%)</label>\r\n            <input type=\"text\" name=\"amount\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">DE ($)</label>\r\n            <input type=\"text\" name=\"from\" class=\"form-control\" value=\"\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Add new bonus</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                        echo json_encode(["content" => $return, "title" => "Novo bônus de pagamento"]);
                                                                                                                                    } else {
                                                                                                                                        if ($action == "payment_bankedit") {
                                                                                                                                            $id = $_POST["id"];
                                                                                                                                            $payment = $conn->prepare("SELECT * FROM payments INNER JOIN bank_accounts ON bank_accounts.id=payments.payment_bank INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_id=:id");
                                                                                                                                            $payment->execute(["id" => $id]);
                                                                                                                                            $payment = $payment->fetch(PDO::FETCH_ASSOC);
                                                                                                                                            $bank = $conn->prepare("SELECT * FROM bank_accounts ");
                                                                                                                                            $bank->execute();
                                                                                                                                            $bank = $bank->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/payments/edit-bank/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>pagamento Bancario</label>\r\n              <select class=\"form-control\" name=\"bank\">";
                                                                                                                                            foreach ($bank as $banka) {
                                                                                                                                                $return .= "<option value=\"" . $banka["id"] . "\"";
                                                                                                                                                if ($payment["payment_bank"] == $banka["id"]) {
                                                                                                                                                    $return .= "selected";
                                                                                                                                                }
                                                                                                                                                $return .= ">" . $banka["bank_name"] . "</option>";
                                                                                                                                            }
                                                                                                                                            $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Payment status</label>\r\n              <select class=\"form-control\" ";
                                                                                                                                            if ($payment["payment_status"] == 3) {
                                                                                                                                                $return .= "disabled";
                                                                                                                                            }
                                                                                                                                            $return .= " name=\"status\">\r\n                    <option value=\"1\"";
                                                                                                                                            if ($payment["payment_status"] == 1) {
                                                                                                                                                $return .= "selected";
                                                                                                                                            }
                                                                                                                                            $return .= ">pending</option>\r\n                    <option value=\"2\"";
                                                                                                                                            if ($payment["payment_status"] == 2) {
                                                                                                                                                $return .= "selected";
                                                                                                                                            }
                                                                                                                                            $return .= ">Cancel</option>\r\n                    <option value=\"3\"";
                                                                                                                                            if ($payment["payment_status"] == 3) {
                                                                                                                                                $return .= "selected";
                                                                                                                                            }
                                                                                                                                            $return .= ">Approved</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Texto </label>\r\n            <input type=\"text\" class=\"form-control\" name=\"note\" value=\"" . $payment["payment_note"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                            echo json_encode(["content" => $return, "title" => "Issue bank payment (ID: " . $id . ") "]);
                                                                                                                                        } else {
                                                                                                                                            if ($action == "payment_banknew") {
                                                                                                                                                $bank = $conn->prepare("SELECT * FROM bank_accounts ");
                                                                                                                                                $bank->execute();
                                                                                                                                                $bank = $bank->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/payments/new-bank/") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome de usuário</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"username\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Valor</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"amount\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>pagamento Bancario</label>\r\n              <select class=\"form-control\" name=\"bank\">";
                                                                                                                                                foreach ($bank as $banka) {
                                                                                                                                                    $return .= "<option value=\"" . $banka["id"] . "\">" . $banka["bank_name"] . "</option>";
                                                                                                                                                }
                                                                                                                                                $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Texto </label>\r\n            <input type=\"text\" class=\"form-control\" name=\"note\" value=\"\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Adicionar pagamento</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                echo json_encode(["content" => $return, "title" => "Adicionar pagamento bancário"]);
                                                                                                                                            } else {
                                                                                                                                                if ($action == "edit_paymentbonus") {
                                                                                                                                                    $id = $_POST["id"];
                                                                                                                                                    $bonus = $conn->prepare("SELECT * FROM payments_bonus WHERE bonus_id=:id ");
                                                                                                                                                    $bonus->execute(["id" => $id]);
                                                                                                                                                    $bonus = $bonus->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                    $methodList = $conn->prepare("SELECT * FROM payment_methods  WHERE id!='7' ");
                                                                                                                                                    $methodList->execute([]);
                                                                                                                                                    $methodList = $methodList->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/payment-bonuses/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n          <label>Método</label>\r\n            <select class=\"form-control\" name=\"method_type\">";
                                                                                                                                                    foreach ($methodList as $method) {
                                                                                                                                                        $return .= "<option value=\"" . $method["id"] . "\"";
                                                                                                                                                        if ($bonus["bonus_method"] == $method["id"]) {
                                                                                                                                                            $return .= "selected";
                                                                                                                                                        }
                                                                                                                                                        $return .= ">" . $method["method_name"] . "</option>";
                                                                                                                                                    }
                                                                                                                                                    $return .= "</select>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">Valor do bônus (%)</label>\r\n            <input type=\"text\" name=\"amount\" class=\"form-control\" value=\"" . $bonus["bonus_amount"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group\">de (<i class=\"fa fa-brl\"></i>)</label>\r\n            <input type=\"text\" name=\"from\" class=\"form-control\" value=\"" . $bonus["bonus_from"] . "\">\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Update bonus</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>  \r\n            <a id=\"delete-row\" data-url=\"" . site_url("admin/settings/payment-bonuses/delete/" . $bonus["bonus_id"]) . "\" class=\"btn btn-link pull-right deactivate-integration-btn\">remove bonus</a>\r\n\r\n          </div>\r\n          </form>\r\n          <script src=\"https://unpkg.com/sweetalert/dist/sweetalert.min.js\"></script>\r\n          <script>\r\n          \$(\"#delete-row\").click(function(){\r\n            var action = \$(this).attr(\"data-url\");\r\n            swal({\r\n              title: \"Tem certeza de que deseja excluir?\",\r\n              text: \"Se você confirmar, este conteúdo será excluído e talvez não seja possível restaurá-lo.\",\r\n              icon: \"warning\",\r\n              buttons: true,\r\n              dangerMode: true,\r\n              buttons: [\"Close\", \"Yes, I am sure!\"],\r\n            })\r\n            .then((willDelete) => {\r\n              if (willDelete) {\r\n                \$.ajax({\r\n                  url:  action,\r\n                  type: \"GET\",\r\n                  dataType: \"json\",\r\n                  cache: false,\r\n                  contentType: false,\r\n                  processData: false\r\n                })\r\n                .done(function(result){\r\n                  if( result.s == \"error\" ){\r\n                    var heading = \"Malsucedido\";\r\n                  }else{\r\n                    var heading =\"Bem-sucedido\";\r\n                  }\r\n                    \$.toast({\r\n                        heading: heading,\r\n                        text: result.m,\r\n                        icon: result.s,\r\n                        loader: true,\r\n                        loaderBg: \"#9EC600\"\r\n                    });\r\n                    if (result.r!=null) {\r\n                      if( result.time ==null ){ result.time = 3; }\r\n                      setTimeout(function(){\r\n                        window.location.href  = result.r;\r\n                      },result.time*1000);\r\n                    }\r\n                })\r\n                .fail(function(){\r\n                  \$.toast({\r\n                      heading: \"Malsucedido\",\r\n                      text: \"A solicitação não pôde ser atendida\",\r\n                      icon: \"error\",\r\n                      loader: true,\r\n                      loaderBg: \"#9EC600\"\r\n                  });\r\n                });\r\n                /* Content deletion confirmed */\r\n              } else {\r\n                \$.toast({\r\n                    heading: \"Malsucedido\",\r\n                    text: \"Solicitação de exclusão negada\",\r\n                    icon: \"error\",\r\n                    loader: true,\r\n                    loaderBg: \"#9EC600\"\r\n                });\r\n              }\r\n            });\r\n          });\r\n          </script>\r\n          ";
                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Update payment bonus"]);
                                                                                                                                                } else {
                                                                                                                                                    if ($action == "new_provider") {
                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/providers/new") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n
          <div class=\"form-group\">\r\n
            <label class=\"form-group__service-name\">API URL</label>\r\n
            <input type=\"text\" class=\"form-control\" name=\"url\" value=\"https://machinesmm.com/api/v2\"readonly>\r\n
          </div>\r\n
          \r\n
          <div class=\"form-group\">\r\n
            <label class=\"form-group__service-name\">API Key</label>\r\n
            <input type=\"text\" class=\"form-control\" name=\"key\" value=\"\">\r\n
          </div>\r\n\r\n
        </div>\r\n\r\n
          <div class=\"modal-footer\">\r\n
            <button type=\"submit\" class=\"btn btn-primary\">Adicionar novo provedor</button>\r\n
            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n
          </div>\r\n
          </form>";
                                                                                                                                                        echo json_encode(["content" => $return, "title" => "Adicionar novo provedor"]);
                                                                                                                                                    } else {
                                                                                                                                                        if ($action == "edit_provider") {
                                                                                                                                                            $id = $_POST["id"];
                                                                                                                                                            $provider = $conn->prepare("SELECT * FROM service_api WHERE id=:id ");
                                                                                                                                                            $provider->execute(["id" => $id]);
                                                                                                                                                            $provider = $provider->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                            $services = $conn->prepare("SELECT * FROM services WHERE service_api=:api");
                                                                                                                                                            $services->execute(["api" => $id]);
                                                                                                                                                            if ($settings["guard_apikey_type"] == 2 && $settings["guard_system_status"] == 2) {
                                                                                                                                                                $key = crypt(crc32(md5(sha1(str_rot13(base64_encode($provider["api_key"]))))));
                                                                                                                                                            } else {
                                                                                                                                                                $key = $provider["api_key"];
                                                                                                                                                            }
                                                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/providers/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome do provedor</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"name\" value=\"" . $provider["api_name"] . "\">\r\n          </div>\r\n<hr>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">API URL</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"url\" value=\"" . $provider["api_url"] . "\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">API Key</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"apikey\" value=\"" . $key . "\">\r\n          </div>\r\n<hr>\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Limite de Saldo <small>(Você receberá uma notificação se seu saldo cair abaixo desse valor)</small></label>\r\n            <input type=\"text\" class=\"form-control\" name=\"limit\" value=\"" . $provider["api_limit"] . "\">\r\n          </div>\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n         <button type=\"submit\" class=\"btn btn-primary\">Atualizar</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>";
                                                                                                                                                            if (!$services->rowCount()) {
                                                                                                                                                                $return .= " <a id=\"delete-row\" data-url=\"" . site_url("admin/settings/providers/delete/" . $provider["id"]) . "\" </a>";
                                                                                                                                                            }
                                                                                                                                                            $return .= "\r\n          </div>\r\n          </form>\r\n          \r\n               <script src=\"https://unpkg.com/sweetalert/dist/sweetalert.min.js\"></script>\r\n          <script>\r\n          \$(\"#delete-row\").click(function(){\r\n            var action = \$(this).attr(\"data-url\");\r\n            swal({\r\n              title: \"Tem certeza de que deseja excluir?\",\r\n              text: \"Se você confirmar, este conteúdo será excluído e talvez não seja possível restaurá-lo.\",\r\n              icon: \"warning\",\r\n              buttons: true,\r\n              dangerMode: true,\r\n              buttons: [\"Close\", \"Yes, I am sure!\"],\r\n            })\r\n            .then((willDelete) => {\r\n              if (willDelete) {\r\n                \$.ajax({\r\n                  url:  action,\r\n                  type: \"GET\",\r\n                  dataType: \"json\",\r\n                  cache: false,\r\n                  contentType: false,\r\n                  processData: false\r\n                })\r\n                .done(function(result){\r\n                  if( result.s == \"error\" ){\r\n                    var heading = \"Malsucedido\";\r\n                  }else{\r\n                    var heading =\"Bem-sucedido\";\r\n                  }\r\n                    \$.toast({\r\n                        heading: heading,\r\n                        text: result.m,\r\n                        icon: result.s,\r\n                        loader: true,\r\n                        loaderBg: \"#9EC600\"\r\n                    });\r\n                    if (result.r!=null) {\r\n                      if( result.time ==null ){ result.time = 3; }\r\n                      setTimeout(function(){\r\n                        window.location.href  = result.r;\r\n                      },result.time*1000);\r\n                    }\r\n                })\r\n                .fail(function(){\r\n                  \$.toast({\r\n                      heading: \"Malsucedido\",\r\n                      text: \"A solicitação não pôde ser atendida\",\r\n                      icon: \"error\",\r\n                      loader: true,\r\n                      loaderBg: \"#9EC600\"\r\n                  });\r\n                });\r\n                /* Content deletion confirmed */\r\n              } else {\r\n                \$.toast({\r\n                    heading: \"Malsucedido\",\r\n                    text: \"Solicitação de exclusão negada\",\r\n                    icon: \"error\",\r\n                    loader: true,\r\n                    loaderBg: \"#9EC600\"\r\n                });\r\n              }\r\n            });\r\n          });\r\n          </script>";
                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Editar provedor(" . $provider["api_name"] . ") "]);
                                                                                                                                                        } else {
                                                                                                                                                            if ($action == "new_news") {
                                                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/appearance/news/new") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n        \r\n     <div class=\"form-group\">\r\n                            <label class=\"control-label\" for=\"createorderform-currency\">ícone de anúncio</label>\r\n                            <select class=\"form-control\" name=\"icon\">\r\n                            <option value=\"duyuru\">General Announcement</option>\r\n                            <option value=\"yildiz\">Star</option>\r\n                            <option value=\"instagram\">Instagram</option>\r\n                            <option value=\"facebook\">Facebook</option>\r\n                            <option value=\"youtube\">Youtube</option>\r\n                            <option value=\"twitter\">Twitter</option>\r\n                            <option value=\"tiktok\">TikTok</option>  \r\n                            <option value=\"spotify\">Spotify</option>\r\n                            <option value=\"pinterest\">Pinterest</option>\r\n                            <option value=\"telegram\">Telegram</option>\r\n                            <option value=\"twitch\">Twitch</option>\r\n\r\n                                                            </select>\r\n                        </div>\r\n                        \r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Título do anúncio</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"title\"></textarea>\r\n          </div>\r\n          \r\n        <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Conteúdo do anúncio</label>\r\n            <textarea class=\"form-control\" name=\"content\"></textarea>\r\n          </div>\r\n</div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Adicionar anúncio</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Adicionar novo anúncio"]);
                                                                                                                                                            } else {
                                                                                                                                                                if ($action == "edit_news") {
                                                                                                                                                                    $id = $_POST["id"];
                                                                                                                                                                    $news = $conn->prepare("SELECT * FROM news WHERE id=:id ");
                                                                                                                                                                    $news->execute(["id" => $id]);
                                                                                                                                                                    $news = $news->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/appearance/news/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n     <div class=\"form-group\">\r\n                            <label class=\"control-label\" for=\"createorderform-currency\">ícone de anúncio</label>\r\n                            <select class=\"form-control\" name=\"icon\">\r\n                            <option value=\"" . $news["news_icon"] . "\">selected: " . $news["news_icon"] . "</option>\r\n                            <option value=\"duyuru\">General Announcement</option>\r\n                            <option value=\"yildiz\">Star</option>\r\n                            <option value=\"instagram\">Instagram</option>\r\n                            <option value=\"facebook\">Facebook</option>\r\n                            <option value=\"youtube\">Youtube</option>\r\n                            <option value=\"twitter\">Twitter</option>\r\n                            <option value=\"tiktok\">TikTok</option>  \r\n                            <option value=\"spotify\">Spotify</option>\r\n                            <option value=\"pinterest\">Pinterest</option>\r\n                            <option value=\"telegram\">Telegram</option>\r\n                            <option value=\"twitch\">Twitch</option>\r\n                                                            </select>\r\n                        </div>\r\n                        \r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Título do anúncio</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"title\" value=\"" . $news["news_title"] . "\"></textarea>\r\n          </div>\r\n          \r\n        <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Conteúdo do anúncio</label>\r\n            <textarea class=\"form-control\" name=\"content\" rows=\"7\" >" . $news["news_content"] . "</textarea>\r\n          </div>\r\n\r\n\r\n          \r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n         <button type=\"submit\" class=\"btn btn-primary\">Atualizar</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n         \r\n      <a id=\"delete-row\" data-url=\"" . site_url("admin/appearance/news/delete/" . $news["id"]) . "\" class=\"btn btn-link pull-right deactivate-integration-btn\">Delete Announcement</a>\r\n          \r\n          </div>\r\n          </form>\r\n          \r\n               <script src=\"https://unpkg.com/sweetalert/dist/sweetalert.min.js\"></script>\r\n          <script>\r\n          \$(\"#delete-row\").click(function(){\r\n            var action = \$(this).attr(\"data-url\");\r\n            swal({\r\n              title: \"Tem certeza de que deseja excluir?\",\r\n              text: \"Se você confirmar, este conteúdo será excluído e talvez não seja possível restaurá-lo.\",\r\n              icon: \"warning\",\r\n              buttons: true,\r\n              dangerMode: true,\r\n              buttons: [\"Close\", \"Yes, I am sure!\"],\r\n            })\r\n            .then((willDelete) => {\r\n              if (willDelete) {\r\n                \$.ajax({\r\n                  url:  action,\r\n                  type: \"GET\",\r\n                  dataType: \"json\",\r\n                  cache: false,\r\n                  contentType: false,\r\n                  processData: false\r\n                })\r\n                .done(function(result){\r\n                  if( result.s == \"error\" ){\r\n                    var heading = \"Malsucedido\";\r\n                  }else{\r\n                    var heading =\"Bem-sucedido\";\r\n                  }\r\n                    \$.toast({\r\n                        heading: heading,\r\n                        text: result.m,\r\n                        icon: result.s,\r\n                        loader: true,\r\n                        loaderBg: \"#9EC600\"\r\n                    });\r\n                    if (result.r!=null) {\r\n                      if( result.time ==null ){ result.time = 3; }\r\n                      setTimeout(function(){\r\n                        window.location.href  = result.r;\r\n                      },result.time*1000);\r\n                    }\r\n                })\r\n                .fail(function(){\r\n                  \$.toast({\r\n                      heading: \"Malsucedido\",\r\n                      text: \"A solicitação não pôde ser atendida\",\r\n                      icon: \"error\",\r\n                      loader: true,\r\n                      loaderBg: \"#9EC600\"\r\n                  });\r\n                });\r\n                /* Content deletion confirmed */\r\n              } else {\r\n                \$.toast({\r\n                    heading: \"Malsucedido\",\r\n                    text: \"Solicitação de exclusão negada\",\r\n                    icon: \"error\",\r\n                    loader: true,\r\n                    loaderBg: \"#9EC600\"\r\n                });\r\n              }\r\n            });\r\n          });\r\n          </script>";
                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Edit Announcement (" . $provider["api_name"] . ") "]);
                                                                                                                                                                } else {
                                                                                                                                                                    if ($action == "export_user") {
                                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/clients/export") . "\" method=\"post\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Status de associação</label>\r\n              <select class=\"form-control\" name=\"client_status\">\r\n                    <option value=\"all\">Todos os membros</option>\r\n                    <option value=\"1\">Desativado</option>\r\n                    <option value=\"2\">Ativo</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Status do e-mail</label>\r\n              <select class=\"form-control\" name=\"email_status\">\r\n                    <option value=\"all\">Todos os membros</option>\r\n                    <option value=\"1\">Não aprovado</option>\r\n                    <option value=\"2\">Onaylı</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Formato</label>\r\n              <select class=\"form-control\" name=\"format\">\r\n                    <option value=\"json\">JSON</option>\r\n                </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Informações do membro</label>\r\n              <div class=\"form-group\">\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[client_id]\" checked value=\"1\"> ID\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[email]\" checked value=\"1\"> Email\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[name]\" checked value=\"1\"> Sobrenome\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[username]\" checked value=\"1\"> Nome de usuário\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[telephone]\" checked value=\"1\"> Número de telefone\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[balance]\" checked value=\"1\"> Saldo\r\n </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[spent]\" checked value=\"1\"> Valor gasto\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[register_date]\" checked value=\"1\"> Data de registro\r\n                  </label>\r\n                  <label class=\"checkbox-inline\">\r\n                    <input type=\"checkbox\" class=\"access\" name=\"exportcolumn[login_date]\" checked value=\"1\"> Última data de login\r\n                  </label>\r\n              </div>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Backup do Usuários </button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "Fazer backup dos usuários"]);
                                                                                                                                                                    } else {
                                                                                                                                                                        if ($action == "all_numbers") {
                                                                                                                                                                            $rows = $conn->prepare("SELECT * FROM clients");
                                                                                                                                                                            $rows->execute([]);
                                                                                                                                                                            $rows = $rows->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                                            $numbers = "";
                                                                                                                                                                            $emails = "";
                                                                                                                                                                            foreach ($rows as $row) {
                                                                                                                                                                                if ($row["telephone"]) {
                                                                                                                                                                                    $numbers .= $row["telephone"] . "\n";
                                                                                                                                                                                }
                                                                                                                                                                                $emails .= $row["email"] . "\n";
                                                                                                                                                                            }
                                                                                                                                                                            $return = "<form>\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Números de telefone dos membros</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>" . $numbers . "</textarea>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Endereços de e-mail dos membros</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>" . $emails . "</textarea>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Informação do usuário"]);
                                                                                                                                                                        } else {
                                                                                                                                                                            if ($action == "details") {
                                                                                                                                                                                $toplamkullanici = $conn->prepare("SELECT * FROM clients");
                                                                                                                                                                                $toplamkullanici->execute();
                                                                                                                                                                                $toplamkullanici = $toplamkullanici->rowCount();
                                                                                                                                                                                $query = $conn->query("SELECT sum(balance) as toplambakiye FROM clients")->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                $query2 = $conn->query("SELECT sum(order_charge) as order_charge FROM orders")->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                $negatifbakiye = $conn->prepare("SELECT * FROM clients where balance < 0");
                                                                                                                                                                                $negatifbakiye->execute();
                                                                                                                                                                                $negatifbakiye = $negatifbakiye->rowCount();
                                                                                                                                                                                $bakiyesiz = $conn->prepare("SELECT * FROM clients where balance = 0");
                                                                                                                                                                                $bakiyesiz->execute();
                                                                                                                                                                                $bakiyesiz = $bakiyesiz->rowCount();
                                                                                                                                                                                $return = "<form>\r\n        <div class=\"modal-body\">\r\n\t\t\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Total Users : " . $toplamkullanici . "</label>\r\n            </div>\r\n          </div>\r\n\t\t  \r\n\t\t  <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Total Available Balance : " . $query["toplambakiye"] . "</label>\r\n            </div>\r\n          </div>\r\n\t\t  \r\n\t\t  <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Total Balance Spent : " . $query2["order_charge"] . "</label>\r\n            </div>\r\n          </div>\r\n\t\t  \r\n\t\t  <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>User with Negative Balance : " . $negatifbakiye . "</label>\r\n            </div>\r\n          </div>\r\n\t\t  \r\n\t\t  <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>User with Zero Balance : " . $bakiyesiz . "</label>\r\n            </div>\r\n          </div>\r\n\t\t  \r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Detail"]);
                                                                                                                                                                            } else {
                                                                                                                                                                                if ($action == "price_user") {
                                                                                                                                                                                    $id = $_POST["id"];
                                                                                                                                                                                    $price = $conn->prepare("SELECT *,services.service_id as serviceid,services.service_price as price,clients_price.service_price as clientprice FROM services LEFT JOIN clients_price ON clients_price.service_id=services.service_id && clients_price.client_id=:id ");
                                                                                                                                                                                    $price->execute(["id" => $id]);
                                                                                                                                                                                    $price = $price->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/clients/price/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n        <div class=\"majer\">\r\n               <div>\r\n                  <div class=\"services-import__list-wrap services-import__list-active\">\r\n                     <div class=\"services-import__scroll-wrap\">\r\n                        <span>\r\n                             <div class=\"services-import__packages\">\r\n                                <ul>";
                                                                                                                                                                                    foreach ($price as $row) {
                                                                                                                                                                                        $return .= "<li id=\"service-" . $row["serviceid"] . "\">\r\n                                     <label class=\"row\" style=\"margin:0\">\r\n                                     <p class=\"col-md-8 col-xs-10\" style=\"margin-top:5px;padding:0px;\">\r\n                                        <span class=\"label-id\" style=\"margin-right:7px\">" . $row["serviceid"] . "</span>" . $row["service_name"] . "</p>\r\n                                        \r\n                                           <span class=\"col-md-1 col-xs-2\" style=\"margin-top:5px\">" . $row["price"] . "</span>\r\n                                        <span class=\"col-md-2 col-xs-10\" >\r\n                                        <div class=\"input-group\" style=\"width:100%\">\r\n                                           <input type=\"text\" style=\"border-radius:5px\" class=\"form_field field_price form-control\" name=\"price[" . $row["serviceid"] . "]\" value=\"" . $row["clientprice"] . "\">\r\n                                           </div>\r\n                                        </span>\r\n                                        \r\n                                           <div class=\"col-md-1 col-xs-2\" style=\"text-align:right;font-size:17px;top:5;right:17px;\"  data-id=\"servicedelete-" . $row["serviceid"] . "\" data-service=\"" . $row["serviceid"] . "\">\r\n                                             <span class=\"fa fa-trash\"></span>\r\n                                           </div>\r\n                                        \r\n                                        \r\n                                     </label>\r\n                                    </li>";
                                                                                                                                                                                    }
                                                                                                                                                                                    $return .= "</ul>\r\n                             </div>\r\n                          </span></div>\r\n                  </div>\r\n               </div>\r\n            </div>\r\n            <script>\r\n\r\n              \$('[data-id^=\"servicedelete-\"]').click(function(){\r\n                var id        = \$(this).attr(\"data-service\");\r\n                \$(\"[name='price[\"+id+\"]']\").val(\"\");\r\n                //\$(\"ul > li#service-\"+id).remove();\r\n              });\r\n\r\n            </script>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Special Pricing"]);
                                                                                                                                                                                } else {
                                                                                                                                                                                    if ($action == "order_errors") {
                                                                                                                                                                                        $id = $_POST["id"];
                                                                                                                                                                                        $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                        $row->execute(["id" => $id]);
                                                                                                                                                                                        $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                        $errors = json_decode($row["order_error"]);
                                                                                                                                                                                        $return = "<form>\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Information from the (provider: " . funwithai($row["order_api"]) . ")</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>";
                                                                                                                                                                                        $return .= print_r($errors, true);
                                                                                                                                                                                        $return .= "</textarea>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "details (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                    } else {
                                                                                                                                                                                        if ($action == "order_comment") {
                                                                                                                                                                                            $id = $_POST["id"];
                                                                                                                                                                                            $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                            $row->execute(["id" => $id]);
                                                                                                                                                                                            $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                            $arr = json_decode($row["order_extras"], true);
                                                                                                                                                                                            $cnazede = $arr["comments"];
                                                                                                                                                                                            $return = "<form>\r\n        <div class=\"modal-body\">\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Comments</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>";
                                                                                                                                                                                            $return .= print_r($cnazede, true);
                                                                                                                                                                                            $return .= "</textarea>\r\n            </div>\r\n          </div>\r\n        </div>\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Comments"]);
                                                                                                                                                                                        } else {
                                                                                                                                                                                            if ($action == "order_details") {
                                                                                                                                                                                                $id = $_POST["id"];
                                                                                                                                                                                                $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                                $row->execute(["id" => $id]);
                                                                                                                                                                                                $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                $detail = json_decode($row["order_detail"]);
                                                                                                                                                                                                $return = "<form>\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Information from the provider</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>";
                                                                                                                                                                                                $return .= print_r($detail, true);
                                                                                                                                                                                                $return .= "</textarea>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Order ID</label>\r\n              <input class=\"form-control\" value=\"" . $row["api_orderid"] . "\" readonly>\r\n            </div>\r\n          </div>\r\n\r\n     <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Nome do provedor</label>\r\n              <input class=\"form-control\" value=\"" . funwithai($row["order_api"]) . "\" readonly>\r\n            </div>\r\n          </div>\r\n\r\n                <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Last update</label>\r\n              <input class=\"form-control\" value=\"" . $row["last_check"] . "\" readonly>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Detalhes do pedido (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                            } else {
                                                                                                                                                                                                if ($action == "order_orderurl") {
                                                                                                                                                                                                    $id = $_POST["id"];
                                                                                                                                                                                                    $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                                    $row->execute(["id" => $id]);
                                                                                                                                                                                                    $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                    $detail = json_decode($row["order_detail"]);
                                                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/orders/set_orderurl/" . $id) . "\" method=\"post\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Link do Pedido</label>\r\n              <input class=\"form-control\" value=\"" . $row["order_url"] . "\" name=\"url\">\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Detalhes do pedido (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                                } else {
                                                                                                                                                                                                    if ($action == "order_startcount") {
                                                                                                                                                                                                        $id = $_POST["id"];
                                                                                                                                                                                                        $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                                        $row->execute(["id" => $id]);
                                                                                                                                                                                                        $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                        $detail = json_decode($row["order_detail"]);
                                                                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/orders/set_startcount/" . $id) . "\" method=\"post\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Number of starts</label>\r\n              <input class=\"form-control\" value=\"" . $row["order_start"] . "\" name=\"start\">\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "Detalhes do pedido (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        if ($action == "order_partial") {
                                                                                                                                                                                                            $id = $_POST["id"];
                                                                                                                                                                                                            $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                                            $row->execute(["id" => $id]);
                                                                                                                                                                                                            $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                            $detail = json_decode($row["order_detail"]);
                                                                                                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/orders/set_partial/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Amount not gone</label>\r\n              <input class=\"form-control\" name=\"remains\">\r\n            </div>\r\n          </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Detalhes do pedido (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            if ($action == "subscriptions_expiry") {
                                                                                                                                                                                                                $id = $_POST["id"];
                                                                                                                                                                                                                $row = $conn->prepare("SELECT * FROM orders WHERE order_id=:id ");
                                                                                                                                                                                                                $row->execute(["id" => $id]);
                                                                                                                                                                                                                $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                $detail = json_decode($row["order_detail"]);
                                                                                                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/subscriptions/set_expiry/" . $id) . "\" method=\"post\">\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Number of starts</label>\r\n              <input class=\"form-control datetime\" value=\"";
                                                                                                                                                                                                                if ($row["subscriptions_expiry"] != "1970-01-01") {
                                                                                                                                                                                                                    $return .= date("d/m/Y", strtotime($row["subscriptions_expiry"]));
                                                                                                                                                                                                                }
                                                                                                                                                                                                                $return .= "\" name=\"expiry\">\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>\r\n          <link rel=\"stylesheet\" type=\"text/css\" href=\"" . site_url("js/") . "datepicker/css/bootstrap-datepicker3.min.css\">\r\n          <script type=\"text/javascript\" src=\"" . site_url("js/") . "datepicker/js/bootstrap-datepicker.min.js\"></script>\r\n          <script type=\"text/javascript\" src=\"" . site_url("js/") . "datepicker/locales/bootstrap-datepicker.tr.min.js\"></script>\r\n          ";
                                                                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Subscription end date (ID: " . $row["order_id"] . ") "]);
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                if ($action == "payment_edit") {
                                                                                                                                                                                                                    $id = $_POST["id"];
                                                                                                                                                                                                                    $payment = $conn->prepare("SELECT * FROM payments INNER JOIN clients ON clients.client_id=payments.client_id WHERE payments.payment_id=:id");
                                                                                                                                                                                                                    $payment->execute(["id" => $id]);
                                                                                                                                                                                                                    $payment = $payment->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                    $methods = $conn->prepare("SELECT * FROM payment_methods WHERE id!='7' ");
                                                                                                                                                                                                                    $methods->execute();
                                                                                                                                                                                                                    $methods = $methods->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/payments/edit-online/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Método de pagamento</label>\r\n              <select class=\"form-control\" name=\"method\">";
                                                                                                                                                                                                                    foreach ($methods as $method) {
                                                                                                                                                                                                                        $return .= "<option value=\"" . $method["id"] . "\"";
                                                                                                                                                                                                                        if ($payment["payment_method"] == $method["id"]) {
                                                                                                                                                                                                                            $return .= "selected";
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $return .= ">" . $method["method_name"] . "</option>";
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                    $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Texto </label>\r\n            <input type=\"text\" class=\"form-control\" name=\"note\" value=\"Pagamento manual" . $payment["payment_note"] . "\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Arrange payment online (ID: " . $id . ") "]);
                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                    if ($action == "payment_new") {
                                                                                                                                                                                                                        $methods = $conn->prepare("SELECT * FROM payment_methods WHERE id!='7' ");
                                                                                                                                                                                                                        $methods->execute();
                                                                                                                                                                                                                        $methods = $methods->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/payments/new-online") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Nome de usuário</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"username\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Valor</label>\r\n            <input type=\"text\" class=\"form-control\" name=\"amount\" value=\"\">\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Adicionar/retirar</label>\r\n              <select class=\"form-control\" name=\"add-remove\">\r\n                <option value=\"add\">Adicionar</option>\r\n                <option value=\"remove\">Remove valor X </option>\r\n            </select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Método de pagamento</label>\r\n              <select class=\"form-control\" name=\"method\">";
                                                                                                                                                                                                                        foreach ($methods as $method) {
                                                                                                                                                                                                                            $return .= "<option value=\"" . $method["id"] . "\">" . $method["method_name"] . "</option>";
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        $return .= "</select>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"form-group\">\r\n            <label class=\"form-group__service-name\">Texto </label>\r\n            <input type=\"text\" class=\"form-control\" name=\"note\" value=\"\">\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"submit\" class=\"btn btn-primary\">Adicionar pagamento</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "Adicionar pagamento manual"]);
                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                        if ($action == "payment_detail") {
                                                                                                                                                                                                                            $id = $_POST["id"];
                                                                                                                                                                                                                            $row = $conn->prepare("SELECT * FROM payments WHERE payment_id=:id ");
                                                                                                                                                                                                                            $row->execute(["id" => $id]);
                                                                                                                                                                                                                            $row = $row->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                            $detail = json_decode($row["payment_extra"]);
                                                                                                                                                                                                                            $return = "<form>\r\n        <div class=\"modal-body\">\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Payment info</label>\r\n              <textarea class=\"form-control\" rows=\"8\" readonly>";
                                                                                                                                                                                                                            $return .= print_r($detail, true);
                                                                                                                                                                                                                            $return .= "</textarea>\r\n            </div>\r\n          </div>\r\n\r\n          <div class=\"service-mode__block\">\r\n            <div class=\"form-group\">\r\n            <label>Last update</label>\r\n              <input class=\"form-control\" value=\"" . $row["payment_update_date"] . "\" readonly>\r\n            </div>\r\n          </div>\r\n\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Payment details (ID: " . $row["payment_id"] . ") "]);
                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                            if ($action == "module_ref") {
                                                                                                                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/settings/modules/ref") . "\" method=\"post\" data-xhr=\"true\">    \r\n        <div class=\"modal-body\">\r\n        <div class=\"form-group\">\r\n              <label class=\"control-label\">Sistema de referência</label>\r\n              <select class=\"form-control\" name=\"referral\">\r\n                <option value=\"2\" ";
                                                                                                                                                                                                                                if ($settings["referral"] == 2) {
                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $return .= ">Ativo</option>\r\n                <option value=\"1\" ";
                                                                                                                                                                                                                                if ($settings["referral"] == 1) {
                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $return .= ">Desativado</option>\r\n              </select>\r\n            </div>\r\n              <hr />\r\n\t\t\t<div class=\"form-group\">\r\n              <label class=\"control-label\">Bônus de referência %</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"ref_bonus\" value=\"" . $settings["ref_bonus"] . "\">\r\n            </div>\r\n\t\t\t<div class=\"form-group\">\r\n              <label class=\"control-label\">Valor Máximo de Carregamento</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"ref_max\" value=\"" . $settings["ref_max"] . "\">\r\n\t\t\t  <p class=\"help-block\">\r\n                <small>Você pode obter um bônus de referência para carregar até um máximo de Quantia.</small>\r\n\t\t\t  </p>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"form-group\">\r\n              <label class=\"control-label\">Tipo de referência</label>\r\n              <select class=\"form-control\" name=\"ref_type\">\r\n                <option value=\"1\" ";
                                                                                                                                                                                                                                if ($settings["ref_type"] == 1) {
                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $return .= ">No primeiro pagamento</option>\r\n                <option value=\"0\" ";
                                                                                                                                                                                                                                if ($settings["ref_type"] == 0) {
                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                $return .= ">Em cada  pagamento</option>\r\n              </select>\r\n            </div>\r\n   \r\n        </div>\r\n\r\n          <div class=\"modal-footer\">            \r\n          <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n          </form>";
                                                                                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Reference System"]);
                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                if ($action == "module_child") {
                                                                                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/modules/module_child") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n     \r\n     <div class=\"form-group\">\r\n          <label class=\"control-label\">Vendas do painel filho</label>\r\n          <select class=\"form-control\" name=\"panel_selling\">\r\n            <option value=\"2\" ";
                                                                                                                                                                                                                                    if ($settings["panel_selling"] == 2) {
                                                                                                                                                                                                                                        $return .= "selected";
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    $return .= ">Ativo</option>\r\n            <option value=\"1\" ";
                                                                                                                                                                                                                                    if ($settings["panel_selling"] == 1) {
                                                                                                                                                                                                                                        $return .= "selected";
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                    $return .= ">Desativado</option>\r\n          </select>\r\n        </div>\r\n     \r\n     <div class=\"form-group\">\r\n              <label class=\"control-label\">Taxa mensal</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"panel_price\" value=\"" . $settings["panel_price"] . "\">\r\n            </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">            \r\n          <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n      </form>  ";
                                                                                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Painel afiliado"]);
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    if ($action == "module_balance") {
                                                                                                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/modules/module_balance") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n     \r\n     <div class=\"form-group\">\r\n          <label class=\"control-label\">Saldo grátis</label>\r\n          <select class=\"form-control\" name=\"free_balance\">\r\n            <option value=\"2\" ";
                                                                                                                                                                                                                                        if ($settings["free_balance"] == 2) {
                                                                                                                                                                                                                                            $return .= "selected";
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $return .= ">Ativo</option>\r\n            <option value=\"1\" ";
                                                                                                                                                                                                                                        if ($settings["free_balance"] == 1) {
                                                                                                                                                                                                                                            $return .= "selected";
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                        $return .= ">Desativado</option>\r\n          </select>\r\n        </div>\r\n     \r\n     <div class=\"form-group\">\r\n              <label class=\"control-label\">Valor do presente</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"free_amount\" value=\"" . $settings["free_amaount"] . "\">\r\n            </div>\r\n\r\n        </div>\r\n\r\n          <div class=\"modal-footer\">            \r\n          <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n      </form>  ";
                                                                                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "Saldo grátis"]);
                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                        if ($action == "module_cache") {
                                                                                                                                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/settings/modules/module_cache") . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n        <div class=\"modal-body\">\r\n\r\n     \r\n     <div class=\"form-group\">\r\n          <label class=\"control-label\">Sistemas de cache </label>\r\n          <select class=\"form-control\" name=\"cache\">\r\n            <option value=\"2\" ";
                                                                                                                                                                                                                                            if ($settings["cache"] == 2) {
                                                                                                                                                                                                                                                $return .= "selected";
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                            $return .= ">Ativo</option>\r\n            <option value=\"1\" ";
                                                                                                                                                                                                                                            if ($settings["cache"] == 1) {
                                                                                                                                                                                                                                                $return .= "selected";
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                            $return .= ">Desativado</option>\r\n          </select>\r\n        </div>\r\n     \r\n     <div class=\"form-group\">\r\n              <label class=\"control-label\">Tempo do cache (horas)</label>\r\n              <input type=\"text\" class=\"form-control\" name=\"cache_time\" value=\"" . $settings["cache_time"] . "\">\r\n            </div>\r\n\r\n        </div>\r\n        \r\n        </div>\r\n\r\n          <div class=\"modal-footer\">            \r\n          <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n          </div>\r\n      </form>  ";
                                                                                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Sistemas de cache "]);
                                                                                                                                                                                                                                        } else {
                                                                                                                                                                                                                                            if ($action == "edit_code") {
                                                                                                                                                                                                                                                $id = $_POST["id"];
                                                                                                                                                                                                                                                $int = $conn->prepare("SELECT * FROM integrations WHERE id=:id");
                                                                                                                                                                                                                                                $int->execute(["id" => $id]);
                                                                                                                                                                                                                                                $int = $int->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                                                $return = "<form class=\"form\" action=\"" . site_url("admin/settings/integrations/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n\r\n       <div class=\"modal-body\">\r\n                            <div id=\"editIntegrationError\" class=\"error-summary alert alert-danger hidden\"></div>                <div class=\"form edit-integration-modal-body\">\r\n                                <div class=\"form-group field-editintegrationform-code\">\r\n            <label class=\"control-label\" for=\"editintegrationform-code\">code area</label>\r\n            <textarea id=\"editintegrationform-code\" class=\"form-control\" name=\"code\" rows=\"7\" placeholder=\"\">" . $int["code"] . "</textarea>\r\n            </div>                    <div class=\"form-group field-editintegrationform-visibility\">\r\n            <label class=\"control-label\" for=\"editintegrationform-visibility\">Visibility</label>\r\n            <select class=\"form-control\" name=\"visibility\">\r\n            <option value=\"1\" ";
                                                                                                                                                                                                                                                if ($int["visibility"] == 1) {
                                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                $return .= ">todas as páginas</option>\r\n            <option value=\"2\" ";
                                                                                                                                                                                                                                                if ($int["visibility"] == 2) {
                                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                $return .= ">Não logado</option>\r\n            <option value=\"3\" ";
                                                                                                                                                                                                                                                if ($int["visibility"] == 3) {
                                                                                                                                                                                                                                                    $return .= "selected";
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                $return .= ">Signed in</option>\r\n            </select>\r\n            </div>                </div>\r\n                        </div>\r\n                        <div class=\"modal-footer\">\r\n                            <button type=\"submit\" class=\"btn btn-primary\">\r\n                                Update                </button>\r\n                            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">\r\n                                Close                </button>\r\n                            <a href=\"/admin/settings/integrations/disabled/" . $id . "\" class=\"btn btn-link pull-right deactivate-integration-btn\">\r\n                                deactivate\r\n                            </a>\r\n                        </div>\r\n                        </form>    ";
                                                                                                                                                                                                                                                echo json_encode(["content" => $return, "title" => "Edit integration (ID: " . $id . ")"]);
                                                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                                                if ($action == "edit_google") {
                                                                                                                                                                                                                                                    $return = "<form class=\"form\" action=\"" . site_url("admin/settings/integrations/google") . "\" method=\"post\" data-xhr=\"true\">\r\n            \r\n                    <div class=\"modal-body\">\r\n\r\n                 \r\n                 <div class=\"form-group\">\r\n                          <label class=\"control-label\">Site Key</label>\r\n                          <input type=\"text\" class=\"form-control\" name=\"pwd\" value=\"" . $settings["recaptcha_key"] . "\">\r\n                        </div>\r\n            \r\n                        <div class=\"form-group\">\r\n                        <label class=\"control-label\">Secret Key</label>\r\n                        <input type=\"text\" class=\"form-control\" name=\"secret\" value=\"" . $settings["recaptcha_secret"] . "\">\r\n                      </div>\r\n                    </div>\r\n            \r\n                      <div class=\"modal-footer\">            \r\n                      <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n                      </div>\r\n                  </form>  ";
                                                                                                                                                                                                                                                    echo json_encode(["content" => $return, "title" => "Google reCAPTCHA v2"]);
                                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                                    if ($action == "edit_seo") {
                                                                                                                                                                                                                                                        $return = "<form class=\"form\" action=\"" . site_url("admin/settings/integrations/seo") . "\" method=\"post\" data-xhr=\"true\">\r\n            \r\n                    <div class=\"modal-body\">\r\n        <div class=\"form-group\">\r\n          <label for=\"\" class=\"control-label\">Title</label>\r\n          <input type=\"text\" class=\"form-control\" name=\"title\" value=\"" . $settings["site_title"] . "\">\r\n        </div>\r\n        <div class=\"form-group\">\r\n          <label for=\"\" class=\"control-label\">Keywords</label>\r\n          <input type=\"text\" class=\"form-control\" name=\"keywords\" value=\"" . $settings["site_keywords"] . "\">\r\n        </div>\r\n        <div class=\"form-group\">\r\n          <label class=\"control-label\">Description</label>\r\n          <textarea class=\"form-control\" rows=\"3\" name=\"description\">" . $settings["site_description"] . "</textarea>\r\n        </div>\r\n                      \r\n                      \r\n                    </div>\r\n            \r\n                      <div class=\"modal-footer\">            \r\n                      <button type=\"submit\" class=\"btn btn-primary\">Atualizar configurações</button>\r\n                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n                      </div>\r\n                  </form>  ";
                                                                                                                                                                                                                                                        echo json_encode(["content" => $return, "title" => "SEO Adjustments"]);
                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                        if ($action == "edit_ticket") {
                                                                                                                                                                                                                                                            $id = $_POST["id"];
                                                                                                                                                                                                                                                            $tickets = $conn->prepare("SELECT * FROM ticket_reply WHERE id=:id");
                                                                                                                                                                                                                                                            $tickets->execute(["id" => $id]);
                                                                                                                                                                                                                                                            $tickets = $tickets->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                                                                                                                            $return = "<form class=\"form\" action=\"" . site_url("admin/tickets/edit/" . $id) . "\" method=\"post\" data-xhr=\"true\">\r\n            \r\n                    <div class=\"modal-body\">\r\n        <div class=\"form-group\">\r\n          <label class=\"control-label\">Message Content</label>\r\n          <textarea class=\"form-control\" rows=\"5\" name=\"description\">" . $tickets["message"] . "</textarea>\r\n        </div>\r\n                    </div>\r\n                      <div class=\"modal-footer\">            \r\n                      <button type=\"submit\" class=\"btn btn-primary\">Atualizar</button>\r\n                        <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Fechar</button>\r\n                      </div>\r\n                  </form>  ";
                                                                                                                                                                                                                                                            echo json_encode(["content" => $return, "title" => "Edit support message"]);
                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                    }
                                                                                                                                                                                                                }
                                                                                                                                                                                                            }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    }
                                                                                                                                                                                                }
                                                                                                                                                                                            }
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                                                }
                                                                                                                                                                            }
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                                                }
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

if( $action == "add_currency" ){
            $currency_code = abcus("id", $settings["site_currency"], "name");

    $return = '<form class="form" action="'.site_url("admin/settings/currency/add").'" method="post" data-xhr="true">

        <div class="modal-body">

          <div class="form-group">
            <label class="form-group__service-name">Símbolo da moeda</label>
            <input type="text" class="form-control" name="symbol" value="">
          </div>

          
          <div class="form-group">
            <label class="form-group__service-name">Nome da moeda</label>
            <input type="text" class="form-control" name="name" value="">
          </div>

          <div class="form-group">
            <label class="form-group__service-name">1 '.$currency_code.' = </label>
            <input type="text" class="form-control" name="value" value="">
          </div>
       
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Adicionar moeda</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
          </form>';
    echo json_encode(["content"=>$return,"title"=>"Add Currency"]);
    }
if( $action == "edit_currency" ){
    $id         = $_POST["id"];
    $provider   = $conn->prepare("SELECT * FROM currency WHERE id=:id ");
    $provider   ->execute(array("id"=>$id));
    $provider   = $provider->fetch(PDO::FETCH_ASSOC);
    $cur=$provider["name"];
    if($provider["rate"]==2){
                      $provider["value"]=liverate($cur);  
    }else{
                           $provider["value"]=$provider["value"];  
   
    }
                        
               
    $return = '<form class="form" action="'.site_url("admin/settings/currency/edit/".$id).'" method="post" data-xhr="true">

        <div class="modal-body">

          <div class="form-group">
            <label class="form-group__service-name">Nome da moeda</label>
            <input type="text" class="form-control" name="name" value="'.$provider["name"].'">
          </div>
          <div class="form-group">
            <label class="form-group__service-name">Símbolo da moeda</label>
            <input type="text" class="form-control" name="symbol" value="'.$provider["symbol"].'">
          </div>
<div class="form-group">
            <label class="form-group__service-name">Taxas de câmbio</label>
            <input type="text" class="form-control" name="currencyvalue" value="'.$provider["value"].'">
          </div> 


<div class="service-mode__block">
                <div class="form-group">
              <label>Status da moeda</label>
                  <select class="form-control" name="status">
                      <option value="1"'; if( $provider["status"] == 1 ): $return.='selected'; endif; $return.='>Ativado</option>
                      <option value="2"'; if( $provider["status"] == 2 ): $return.='selected'; endif; $return.='>Desativado</option>
                  </select>
                </div>
              </div>


          </div>
          
          

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

         </form>';  
    echo json_encode(["content"=>$return,"title"=>"Editar moeda (".$provider["name"].") "]);
   

 }
 elseif( $action == "coustm_rate" ){
    $id     = $_POST["id"];
    $row    = $conn->prepare("SELECT * FROM clients WHERE client_id=:id ");
    $row ->execute(array("id"=>$id));
    $row    = $row->fetch(PDO::FETCH_ASSOC);
    
    $return = '<form class="form" action="'.site_url("admin/clients/set_discount/".$id).'" method="post" data-xhr="true">
        <div class="modal-body">

          <div class="service-mode__block">
            <div class="form-group">
            <label>Porcentagem de desconto (%)</label>
              <input class="form-control" name="coustm_rate" placeholder="25"   value="'.$row["coustm_rate"].'"    >
            </div>
          </div>

        </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">salvar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
          </form>';
    echo json_encode(["content"=>$return,"title"=>"Bulk Discount (For: ".$row["username"].") "]);


}
?>