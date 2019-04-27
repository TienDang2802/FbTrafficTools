<?php

namespace App\Component\Core\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Row as SpreadsheetRow;

class Row
{
    /**
     * @var array
     */
    protected $headingRow = [];
    /**
     * @var SpreadsheetRow
     */
    private $row;

    public function __construct(SpreadsheetRow $row, array $headingRow = [])
    {
        $this->row = $row;
        $this->headingRow = $headingRow;
    }

    /**
     * @param null $nullValue
     * @param bool $calculateFormulas
     * @param bool $formatData
     *
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function toArray($nullValue = null, $calculateFormulas = false, $formatData = true): array
    {
        $cells = [];

        foreach ($this->row->getCellIterator() as $i => $cell) {
            $value = (new Cell($cell))->getValue($nullValue, $calculateFormulas, $formatData);

            if (isset($this->headingRow[$i])) {
                $cells[$this->headingRow[$i]] = $value;
            } else {
                $cells[] = $value;
            }
        }

        return $cells;
    }
}
