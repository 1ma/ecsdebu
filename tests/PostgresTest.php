<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class PostgresTest extends TestCase
{
    public function testInterContainerNetworking(): void
    {
        $pdo = new \PDO('pgsql:host=postgres;port=5432;dbname=postgres;user=postgres');

        self::assertSame(0, $pdo->exec('DROP TABLE IF EXISTS foo'));
        self::assertSame(0, $pdo->exec('CREATE TABLE foo (id INT)'));
        self::assertSame(3, $pdo->exec('INSERT INTO foo (id) VALUES (1),(2),(3)'));

        $result = $pdo->query('SELECT * FROM foo');

        self::assertInstanceOf(\PDOStatement::class, $result);

        self::assertSame(
            [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3]
            ],
            $result->fetchAll(\PDO::FETCH_ASSOC)
        );
    }
}
