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
 * Class ClientsSpreadsheetCreator
 * @package app\commons\models\analytics\factory
 */
class ClientsSpreadsheetCreator extends SpreadsheetCreator
{
    protected const TEMPLATE_NAME = 'clients-default.xlsx';
    protected const START_ROW = 4;
    private $orders = [];

    /**
     * ClientsSpreadsheetCreator constructor.
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

            $totalOrdersCount = Orders::find()->where(['carwash_id' => Yii::$app->user->identity->getCWid()])
                ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
                ->count();

            $totalOrdersSum = (new Query())
                ->select('SUM(total_price) as total')
                ->from('orders')
                ->where(['carwash_id' => Yii::$app->user->identity->getCWid()])
                ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
                ->groupBy(new Expression("CONCAT(orders.car_number, orders.car_region)"))
                ->one();
            $totalOrdersSum = $totalOrdersSum['total'] ?? 0;

            $isSubscriber = ClientHelper::isSubscriberByCarNumber(Yii::$app->user->identity->getCWid(),
                $order->car_number, $order->car_region);

            $carNumber = ($order->car_number ?? ' --- ') . ' ' . ($order->car_region ?? ' --- ');
            $isSubscriber = $isSubscriber ? 'Подписчик' : 'Клиент';

            $sheet->setCellValue('A' . $currentRow, $carNumber);
            $sheet->setCellValue('B' . $currentRow, $isSubscriber);
            $sheet->setCellValue('C' . $currentRow, $totalOrdersCount ?? 0);
            $sheet->setCellValue('D' . $currentRow, $totalOrdersSum ?? 0);
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