<?php

namespace Sata\DbTest;

use PDO;
use PHPUnit_Extensions_Database_DataSet_ArrayDataSet;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_Operation_IDatabaseOperation;
use Sata\DbTest\Exceptions\OperationNotFoundException;

class OperationsFacade
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $dbUnitConnection;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getDBUnitConnection()
    {
        if ($this->dbUnitConnection === null) {
            $this->dbUnitConnection = $this->createDBUnitConnection();
        }
        return $this->dbUnitConnection;
    }

    /**
     * @return void
     */
    public function none()
    {
        $this->executeOperation('none');
    }

    /**
     * @param array $data
     */
    public function insert($data)
    {
        $this->executeOperation('insert', $data);
    }

    /**
     * @param array $data
     */
    public function cleanInsert($data)
    {
        $this->executeOperation('clean_insert', $data);
    }

    /**
     * @param array $data
     */
    public function update($data)
    {
        $this->executeOperation('update', $data);
    }

    /**
     * @param string[]|string $tables
     */
    public function truncate($tables)
    {
        $data = $this->simpleTablesListData($tables);
        $this->executeOperation('truncate', $data);
    }

    /**
     * @param array $condition
     */
    public function delete($condition)
    {
        $this->executeOperation('delete', $condition);
    }

    /**
     * @param string[]|string $tables
     */
    public function deleteAll($tables)
    {
        $data = $this->simpleTablesListData($tables);
        $this->executeOperation('delete_all', $data);
    }

    /**
     * @param string $operation
     * @param mixed $data
     */
    public function executeOperation($operation, $data = [])
    {
        $operation = $this->createDBUnitOperation($operation);
        $data = $this->createDBUnitDataSet($data);
        $connection = $this->getDBUnitConnection();
        $operation->execute($connection, $data);
    }

    /**
     * @param string $operation
     * 
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     * 
     * @throws OperationNotFoundException
     */
    protected function createDBUnitOperation($operation)
    {
        switch ($operation) {
            case 'none':
            case 'clean_insert':
            case 'insert':
            case 'truncate':
            case 'delete':
            case 'delete_all':
            case 'update':
                return call_user_func(['PHPUnit_Extensions_Database_Operation_Factory', strtoupper($operation)]);
        }

        throw new OperationNotFoundException($operation, 'Operation ' . $operation . ' not found');
    }

    /**
     * @param $data
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function createDBUnitDataSet($data)
    {
        return new PHPUnit_Extensions_Database_DataSet_ArrayDataSet($data);
    }

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function createDBUnitConnection()
    {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($this->pdo);
    }

    /**
     * Return data for DBUnit operations without tables data needs
     * 
     * @param string[]|string $tables
     * 
     * @return array
     */
    protected function simpleTablesListData($tables)
    {
        if (!is_array($tables)) {
            $tables = [$tables];
        }

        $data = [];
        foreach($tables as $table) {
            $data[$table] = [];
        }
        
        return $data;
    }
}