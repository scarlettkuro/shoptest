<?php

namespace App\Components\DAO;

/**
 * Description of ModelDAO
 *
 * @author kuro
 */
class ModelDAO {
    
    /** @var PDO $pdo PDO object */
    private $pdo;
    /** @var class $modelClass Model class */
    private $modelClass;
    
    /**
     * @param Class $class Class of the model
     */
    public function __construct($pdo, $class) { 
        $this->pdo = $pdo;
        $this->modelClass = $class;
    }
    
    /**
     * Return array of all models
     * @return mixed Models
     */
    public function readAll() {  
        $table_name = call_user_func([$this->modelClass, 'table_name']);
        
        $sql = "SELECT * FROM $table_name";
        $statement = $this->pdo->prepare($sql);
        
        $statement->execute() or $this->sqlException($statement);
        
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->modelClass);
        $entities =  $statement->fetchAll();
        
        return $entities;
    }
    
    /**
     * Return specific model
     * @param String $id Value of primary key
     * @return mixed Model
     */
    public function read($id) {
        $pk = call_user_func([$this->modelClass, 'primary']);
        return $this->query("$pk = :id", ['id' => $id]);
    }
    
    /**
     * Retrieve models by query
     * @param String $where Part of SQL query after 'WHERE'
     * @param Array $params Values for query to bind
     * @return mixed Models
     */
    public function query($where, $params) { 
        $table_name = call_user_func([$this->modelClass, 'table_name']);
        
        $sql = "SELECT * FROM $table_name WHERE $where";   
        $statement = $this->pdo->prepare($sql);   
        
        foreach($params as $key=>$value) {
            $statement->bindValue(":$key", $value);
        }
        
        $statement->execute() or $this->sqlException($statement);
        
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->modelClass);
        return $statement->fetch();
    }
    
    /**
     * Insert (if new) or update model
     * @param ModelInterface $entity Model
     */
    public function save(ModelInterface $entity) {        
        if ($this->isNew($entity)) {
            $this->create($entity);
        } else {
            $this->update($entity);
        }
    }
    
    /**
     * Check, if model is new
     * @param ModelInterface $entity Model
     * @return boolean True if new
     */
    public function isNew(ModelInterface $entity) {
        $pk = $entity->primary();
        return $entity->$pk == NULL;
    }
    
    /**
     * Inserts model
     * @param ModelInterface $entity Model
     */
    public function create(ModelInterface $entity) {
        $fields = array_diff($entity->fields(), [$entity->primary()]);
        $table_name = $entity->table_name();
        
        $keys = implode(", ",$fields);
        $set_list = ':'.implode(", :", $fields);
        
        $sql = "INSERT INTO $table_name ($keys) VALUES ($set_list)";
        $statement = $this->pdo->prepare($sql);
        
        foreach($fields as $field) {
            $statement->bindParam(":$field", $entity->$field);
        }
        
        $statement->execute() or $this->sqlException($statement);
    }

    /**
     * Updates model
     * @param ModelInterface $entity Model
     */
    public function update(ModelInterface $entity) {
        $fields = $entity->fields();
        $pk = $entity->primary();
        $table_name = $entity->table_name();
        
        $set = [];
        
        foreach($fields as $field) {                       
            $set[] = "$field = :$field";
        }
        
        $set_list = implode(", ", $set);
        
        $sql = "UPDATE $table_name SET $set_list WHERE $pk = :$pk";                              
        $statement = $this->pdo->prepare($sql);
        
        foreach($fields as $field) {
            $statement->bindParam(":$field", $entity->$field);
        }
        
        $statement->execute() or $this->sqlException($statement);
    }
    
    /**
     * Throws SQL Exception
     * @param PDOStatement $statement Statement after execution
     */
    public function sqlException ($statement) {
        throw new \Exception($statement->errorInfo()[2]);
    }
    
}
