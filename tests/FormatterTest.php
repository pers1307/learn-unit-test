<?php

namespace TDD\Test;

require dirname(dirname(__FILE__))
    . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;

use TDD\Formatter;

class FormatterTest extends TestCase
{
    /** @var  */
    private $formatter;

    public function setUp()
    {
        $this->formatter = new Formatter;
    }

    public function tearDown()
    {
        unset($this->formatter);
    }

    /**
     * @dataProvider provideCurrencyAmt
     */
    public function testCurrencyAmt($input, $expected, $msg)
    {
        $this->assertSame(
            $expected,
            $this->formatter->currencyAmt($input),
            $msg
        );
    }

    public function provideCurrencyAmt()
    {
        return [
            [1, 1.00, '1 should be transform into 1.00'],
            [1.1, 1.10, '1.1 should be transform into 1.10'],
            [1.11, 1.11, '1.11 should be transform into 1.11'],
            [1.111, 1.11, '1.111 should be transform into 1.11'],
        ];
    }
}
