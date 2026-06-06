<?php


namespace app\commons\models\analytics\factory;


use app\commons\helpers\ClientHelper;
use app\models\ar\order\Orders;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use yii\base\BaseObject;
use yii\db\Expression;
use yii\db\Query;
use Yii;

/**
 * Class FinancePersonalSpreadsheetCreator
 * @package app\commons\models\analytics\factory
 */
class FinancePersonalSpreadsheetCreator extends SpreadsheetCreator
{
    protected const TEMPLATE_NAME = 'finance-personal.xlsx';
    protected const START_ROW = 4;
    private $data = [];

    /**
     * FinancePersonalSpreadsheetCreator constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function printDocument()
    {
        $templatePath = $this->getTemplateFile();
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);

        // Первая страница
        $index = 0;
        $spreadsheet->setActiveSheetIndex($index);
        $locale = 'ru';
        $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale($locale);

        $sheet = $spreadsheet->getActiveSheet();

        $currentRow = self::START_ROW;
        foreach ($this->data as $datum) {
            $personalName = $datum['personalName'] ?? 'Не задано';
            $personalSum = $datum['personalSum'] ?? 0;
            $personalTotalOrders = $datum['totalOrders'] ?? 0;
            $personalSalary = $datum['personalSalary'] ?? 'не указана';
            $workDays = $datum['workDays'] ?? 0;

            $sheet->setCellValue('A' . $currentRow, $personalName);
            $sheet->setCellValue('B' . $currentRow, $personalSum);
            $sheet->setCellValue('C' . $currentRow, $personalTotalOrders);
            $sheet->setCellValue('D' . $currentRow, $personalSalary);
            $sheet->setCellValue('E' . $currentRow, $workDays);
            $currentRow++;
        }

        // Открываем первый лист по-умолчанию
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $fileLink = \Yii::getAlias('@analytics') . 'tables/' . date('Y-m-d_H.i.s') . rand(1111, 9999) . self::TEMPLATE_NAME;
        $writer->save($fileLink);

        return $fileLink;
    }
}