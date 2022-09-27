<?php


use App\Case2\Service\DummyUserEmailSender;
use App\Case2\Service\UserEmailChangerService;

require_once 'vendor/autoload.php';


//docker rm -f mysql; docker run -d -e MYSQL_ROOT_PASSWORD=roflcoding -p 3306:3306 --name mysql mysql

$db = new PDO("mysql:host=127.0.0.1;port=3306;dbname=testdb", 'root', 'roflcoding');

$ddl = <<<SQL
create table if not exists users
(
    id int auto_increment,
    name varchar(64) not null,
    email varchar(256) not null,
    constraint users_pk
        primary key (id),
    constraint users_email
        unique (email)
);
SQL;


$db->exec($ddl);
$service = new UserEmailChangerService($db, new DummyUserEmailSender());
$service->changeEmail(1, 'troll@ya.ru');


