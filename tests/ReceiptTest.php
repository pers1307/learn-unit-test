<?php

namespace TDD\Test;

require dirname(dirname(__FILE__))
    . DIRECTORY_SEPARATOR . 'vendor'
    . DIRECTORY_SEPARATOR . 'autoload.php';

use PHPUnit\Framework\TestCase;

use TDD\Receipt;

class ReceiptTest extends TestCase
{
    /** @var Receipt */
    private $receipt;

    private $formatter;

    public function setUp()
    {
        $this->formatter = $this->getMockBuilder('TDD\Formatter')
            ->setMethods(['currencyAmt'])
            ->getMock();

        $this
            ->formatter
            ->expects($this->any())
            ->method('currencyAmt')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->receipt = new Receipt($this->formatter);
    }

    public function tearDown()
    {
        unset($this->receipt);
    }

    /**
     *
     * @dataProvider provideSubtotal Определяет какой провайдер будет юзать эти тесты
     * @param $items это входные данные
     * @param $expected это результат, который должен быть получен
     */
    public function testSubtotal($items, $expected)
    {
        $coupon = null;
        $output = $this->receipt->subtotal($items, $coupon);

        $this->assertEquals(
            $expected,
            $output,
            'When summing the total should equal ' . $expected
        );
    }

    /**
     * Провайдер со списком значений, которые передаются в тест
     * @return array
     */
    public function provideSubtotal()
    {
        return [
            [[1, 2, 5, 8], 16],
            [[-1, 2, 5, 8], 14],
            [[1, 2, 8], 11],
        ];
    }

    public function testSubtotalAndCoupon()
    {
        $input  = [0,2,5,8];
        $coupon = 0.2;
        $output = $this->receipt->subtotal($input, $coupon);

        $this->assertEquals(
            12,
            $output,
            'When summing the total should equal 15'
        );
    }

    public function testSubtotalException()
    {
        $input  = [0,2,5,8];
        $coupon = 1.20;
        /**
         * Метод должен выпасть в exception
         */
        $this->expectException('BadMethodCallException');
        $this->receipt->subtotal($input, $coupon);
    }

    /**
     * It is stub
     * Суть в том, что можно подменить методы в классе
     * и значения, которые они возвращают, тем самым
     * проверяя метод, который вызывает другие методы
     * годиться для private / protected методов
     */
    public function testPostTaxTotal()
    {
        $items  = [1, 2, 5, 8];
        $coupon = null;

        $receipt = $this->getMockBuilder('TDD\Receipt')
            ->setMethods(['tax', 'subtotal'])
            ->setConstructorArgs([$this->formatter])
            ->getMock();

        $receipt
            /**
             * Метод должен быть вызван 1 раз
             */
            ->expects($this->once())
            /**
             * Название переопределяемого метода
             */
            ->method('subtotal')
            /**
             * Метод должен быть вызван с параметрами
             */
            ->with($items, $coupon)
            /**
             * Метод должен вернуть значение
             */
            ->will($this->returnValue(10.00));

        $receipt
            ->expects($this->once())
            ->method('tax')
            ->with(10.00)
            ->will($this->returnValue(1.00));

        $result = $receipt->postTaxTotal([1, 2, 5, 8], null);

        $this->assertEquals(11.00, $result);
    }

    public function testTax()
    {
        $inputAmount = 10.00;
        $this->receipt->tax = 0.10;

        $output = $this->receipt->tax($inputAmount);

        $this->assertEquals(
            1.00,
            $output,
            'The tax calculation should equal 1.00'
        );
    }
}
