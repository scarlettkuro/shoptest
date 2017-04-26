<?php

namespace App\Components\DAO;

use \PDO;

/**
 * Description of DAO
 *
 * @author kuro
 */
class DAO {
    
    protected $pdo;

    public function __construct($params) {
        $this->pdo = $this->getPDO($params);
    }

    public function getModelDAO($modelClass) {
        return new ModelDAO($this->pdo, $modelClass);
    }

    /**
     * Creates a PDO object with passed params
     * @param Array Connection data
     * @return PDO PDO object
     */
    public function getPDO($params) {
        extract($params);
        return new PDO(
            "$driver:host=$host;dbname=$dbname;charset=$charset", 
            $username, 
            $password,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]
        );
    }
}
