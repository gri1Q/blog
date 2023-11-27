<?php

namespace MyProject\Models;

use MyProject\Services\Db;
use ReflectionClass;
use ReflectionObject;

abstract class ActiveRecordEntity
{
    /** @var int */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function __set(string $name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }


    /**
     * @return static[]
     */
    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    public static function getById(int $id)
    {
        $db = Db::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    abstract protected static function getTableName(): string;

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
        // var_dump($mappedProperties);
        // $this->update($mappedProperties);
    }

    private function update(array $mappedProperties): void
    {
        //здесь мы обновляем существующую запись в базе
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index; // :param1
            $columns2params[] = $column . ' = ' . $param; // column1 = :param1
            $params2values[$param] = $value; // [:param1 => value1]
            $index++;
        }
        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id = ' . $this->id;
        $db = DB::getInstance();
        $db->query($sql, $params2values, static::class);
        // var_dump($sql,);
        // var_dump($params2values);
    }

    private function insert(array $mappedProperties): void
    {
        //здесь мы создаём новую запись в базе
        $filtredProperties = array_filter($mappedProperties);
        var_dump($filtredProperties);
        $columns = [];
        $paramsNames = [];
        $params2values = [];
        foreach ($filtredProperties as $columnName  => $value) {
            $columns[] = "`$columnName`";
            $paramName = ":$columnName";
            $paramsNames[] = $paramName;
            $params2values[$paramName] = $value;
        }
        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);
        var_dump($paramsNamesViaSemicolon);
        var_dump($columnsViaSemicolon);
        var_dump($columns);
        var_dump($paramsNames);
        var_dump($params2values);
        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ') VALUES (' . $paramsNamesViaSemicolon . ');';
        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);
        $this->id = $db->getLastInsertId();
        $this->refresh();
    }
    public function delete()
    {
        $db = DB::getInstance();
        $sql = 'DELETE FROM `' . static::getTableName() . '` WHERE id = :id;';
        $db->query($sql, [':id' => $this->id]);
        $this->id = null;
    }
    private function refresh(): void
    {
        $objectFromDb = static::getById($this->id);
        foreach ($objectFromDb as $property => $value) {
            $this->$property = $value;
        }
    }
    public static function findOneByColumn($columnName, $value)
    {
        $db = DB::getInstance();
        $result = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `' . $columnName . '` = :value LIMIT 1;',
            [':value' => $value],
            static::class

        );
        if ($result === []) {
            return null;
        }
        echo "<pre>";
        return var_dump($result);
    }
    private function mapPropertiesToDbFormat()/* : array */
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            // var_dump($propertyName);
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
            // var_dump($mappedProperties);
        }

        return $mappedProperties;
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('#(?<!^)[A-Z]#', '_$0', $source));
    }
}
