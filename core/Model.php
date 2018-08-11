<?php

namespace Core;

use PDO;

abstract class Model extends DB
{
    /**
     * The model data.
     *
     * @var array
     */
    private $data = [];

    /**
     * The model fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The fields to be hidden on toArray method.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The fields to be filtered when saving the model to database.
     *
     * @var array
     */
    private static $fieldsToBeFilteredWhenSaving = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The constructor.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct (array $data = [])
    {
        $this->fill($data);
    }

    /**
     * Perform a query to database.
     *
     * @param  string  $sql
     * @return \PDOStatement
     */
    protected static function query ($sql)
    {
        return parent::query($sql);
    }

    /**
     * Prepare a query to be performed.
     *
     * @param  string  $sql
     * @return \PDOStatement
     */
    protected static function prepare ($sql)
    {
        return parent::prepare($sql);
    }

    /**
     * Get the last inserted id from database.
     *
     * @return int
     */
    protected static function getInsertedId ()
    {
        return parent::getInsertedId();
    }

    /**
     * Get the model id.
     *
     * @return int
     */
    public function getId ()
    {
        return $this->data['id'];
    }

    /**
     * Fill the model data.
     *
     * @param  array  $data
     * @return void
     */
    public function fill (array $data)
    {
        foreach ($data as $field => $value)
        {
            if (in_array($field, $this->fields))
            {
                $this->data[$field] = $value;
            }
        }
    }

    /**
     * Get a specified field from model.
     *
     * @param  string  $name
     * @return string
     */
    public function getField ($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Set a specified field to model.
     *
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    protected function setField ($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Check if the model has a specified field.
     *
     * @param  string  $field
     * @return boolean
     */
    protected function hasField ($field)
    {
        return array_key_exists($field, $this->data);
    }

    /**
     * Load a specified relation to the model.
     *
     * @param  string  $class
     * @return void
     */
    public function load ($class)
    {
        $relation = $class::getTable();

        $sql = "SELECT * FROM {$relation} WHERE user_id = :user_id";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
        $stmt->execute();

        $collection = $stmt->fetchAll();

        $models = [];

        foreach ($collection as $data)
        {
            $models[] = new $class($data);
        }

        $this->setField($relation, $models);
    }

    /**
     * Convert the model's data to array.
     *
     * @return array
     */
    public function toArray ()
    {
        $data = $this->data;

        foreach ($data as $field => $value)
        {
            if (in_array($field, $this->hidden))
            {
                unset($data[$field]);
            }
            else if (is_array($value))
            {
                foreach ($value as $relation)
                {
                    $index = key($value);

                    $data[$field][$index] = $relation->toArray();

                    next($value);
                }
            }
        }

        return $data;
    }

    /**
     * Get the model's table.
     *
     * @return string
     */
    public static function getTable ()
    {
        return static::$table;
    }

    /**
     * Get a specified model by a specified field.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $criteria
     * @return mixed
     */
    public static function findBy ($field, $value, $criteria = '*')
    {
        $table = self::getTable();

        $sql = "SELECT {$criteria} FROM {$table} WHERE {$field} = :{$field} LIMIT 1";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(":{$field}", $value, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch();

        return $data ? new static($data) : null;
    }

    /**
     * Get a specified model by its id.
     *
     * @param  int      $id
     * @param  string   $criteria
     * @return mixed
     */
    public static function findById ($id, $criteria = '*')
    {
        return static::findBy('id', $id, $criteria);
    }

    /**
     * Get all models based on instantiated class.
     *
     * @param  string  $criteria
     * @return array
     */
    public static function all ($criteria = '*')
    {
        $table = self::getTable();

        $sql = "SELECT {$criteria} FROM {$table} ORDER BY id DESC";

        $stmt = parent::query($sql);

        $collection = $stmt->fetchAll();

        $models = [];

        foreach ($collection as $data)
        {
            $models[] = new static($data);
        }

        return $models;
    }

    /**
     * Save the current model to database.
     *
     * @return mixed
     */
    public function save ()
    {
        $data = [];
        $table = self::getTable();

        $fields = array_filter($this->fields, function ($element) {
            return !in_array($element, self::$fieldsToBeFilteredWhenSaving);
        });

        $columns = implode(', ', $fields);
        $values = implode(', :', $fields);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES (:{$values})";

        $stmt = parent::prepare($sql);

        foreach ($fields as $field)
        {
            $value = $this->data[$field];
            $type = parent::$dataTypes[gettype($value)];

            $stmt->bindValue(":{$field}", $value, $type);

            $data[$field] = $value;
        }

        if ($stmt->execute())
        {
            $this->setField('id', parent::getInsertedId());
            $this->setField('created_at', date('Y-m-d H:i:s'));
            $this->setField('updated_at', date('Y-m-d H:i:s'));

            return $this;
        }

        return false;
    }

    /**
     * Store a specified model to database.
     *
     * @param  array  $data
     * @return mixed
     */
    public static function create (array $data)
    {
        $model = new static($data);

        $model->save();

        return $model;
    }

    /**
     * Update the current model.
     *
     * @param  array  $data
     * @return mixed
     */
    public function update (array $data)
    {
        $table = self::getTable();

        $setters = '';

        foreach ($data as $field => $value)
        {
            $setters .= "{$field} = :{$field}, ";
        }

        $setters = rtrim($setters, ', ');

        $sql = "UPDATE {$table} SET {$setters} WHERE id = :id";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        foreach ($data as $field => $value)
        {
            $type = parent::$dataTypes[gettype($value)];

            $stmt->bindValue(":{$field}", $value, $type);

            $this->setField($field, $value);
        }

        if ($stmt->execute())
        {
            return $this;
        }

        return false;
    }

    /**
     * Delete the current model from database.
     *
     * @return boolean
     */
    public function delete ()
    {
        $table = self::getTable();

        $sql = "DELETE FROM {$table} WHERE id = :id";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        if ($stmt->execute())
        {
            return true;
        }

        return false;
    }

    /**
     * Get all models related to a specified user based on instantiated class.
     *
     * @param  int  $userId
     * @return array
     */
    public static function findAllFromUser ($userId)
    {
        $sql = "SELECT * FROM sessions WHERE user_id = :user_id ORDER BY id DESC";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_STR);
        $stmt->execute();

        $collection = $stmt->fetchAll();

        $models = [];

        foreach ($collection as $data)
        {
            $models[] = new static($data);
        }

        return $models;
    }

    /**
     * Delete all models related to a specified user based on instantiated class.
     *
     * @param  int  $userId
     * @return void
     */
    public static function deleteAllFromUser ($userId)
    {
        $table = self::getTable();

        $sql = "DELETE FROM {$table} WHERE user_id = :user_id";

        $stmt = parent::prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
