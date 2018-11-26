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

namespace OmegaCode\PhpParsecsvValidator;

use ParseCsv\Csv;

/**
 * Class CsvValidator.
 */
class CsvValidator
{
    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * CsvValidator constructor.
     *
     * @param Csv $csv
     */
    public function __construct(Csv $csv)
    {
        $this->csv = $csv;
    }

    /**
     * @param array $expectedHeaders
     *
     * @return $this
     */
    public function validateHeadersAreEqual(array $expectedHeaders)
    {
        if (count($this->csv->titles) <= 0) {
            $this->addError(
                0,
                new \Exception('Actual headers count is 0', 1542991949)
            );
        }
        if (count($this->csv->titles) != count($expectedHeaders)) {
            $this->addError(
                0,
                new \Exception('Actual headers count is not equal expected headers count', 1542991950)
            );
        }
        $diff = array_merge(
            array_diff($this->csv->titles, $expectedHeaders),
            array_diff($expectedHeaders, $this->csv->titles)
        );
        if (count($diff) > 0) {
            $this->addError(
                0,
                new \Exception('Actual headers not equal expected headers', 1542991951)
            );
        }

        return $this;
    }

    /**
     * This method checks if each row has the same headers as the expected headers.
     *
     * @param array $expectedHeaders
     *
     * @return $this
     */
    public function validateDataStructure(array $expectedHeaders)
    {
        if (count($this->csv->data) <= 0) {
            $this->addError(
                0,
                new \Exception('Data is equal 0', 1542991952)
            );
        }
        $index = 1;
        /** @var array $row */
        foreach ($this->csv->data as $row) {
            $this->validateDataRow($index, $row, $expectedHeaders);
            ++$index;
        }

        return $this;
    }

    /**
     * @param array $requiredHeaders
     *
     * @return $this
     */
    public function validateDataForRequiredHeaders(array $requiredHeaders)
    {
        $index = 1;
        /** @var array $row */
        foreach ($this->csv->data as $row) {
            $this->validateRowForRequiredHeaders($index, $row, $requiredHeaders);
            ++$index;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param int   $index
     * @param array $row
     * @param array $expectedKeys
     */
    private function validateDataRow($index, array $row, array $expectedKeys)
    {
        if (count($row) != count($expectedKeys)) {
            $this->addError(
                $index,
                new \Exception('Data row count is not equal expected keys count', 1542991955)
            );
        }
        $diff = array_merge(
            array_diff(array_keys($row), $expectedKeys),
            array_diff($expectedKeys, array_keys($row))
        );
        if (count($diff) > 0) {
            $this->addError(
                $index,
                new \Exception('Data row keys are not equal expected keys', 1542991956)
            );
        }
    }

    /**
     * @param int   $index
     * @param array $row
     * @param array $requiredHeaders
     */
    private function validateRowForRequiredHeaders($index, array $row, array $requiredHeaders)
    {
        /** @var string $requiredHeader */
        foreach ($requiredHeaders as $requiredHeader) {
            if (!isset($row[$requiredHeader])) {
                $this->addError(
                    $index,
                    new \Exception("Data row missing required field *$requiredHeader*", 1542991957)
                );
                continue;
            }
            if (empty($row[$requiredHeader])) {
                $this->addError(
                    $index,
                    new \Exception("Data row required *$requiredHeader* field has no content", 1542991958)
                );
            }
        }
    }

    /**
     * @param int        $line
     * @param \Exception $exception
     */
    private function addError($line, \Exception $exception)
    {
        $this->errors[] = new ValidatorError($line, $exception);
    }
}
