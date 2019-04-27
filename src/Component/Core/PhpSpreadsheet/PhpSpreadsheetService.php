<?php

namespace App\Component\Core\PhpSpreadsheet;

use Onurb\Bundle\ExcelBundle\Factory\ExcelFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\File\File;

class PhpSpreadsheetService
{
    /**
     * @var ExcelFactory
     */
    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param File $file
     *
     * @param int|null $startRow
     * @param null $endRow
     *
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function readFile(File $file, int $startRow = 1, $endRow = null): array
    {
        /** @var IReader $reader */
        $reader = $this->client->createReader(ucfirst($file->getExtension()));

        /** @var Spreadsheet $spreadsheet */
        $spreadsheet = $reader->load($file->getRealPath());

        $worksheet = $spreadsheet->getActiveSheet();

        $rows = [];

        foreach ($worksheet->getRowIterator($startRow, $endRow) as $row) {
            $row = (new Row($row))->toArray(null, false, false);

            $rows[] = $row;
        }

        return $rows;
    }
}
