<?php
Class Database extends MyPDO {

    public function getCompany(){ // Получени списка компаний(Можно было использовать GROUP_CONCAT,для группировка)

        /*$sql = "SELECT * FROM company_and_users
                    JOIN users ON users.id_user=company_and_users.id_user
                      JOIN company ON company.id_company=company_and_users.id_company 
                      ORDER BY company_and_users.id_company DESC"; */
        $sql = "SELECT Company.*, GROUP_CONCAT(Users.name_user) AS user_full,company_and_users.id_user from Company 
left join company_and_users on company_and_users.id_company = Company.id_company 
LEFT JOIN Users on Users.id_user = company_and_users.id_user GROUP by Company.name_company";
        $getDatabase = $this->dbo->query($sql);
        return $getDatabase;
    }
    public function insertUser($name){ // Добавление Друзей
        $stmt = $this->dbo->prepare("INSERT INTO Users (name_user) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $users = $this->dbo->lastInsertId();

    }
    public function insertCompany($job){  // Добавление Компаний
        $stmt = $this->dbo->prepare("INSERT INTO Company (name_company) VALUES (:job)");
        $stmt->bindParam(':job', $job);
        $stmt->execute();
        return $companys = $this->dbo->lastInsertId();

    }
    public function insertCompanyAndUser($companys,$users){ // Добавление id компаний и друзей в связующую таблицу
        $stmt = $this->dbo -> prepare("INSERT INTO company_and_users (id_company,id_user) VALUES (:id_company,:id_user)");
        $stmt->bindParam(':id_company', $companys);
        $stmt->bindParam(':id_user', $users);
        $stmt->execute();

    }

    public function dell(){
        $this->dbo->query("TRUNCATE  TABLE `company_and_users`");
        $this->dbo->query("TRUNCATE  TABLE `Company`");
        $this->dbo->query("TRUNCATE  TABLE `Users`");
    }
}