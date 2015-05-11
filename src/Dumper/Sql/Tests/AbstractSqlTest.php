<?php

namespace Digilist\SnakeDumper\Dumper\Sql\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractSqlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var AbstractPlatform
     */
    protected $platform;

    public function setUp()
    {
        parent::setUp();

        $pdo = new \PDO('sqlite::memory:');
        $pdo->query('PRAGMA foreign_keys=ON;');
        $this->connection = DriverManager::getConnection(array(
            'pdo' => $pdo
        ));
        $this->platform = $this->connection->getDatabasePlatform();
    }

    /**
     * Create a simple schema the tests can run against.
     *
     * if $randomTables is true, some other tables will be created.
     *
     * @param bool $randomTables
     */
    protected function createTestSchema($randomTables = false)
    {
        $pdo = $this->connection->getWrappedConnection();

        $pdo->query('CREATE TABLE Customer (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(10)
        )');
        $pdo->query('CREATE TABLE Billing (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_id INTEGER,
            product VARCHAR(100),
            amount REAL,
            FOREIGN KEY (customer_id) REFERENCES Customer(id)
        )');
        $pdo->query('CREATE INDEX billing_product ON Billing (product)');

        if ($randomTables) {
            $pdo->query('CREATE TABLE RandomTable (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(10)
            )');
                $pdo->query('CREATE TABLE RandomTable2 (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(10)
            )');
        }

        // insert data
        $pdo->query('INSERT INTO Customer VALUES (1, "Markus")');
        $pdo->query('INSERT INTO Customer VALUES (2, "Konstantin")');
        $pdo->query('INSERT INTO Customer VALUES (3, "John")');
        $pdo->query('INSERT INTO Customer VALUES (4, "Konrad")');
        $pdo->query('INSERT INTO Customer VALUES (5, "Mark")');
        $pdo->query('INSERT INTO Billing VALUES (1, 1, "IT", 42)');
        $pdo->query('INSERT INTO Billing VALUES (2, 1, NULL, 1337)');
        $pdo->query('INSERT INTO Billing VALUES (3, 2, "Some stuff", 1337)');
        $pdo->query('INSERT INTO Billing VALUES (4, 5, "Another stuff", 1337)');
        $pdo->query('INSERT INTO Billing VALUES (5, 5, "LOLj stuff", 1337)');

        if ($randomTables) {
            $pdo->query('INSERT INTO RandomTable VALUES (1, "Foo")');
            $pdo->query('INSERT INTO RandomTable VALUES (2, "Bar")');
            $pdo->query('INSERT INTO RandomTable2 VALUES (1, "FooBar")');
        }
    }
}