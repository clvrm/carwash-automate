<?php


namespace app\commons\models;


use app\commons\models\analytics\factory\ClientsSpreadsheetCreator;
use app\commons\models\analytics\factory\FinanceComplexSpreadsheetCreator;
use app\commons\models\analytics\factory\FinancePersonalSpreadsheetCreator;
use app\commons\models\analytics\factory\FinanceServiceSpreadsheetCreator;
use app\commons\models\analytics\factory\OrdersSpreadsheetCreator;

/**
 * Class AnalyticsSpreadsheet
 * @package app\commons\models
 */
class AnalyticsSpreadsheet
{
    /**
     * @param $orders
     * @return false|string
     */
    public function printOrdersTable($orders)
    {
        $spreadsheet = new OrdersSpreadsheetCreator($orders);
        $fileLink = $spreadsheet->printDocument();
        if (!$fileLink){
            return false;
        }
        return $fileLink;
    }

    /**
     * @param $orders
     * @return false|string
     */
    public function printClientsTable($orders)
    {
        $spreadsheet = new ClientsSpreadsheetCreator($orders);
        $fileLink = $spreadsheet->printDocument();
        if (!$fileLink){
            return false;
        }
        return $fileLink;
    }

    /**
     * @param $data
     * @return false|string
     */
    public function printFinancePersonalTable($data)
    {
        $spreadsheet = new FinancePersonalSpreadsheetCreator($data);
        $fileLink = $spreadsheet->printDocument();
        if (!$fileLink){
            return false;
        }
        return $fileLink;
    }

    /**
     * @param $data
     * @return false|string
     */
    public function printFinanceServiceTable($data)
    {
        $spreadsheet = new FinanceServiceSpreadsheetCreator($data);
        $fileLink = $spreadsheet->printDocument();
        if (!$fileLink){
            return false;
        }
        return $fileLink;
    }

    /**
     * @param $data
     * @return false|string
     */
    public function printFinanceComplexTable($data)
    {
        $spreadsheet = new FinanceComplexSpreadsheetCreator($data);
        $fileLink = $spreadsheet->printDocument();
        if (!$fileLink){
            return false;
        }
        return $fileLink;
    }

}