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

    public function testNone()
    {
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('none');

        $facade->none();
    }

    public function testInsert()
    {
        $data = [
            'table1' => [
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
            ]
        ];
        
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('insert', $data);

        $facade->insert($data);
    }

    public function testTruncateManyTables()
    {
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('truncate', [
                'table1' => [],
                'table2' => [],
            ]);

        $facade->truncate(['table1', 'table2']);
    }

    public function testTruncateOneTables()
    {
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('truncate', [
                'table1' => [],
            ]);

        $facade->truncate('table1');
    }

    public function testDelete()
    {
        $condition = [
            'table1' => [
                [
                    'id' => 'some id',
                ],
            ]
        ];
        
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('delete', $condition);

        $facade->delete($condition);
    }

    public function testDeleteAllManyTables()
    {
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('delete_all', [
                'table1' => [],
                'table2' => [],
            ]);

        $facade->deleteAll(['table1', 'table2']);
    }

    public function testDeleteAllOneTables()
    {
        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('delete_all', [
                'table1' => [],
            ]);

        $facade->deleteAll('table1');
    }

    public function testUpdate()
    {
        $data = [
            'table1' => [
                [
                    'id' => 'some id',
                    'field2' => 'modified value',
                ],
            ]
        ];

        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('update', $data);

        $facade->update($data);
    }

    public function testCleanInsert()
    {
        $data = [
            'table1' => [
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
            ]
        ];

        $facade = $this->getOperationFacade(['executeOperation']);
        $facade->expects($this->once())
            ->method('executeOperation')
            ->with('clean_insert', $data);

        $facade->cleanInsert($data);
    }
}