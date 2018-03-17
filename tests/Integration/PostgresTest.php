<?php

declare(strict_types=1);

namespace Project\Tests\Integration;

use PHPUnit\Framework\TestCase;

class PostgresTest extends TestCase
{
    /**
     * The FPM container must be able to open a PDO connection against
     * the Postgres container and run arbitrary SQL statements.
     *
     * This test has a side effect on the database _on purpose_.
     */
    public function testInterContainerConnectivity(): void
    {
        $pdo = new \PDO(
            "pgsql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};user={$_ENV['DB_USER']}"
        );

        self::assertTrue($pdo->beginTransaction());
        self::assertSame(0, $pdo->exec('DROP TABLE IF EXISTS foo'));
        self::assertSame(0, $pdo->exec('CREATE TABLE foo (bar INT)'));
        self::assertSame(3, $pdo->exec('INSERT INTO foo (bar) VALUES (1),(2),(3)'));
        self::assertTrue($pdo->commit());

        $stmt = $pdo->query('SELECT * FROM foo');

        self::assertInstanceOf(\PDOStatement::class, $stmt);

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        self::assertSame([
            ['bar' => 1],
            ['bar' => 2],
            ['bar' => 3]
        ], $result);
    }
}
