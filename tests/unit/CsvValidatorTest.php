<?php
/*******************************************************************************
 * Copyright (c) 2018 Wolf-Peter Utz.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 ******************************************************************************/

namespace OmegaCode\PhpParsecsvValidator\tests\unit;

use OmegaCode\PhpParsecsvValidator\CsvValidator;
use ParseCsv\Csv;
use PHPUnit\Framework\TestCase;

/**
 * Class CsvValidatorTest.
 */
class CsvValidatorTest extends TestCase
{
    /**
     * @var CsvValidator
     */
    private $subject;

    /**
     * @var Csv
     */
    private static $csv;

    /**
     * @var Csv
     */
    private static $invalidCsv;

    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self::$csv = new Csv();
        self::$csv->encoding('UTF-8', 'UTF-8');
        self::$csv->delimiter = ';';
        self::$csv->parse(__DIR__.'/../fixtures/valid.csv');
        self::$invalidCsv = new Csv();
        self::$invalidCsv->encoding('UTF-8', 'UTF-8');
        self::$invalidCsv->delimiter = ';';
        self::$invalidCsv->parse(__DIR__.'/../fixtures/invalid.csv');
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->subject = new CsvValidator(self::$csv);
    }

    /**
     * @test
     */
    public function testThatValidateHeadersAreEqualIHeadersAreEqual()
    {
        $headers = ['header1', 'header2', 'header3'];
        $this->subject->validateHeadersAreEqual($headers);
        $this->assertEquals(0, count($this->subject->getErrors()));
    }

    /**
     * @test
     */
    public function testValidateDataStructure()
    {
        $headers = ['header1', 'header2', 'header3'];
        $this->subject->validateDataStructure($headers);
        $this->assertEquals(0, count($this->subject->getErrors()));
    }

    /**
     * @test
     */
    public function testValidateDataForRequiredHeaders()
    {
        $headers = ['header1', 'header2', 'header3'];
        $this->subject->validateDataForRequiredHeaders($headers);
        $this->assertEquals(0, count($this->subject->getErrors()));
    }

    /**
     * @test
     */
    public function testValidateDataForRequiredHeadersWithWrongHeaders()
    {
        $headers = ['header11', 'header21', 'header31'];
        $this->subject->validateDataForRequiredHeaders($headers);
        $this->assertGreaterThan(0, count($this->subject->getErrors()));
    }

    /**
     * @test
     */
    public function testThatEmptyFileBuildErrors()
    {
        $emptyCsv = new Csv();
        $emptyCsv->parse('');
        $this->subject = new CsvValidator($emptyCsv);

        $headers = ['header1', 'header2', 'header3'];
        $this->subject
            ->validateHeadersAreEqual($headers)
            ->validateDataStructure($headers)
            ->validateDataForRequiredHeaders($headers)
        ;
        $this->assertGreaterThan(0, count($this->subject->getErrors()));
    }

    /**
     * @test
     */
    public function testThatValidationCanFail()
    {
        $this->subject = new CsvValidator(self::$invalidCsv);
        $headers = ['header1', 'header2', 'header3', 'header4'];
        $this->subject
            ->validateHeadersAreEqual($headers)
            ->validateDataStructure($headers)
            ->validateDataForRequiredHeaders($headers)
        ;
        $this->assertGreaterThan(0, count($this->subject->getErrors()));
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        self::$csv = null;
        self::$invalidCsv = null;
    }
}
