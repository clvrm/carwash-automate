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
 * Class FinanceServiceSpreadsheetCreator
 * @package app\commons\models\analytics\factory
 */
class FinanceServiceSpreadsheetCreator extends SpreadsheetCreator
{

    protected const TEMPLATE_NAME = 'finance-service.xlsx';
    protected const START_ROW = 4;
    private $data = [];

    /**
     * FinanceServiceSpreadsheetCreator constructor.
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
            $serviceName = $datum['name'] ?? 'Не задано';
            $serviceSum = $datum['serviceSum'] ?? 0;
            $totalServices = $datum['totalServices'] ?? 0;

            $sheet->setCellValue('A' . $currentRow, $serviceName);
            $sheet->setCellValue('B' . $currentRow, $serviceSum);
            $sheet->setCellValue('C' . $currentRow, $totalServices);
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