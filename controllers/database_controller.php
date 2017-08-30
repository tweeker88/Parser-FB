<?php
if(isset($_POST['btn'])){
    $login = strip_tags(htmlspecialchars($_POST['login'])); // проверка на безопасность
    $pass = strip_tags(htmlspecialchars($_POST['pass']));
    $data = new Database($dbh);

    $fb = new Facebook();
    $auth = $fb->auth($login,$pass); //Авторизация

    $friend = $fb->parseFriends(); // Поиск друзей
    $data->dell();
    foreach ($friend as $name=>$id){
        $users = $data->insertUser($name);// Вставка друзей
        foreach ($id as $job){
            $company = $data->insertCompany($job); // Вставка компаний
            $data->insertCompanyAndUser($company,$users); // Вставка в связующую таблицу
        }

    }

    $getDatabase = $data->getCompany(); // Получение списка компаний


    // Запись в файл
    $file = fopen('CompanyAndFriends.csv',"w+");
    $title = "Место работы;ФИО сотрудника";
    $title = iconv('utf-8', 'windows-1251', $title);
    fputcsv($file, explode(";", $title), ";");

    foreach ($getDatabase as $item) {
        $value = $item['name_company'].";".$item['user_full'];
        $value = iconv('utf-8', 'windows-1251', $value);
        fputcsv($file, explode(";", $value), ";");
    }
    fclose($file);
    // Подключение файла вида с сообщением
    require_once "views/success.html";

}


