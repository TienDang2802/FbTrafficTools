<?php

namespace App\Component\Core\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell as SpreadsheetCell;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Cell
{
    /**
     * @param SpreadsheetCell $cell
     */
    public function __construct(SpreadsheetCell $cell)
    {
        $this->cell = $cell;
    }

    /**
     * @param null $nullValue
     * @param bool $calculateFormulas
     * @param bool $formatData
     *
     * @return mixed|string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function getValue($nullValue = null, $calculateFormulas = false, $formatData = true)
    {
        $value = $nullValue;
        if ($this->cell->getValue() !== null) {
            if ($this->cell->getValue() instanceof RichText) {
                $value = $this->cell->getValue()->getPlainText();
            } else {
                if ($calculateFormulas) {
                    $value = $this->cell->getCalculatedValue();
                } else {
                    $value = $this->cell->getValue();
                }
            }

            if ($formatData) {
                $style = $this->cell->getWorksheet()->getParent()->getCellXfByIndex($this->cell->getXfIndex());
                $value = NumberFormat::toFormattedString(
                    $value,
                    ($style && $style->getNumberFormat()) ? $style->getNumberFormat()->getFormatCode() : NumberFormat::FORMAT_GENERAL
                );
            }
        }

        return $value;
    }
}
