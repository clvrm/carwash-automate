<?php


namespace app\commons\models\analytics\factory;


use app\models\ar\order\Orders;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class OrdersSpreadsheetCreator
 * @package app\commons\models\analytics\factory
 */
class OrdersSpreadsheetCreator extends SpreadsheetCreator
{
    protected const TEMPLATE_NAME = 'orders-default.xlsx';
    protected const START_ROW = 4;
    private $orders = [];

    /**
     * OrdersSpreadsheetCreator constructor.
     * @param $orders
     */
    public function __construct($orders)
    {
        $this->orders = $orders;
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
        foreach ($this->orders as $order) {
            /** @var Orders $order */
            $orderId = (string)$order->id ?? 'Не указан';
            $carNumber = ($order->car_number ?? ' --- ') . ' ' . ($order->car_region ?? ' --- ');
            $status = $order->currentStatusLabel() ?? 'Не указан';
            $createdAt = $order->created_at ?? 'Не указана';
            $price = $order->total_price ?? '0';
            $post = $order->post ?? 'Не указан';
            $date = $order->date ?? 'Не указана';
            $review = ($order->getChats()->one() ? 'Есть' : 'Нет') ?? 'Нет';

            $sheet->setCellValue('A' . $currentRow, $orderId);
            $sheet->setCellValue('B' . $currentRow, $carNumber);
            $sheet->setCellValue('C' . $currentRow, $status);
            $sheet->setCellValue('D' . $currentRow, $createdAt);
            $sheet->setCellValue('E' . $currentRow, $price);
            $sheet->setCellValue('F' . $currentRow, $post);
            $sheet->setCellValue('G' . $currentRow, $date);
            $sheet->setCellValue('H' . $currentRow, $review);
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