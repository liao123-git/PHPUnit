<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CsvFileIterator implements Iterator
{
    private $file;
    private $key = 0;
    private $current;

    public function __construct(string $file)
    {
        $this->file = fopen($file, 'r');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function rewind(): void
    {
        rewind($this->file);

        $this->current = fgetcsv($this->file);
        $this->key = 0;
    }

    public function valid(): bool
    {
        return !feof($this->file);
    }

    public function key(): int
    {
        return $this->key;
    }

    public function current(): array
    {
        foreach ($this->current as $key => $item) {
            $this->current[$key] = (int)$item;
        }

        return $this->current;
    }

    public function next(): void
    {
        $this->current = fgetcsv($this->file);

        $this->key++;
    }
}


final class DataTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testAdd(int $a, int $b, int $expected): void
    {
        $this->assertSame($expected, $a + $b);
    }

    public function additionProvider(): CsvFileIterator
    {
        return new CsvFileIterator('public/test.csv');
    }

    public function testExpectFooActualFoo(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            ['1', 2, 33, 4, 5, 6]
        );
    }
}
