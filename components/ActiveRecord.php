<?php

/**
 * Абстрактний клас ActiveRecord містить загальну роботи з базою даних
 */
abstract class ActiveRecord
{

    /**
     * Метод який повертає всі данні відповідної таблиці у вигляді масива обєктів відповідного классу
     * @return array|mixed
     */
    public static function findAll()
    {
        $tableName = static::$tableName;

        $data = Db::getData("SELECT * FROM " . $tableName);

        return self::getObject($data);
    }

    /**
     * Метод який повертає один об'єкт згідно заданого параметра
     * @param $id
     * @return mixed
     */
    public static function findOne($id)
    {
        $tableName = static::$tableName;

        $data = Db::getData("SELECT * FROM ".$tableName." WHERE `id`=".$id);

        return self::getObject($data);
    }

    /**
     * Метод який повертає довільну кількість об'єктів згідно заданих параметрів
     * @param $whereAnd
     * @return array|mixed
     */
    public static function find($whereAnd)
    {
        $tableName = static::$tableName;

        $query_and = self::prepareWhereAnd($whereAnd);

        $data = Db::getData("SELECT * FROM ".$tableName." WHERE ".$query_and);

        return self::getObject($data);
    }

    /**
     * Метод який повертає довільну кількість об'єктів згідно заданого sql Where виразу
     * @param $sql
     * @return array|mixed
     */
    public static function findSql($sql)
    {
        $tableName = static::$tableName;

        $data = Db::getData("SELECT * FROM ".$tableName." WHERE ".$sql);

        return self::getObject($data);
    }

    /**
     * Метод який повертає довільну кількість об'єктів згідно заданого повного sql виразу
     * @param $sql_query
     * @return array|mixed
     */
    public static function sql($sql_query)
    {

        $data = Db::getData($sql_query);
        return self::getObject($data);
    }

    /**
     * Оновлення запису в базі даних відповідно об'єкта
     */
    public function update()
    {
        $tableName = static::$tableName;

        $arrayDataObject = $this->iterateItems();

        $set_data = self::prepareUpdateSet($arrayDataObject);

        Db::update("UPDATE ".$tableName." SET ".$set_data." WHERE `id`=".$this->id);
    }

    /**
     * Створення запису в базі даних відповідно об'єкта
     */
    public function save()
    {

        $tableName = static::$tableName;
        $data = $this->iterateItems();

        $into_data =  self::prepareInsertInto($data);
        $values_data = self::prepareInsertValues($data);

        $insert_data = self::prepareInsertData($data);
        Db::create("INSERT INTO ".$tableName." ".$into_data." VALUES ".$values_data.";",$insert_data);

    }

    /**
     * Видалення запису в базі даних відповідно об'єкта
     */
    public function delete()
    {
        $tableName = static::$tableName;
        Db::delete("DELETE  FROM ".$tableName." WHERE  `id` = ".$this->id);
    }

    /**
     * Конвертація даних у відповідний масив
     * @param $data
     * @return array
     */
    private static function prepareInsertData($data)
    {
        $list = [];
        foreach ($data as $key=>$value)
        {
            $list[':'.$key] =  $value;
        }
        return $list;
    }

    /**
     * Підготовка даних до sql запиту
     * @param $data
     * @return mixed|string
     */
    private static function prepareInsertValues($data)
    {
        $str = '(';
        foreach ($data as $key=>$value)
        {
            $str.= ' :'.$key.' ,';
        }
        $str.=')';

        $str = str_replace(',)',')',$str);

        return $str;
    }

    /**
     * Підготовка полів до sql запиту
     * @param $data
     * @return mixed|string]
     */
    private static function prepareInsertInto($data)
    {
        $str = '(';
        foreach ($data as $key=>$value)
        {
            $str.= '`'.$key.'` ,';
        }
        $str.=')';

        $str = str_replace(',)',')',$str);

        return $str;
    }

    /**
     * Підготовка полів і даних до sql запиту
     * @param $data
     * @return string
     */
    private static function prepareUpdateSet($data)
    {
        $set_data = '';
        $i = 0;
        
        foreach ($data as $key => $value)
        {
            if($i == count($data)-1)
                $set_data.=" `".$key."` = '".$value."'";
            else
                $set_data.=" `".$key."` = '".$value."',";
            
            
            $i++;
        }

        return $set_data;
    }

    /**
     * Підготовка даних згідно WHERE
     * @param $whereAnd
     * @return string
     */
    private static function prepareWhereAnd($whereAnd)
    {
        if($whereAnd)
        {
            $query_and = '';

            $i = 0;
            foreach ($whereAnd as $key => $value)
            {
                if($i == 0 && count($whereAnd) != 1)
                    $query_and.=  "( `".$key."` = '".$value."'  ";
                elseif (count($whereAnd) == 1)
                    $query_and.=  "( `".$key."` = '".$value."'  )";
                elseif ($i == count($whereAnd)-1)
                    $query_and.=  " AND `".$key."` = '".$value."'  )";
                else
                    $query_and.=  " AND `".$key."` = '".$value."' ";

                $i++;
            }

        }else
        {
            $query_and = '';
        }
        return $query_and;
    }

    /**
     * Конвертація даних об'єкта в масив
     * @return array
     */
    public function iterateItems()
    {
        $list =[];
        foreach ($this as $key => $item) {
            $list[$key] = $item;
        }
        return $list;
    }

    /**
     * Створення властивостей об'єкта з масива
     * @param $name
     * @param $value
     */
    private function createProperty($name,$value)
    {
        $this->{$name} = $value;
    }

    /**
     * Формування масива обєктів
     * @param $data
     * @return array|mixed
     */
    private static function getObject($data)
    {
        $nameClass = get_called_class();
        $list = [];
        foreach ($data as $value)
        {
            $list[] = new $nameClass($value);
        }

        if(count($list) == 1)
            return $list[0];
        else
            return $list;
    }

    /**
     * Створення об'єкта згідно масива
     * @param $data
     * @return array|mixed
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value)
        {
            $this->createProperty($key,$value);
        }
    }

}