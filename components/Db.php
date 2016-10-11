<?php

/**
  * Клас Db
  * Компонент для роботи з базою даних
  */
class Db
{

    /**
     * Встановлює з'єднання з базою даних
     * @return \ PHP <p> Об'єкт класу PDO для роботи з БД </ p>
     */
    public static function getConnection()
    {
        $paramsPath = ROOT . '/config/db_params.php';
        $params = include($paramsPath);

        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
        $db = new PDO($dsn, $params['user'], $params['password']);

        $db->exec("set names utf8");

        return $db;
    }
    public static function getData($query)
    {
        $db = self::getConnection();

        $query = $db->query($query);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function update($query)
    {
        $db = self::getConnection();
        $db->query($query);
    }
    public static function create($query,$insert_data)
    {
        $db = self::getConnection();
        $object = $db->prepare($query);
        $object->execute($insert_data);
    }
    public static function delete($query)
    {
        $db = self::getConnection();
        $count = $db->exec($query);
    }


}
