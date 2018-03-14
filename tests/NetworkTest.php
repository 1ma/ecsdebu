<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class NetworkTest extends TestCase
{
    public function testInterContainerNetworking()
    {
        $pdo = new \PDO('pgsql:host=postgres;port=5432;dbname=postgres;user=postgres');

        $pdo->exec('DROP TABLE IF EXISTS foo');
        $pdo->exec('CREATE TABLE foo (id INT)');
        $pdo->exec('INSERT INTO foo (id) VALUES (1),(2),(3)');

        $result = $pdo->query('SELECT * FROM foo')
            ->fetchAll(\PDO::FETCH_ASSOC);

        self::assertSame([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ], $result);
    }
}
