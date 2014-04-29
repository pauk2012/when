<?php

use \When\DateTime as When;

class WhenCoreTest extends \PHPUnit_Framework_TestCase {

    public function testValidDateString()
    {
        $test = new When('20121010');
        $this->assertInstanceOf('\\DateTime', $test);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidDateString()
    {
        $test = new When('asdasd');
    }

    public function testValidStartDate()
    {
        $date = new DateTime();

        $test = new When($date);

        $this->assertEquals($test->format('U'), $date->format('U'));
    }

    public function testValidFreq()
    {
        $test = new When();
        $test->frequency("secondly");

        $this->assertEquals($test->frequency(), When::SECONDLY);

        // Allow uppercase.
        $test = new When();
        $test->frequency("HOURLY");

        $this->assertEquals($test->frequency(), When::HOURLY);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidFreq()
    {
        $test = new When();
        $test->frequency("monthy");
        $this->assertEquals($test->frequency(), null);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidStartDate()
    {
        $test = new When('test');
    }

    public function testValidUntil()
    {
        $date = (new DateTime())->format('U');

        $test = new When();
        $test->until($date);

        $this->assertEquals($test->until()->format('U'), $date);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidUntil()
    {
        $test = new When();
        $test->until("test");
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidDateObject()
    {
        $test = new When(new FakeObject);
    }

    /* it is important we have this working */
    public function testZuluTimeString()
    {
        $test = new When(new DateTime("19970610T172345Z"));

        $this->assertInstanceOf('\\DateTime', $test);
    }

    public function testPrepareList()
    {
        /*$method = new ReflectionMethod('When\\When', 'prepareList');
        $method->setAccessible(true);

        $response = array(1, 2, 3, 4);

        $this->assertEquals($method::prepareList(1, 2, "3", 4), $response);*/
    }

    public function testValidWkst()
    {
        $test = new When;
        $test->weekStart('mo');

        $this->assertEquals($test->weekStart(), When::MONDAY);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidWkst()
    {
        $test = new When;
        $test->weekStart('va');
    }

    public function testValidByMonthDay()
    {
        $test = new When;
        $test->monthDay(12);

        $this->assertEquals($test->monthDay(), array(12));

        $test = new When;
        $test->monthDay(-12);

        $this->assertEquals($test->monthDay(), array(-12));

        // sloppy input works
        $test = new When;
        $test->monthDay(1, '2', 3);

        $this->assertEquals($test->monthDay(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByMonthDay()
    {
        $test = new When;
        $test->monthDay('asd');
    }

    public function testValidByYearDay()
    {
        $test = new When;
        $test->yearDay(12);

        $this->assertEquals($test->yearDay(), array(12));

        $test = new When;
        $test->yearDay(-12);

        $this->assertEquals($test->yearDay(), array(-12));

        // sloppy input works
        $test = new When;
        $test->yearDay(1, '2', '3');

        $this->assertEquals($test->yearDay(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByYearDay()
    {
        $test = new When;
        $test->yearDay(367);
    }

    public function testValidByWeekNo()
    {
        $test = new When;
        $test->weekNumber(12);

        $this->assertEquals($test->weekNumber(), array(12));

        $test = new When;
        $test->weekNumber(-12);

        $this->assertEquals($test->weekNumber(), array(-12));

        // sloppy input works
        $test = new When;
        $test->weekNumber(1, '2', 3);

        $this->assertEquals($test->weekNumber(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByWeekNo()
    {
        $test = new When;
        $test->weekNumber(55);
    }

    public function testValidByMonth()
    {
        $test = new When;
        $test->month(12);

        $this->assertEquals($test->month(), array(12));

        // sloppy input works
        $test = new When;
        $test->month(1, '2', 3);

        $this->assertEquals($test->month(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByMonth()
    {
        $test = new When;
        $test->month(-1);
    }

    public function testValidBySetPos()
    {
        $test = new When;
        $test->position(12);

        $this->assertEquals($test->position(), array(12));

        $test = new When;
        $test->position(-12);

        $this->assertEquals($test->position(), array(-12));

        // sloppy input works
        $test = new When;
        $test->position(1, '2', 3);

        $this->assertEquals($test->position(), array(1, 2, 3));
    }

    public function testValidbyDay()
    {
        $test = new When;
        $test->weekDay('mo');

        $this->assertEquals($test->weekDay(), array(When::MONDAY));

        $test = new When;
        $test->weekDay('sun', 'friday');

        $this->assertEquals($test->weekDay(), array(When::SUNDAY, When::FRIDAY));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidbyDay()
    {
        $test = new When;
        $test->monthDay('fakeday');
    }

    public function testValidByHour()
    {
        $test = new When;
        $test->hour(12);

        $this->assertEquals($test->hour(), array(12));

        // sloppy input works
        $test = new When;
        $test->hour(1, '2', 3);

        $this->assertEquals($test->hour(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByHourOne()
    {
        $test = new When;
        $test->hour(24);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByHourTwo()
    {
        $test = new When;
        $test->hour(-1, -2);
    }

    public function testValidByMinute()
    {
        $test = new When;
        $test->minute(12);

        $this->assertEquals($test->minute(), array(12));

        // sloppy input works
        $test = new When;
        $test->minute(1, '2', 3);

        $this->assertEquals($test->minute(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByMinuteOne()
    {
        $test = new When;
        $test->minute(65);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidByMinuteTwo()
    {
        $test = new When;
        $test->minute(-1, -2);
    }

    public function testValidBySecond()
    {
        $test = new When;
        $test->second(12);

        $this->assertEquals($test->second(), array(12));

        // sloppy input works
        $test = new When;
        $test->second(1, '2', 3);

        $this->assertEquals($test->second(), array(1, 2, 3));
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidBySecondOne()
    {
        $test = new When;
        $test->second(65);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidBySecondTwo()
    {
        $test = new When;
        $test->second(-1, -2);
    }

    public function testValidInterval()
    {
        $test = new When;
        $test->interval(20);

        $this->assertEquals($test->interval(), 20);

        $test = new When;
        $test->interval('20');

        $this->assertEquals($test->interval(), 20);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidInterval()
    {
        $test = new When;
        $test->interval('week');
    }

    public function testValidCount()
    {
        $test = new When;
        $test->count(20);

        $this->assertEquals($test->count(), 20);

        $test = new When;
        $test->count('20');

        $this->assertEquals($test->count(), 20);
    }

    /**
     * @expectedException When\Exceptions\InvalidArgument
     */
    public function testInvalidCount()
    {
        $test = new When;
        $test->count('weekly');
    }
}

class FakeObject {}
