<?php

namespace Sata\DbTest\Test\Unit;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_Operation_IDatabaseOperation;
use PHPUnit_Framework_TestCase;
use Sata\DbTest\OperationsFacade;

class OperationsFacadeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected $operation;

    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $connection;

    /**
     * @var PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected $dataSet;
    
    public function setUp()
    {
        $this->pdo = $this->getMock('\Sata\DbTest\Test\PDOMock');
        $this->operation = $this->getMock('\PHPUnit_Extensions_Database_Operation_IDatabaseOperation');
        $this->connection = $this->getMock('\PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        $this->dataSet = $this->getMock('\PHPUnit_Extensions_Database_DataSet_IDataSet');
    }

    /**
     * @param array $mockMethods
     * 
     * @return OperationsFacade
     */
    protected function getOperationFacade($mockMethods = ['createDBUnitOperation', 'createDBUnitConnection', 'createDBUnitDataSet'])
    {
        $facade = $this->getMockBuilder('\Sata\DbTest\OperationsFacade')
            ->setConstructorArgs([$this->pdo])
            ->setMethods($mockMethods)
            ->getMock();
        
        foreach ($mockMethods as $method) {
            switch ($method) {
                case 'createDBUnitOperation':
                    $facade
                        ->expects($this->any())
                        ->method('createDBUnitOperation')
                        ->willReturn($this->operation);
                    break;
                case 'createDBUnitConnection':
                    $facade
                        ->expects($this->any())
                        ->method('createDBUnitConnection')
                        ->willReturn($this->connection);
                    break;
                case 'createDBUnitDataSet':
                    $facade
                        ->expects($this->any())
                        ->method('createDBUnitDataSet')
                        ->willReturn($this->dataSet);
                    break;
            }
        }
        
        return $facade;
    }

    public function testExecuteOperation()
    {
        $this->operation
            ->expects($this->once())
            ->method('execute');
        
        $facade = $this->getOperationFacade();
        $facade->expects($this->once())
            ->method('createDBUnitOperation')
            ->with('insert');
        $facade->expects($this->once())
            ->method('createDBUnitDataSet')
            ->with(['test data']);
        
        $facade->executeOperation('insert', ['test data']);
    }

    /**
     * @expectedException \Sata\DbTest\Exceptions\OperationNotFoundException
     */
    public function testThrowExceptionIfExecuteUnknownOperation()
    {
        $facade = $this->getOperationFacade(['createDBUnitConnection', 'createDBUnitDataSet']);

        $facade->executeOperation('not_existing_opeartion', 'data');
    }
}