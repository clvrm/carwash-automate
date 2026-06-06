<?php


namespace app\commons\helpers;


use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSalesItem;
use app\models\ar\complex\Complexes;
use app\models\ar\service\Services;
use yii\helpers\ArrayHelper;

class SaleHelper
{
    private $carwash;
    private $currentCarwashSales;

    public function __construct($carwashId)
    {
        $this->carwash = Carwash::findOne($carwashId);
        $this->currentCarwashSales = CarwashSales::find()->where(['carwash_id' => $carwashId])->andWhere(['<=', 'start_at', date('Y-m-d')])->andWhere(['>=', 'end_at', date('Y-m-d')])->orderBy('position ASC')->all();

    }

    public function calculateTotalSales(bool $isSubscriber = false, $carType = 1, array $serviceIds = [], array $complexIds = [])
    {
        $currentSales = $this->currentCarwashSales;
        // Если пользователь не подписчик - то у него ограниченный набор акций
        if ($isSubscriber === false) {
            /** @var CarwashSales $sale */
            foreach ($currentSales as $saleKey => $sale) {
                if ($sale->only_subscribers === CarwashSales::SALE_FOR_SUBSCRIBER) {
                    unset($currentSales[$saleKey]);
                }
            }
        }

        /** @var Complexes $complexes */
        $complexes = Complexes::find()->where(['in', 'id', $complexIds])->all();
        /** @var Services $services */
        $services = Services::find()->where(['in', 'id', $serviceIds])->all();

        /** @var $canApplicableSales
         * [ saleId = 1, saleName = 'Test', totalPrice = 100, isNeedSum = false, applyGreater = true
         * complexes = [
         *          id = 1, price = 200, oldPrice = 240,
         *          id = 2, price = 100, oldPrice = 110,
         *      ],
         * services = [
         *          id = 20, price = 15, oldPrice = 20,
         *      ],
         * ], ...
         */
        $canApplicableSales = [];

        /** @var CarwashSales $sale */
        foreach ($currentSales as $sale) {
            $saleItems = CarwashSalesItem::findAll(['sale_id' => $sale->id]);
            $saleComplexesIds = ArrayHelper::map($saleItems, 'id', 'complex_id');
            $saleServicesIds = ArrayHelper::map($saleItems, 'id', 'service_id');

            $complexSaleArray = [];
            foreach ($complexes as $complex) {
                // Если данный комплекс участвует в расчете скидок, то считаем скидку
                if (in_array($complex->id, $saleComplexesIds)) {
                    $oldPrice = $newPrice = $complex->{'type_' . $carType . '_price'};
                    if ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE) {
                        $newPrice = $oldPrice - ($sale->sale ?? 0);
                    } elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT) {
                        $newPrice = floor(($oldPrice / 100) * (100 - $sale->sale));
                    }
                    if (!empty($sale->rounding_to)) {
                        $newPrice = CarwashSales::roundSaleByValue($newPrice, $sale->rounding_to);
                    }
                    // Если после применения скидки цена стала меньше чем ожидалось - то возвращаем старую цену
                    if ($newPrice <= 0) {
                        $newPrice = $oldPrice;
                    }
//                    echo 'Скидка на комплекс до суммирования ' . $complex->name . ' Было: ' . $oldPrice . ' стало ' . $newPrice;
                    $complexSaleArray[$complex->id] = ['id' => $complex->id, 'price' => $newPrice, 'oldPrice' => $oldPrice];
                }
            }

            $serviceSaleArray = [];
            foreach ($services as $service) {
                // Если данная услуга участвует в расчете скидок, то считаем скидку
                if (in_array($service->id, $saleServicesIds)) {
                    $oldPrice = $newPrice = $service->{'type_' . $carType . '_price'};
                    if ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE) {
                        $newPrice = $oldPrice - ($sale->sale ?? 0);
                    } elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT) {
                        $newPrice = floor(($oldPrice / 100) * (100 - $sale->sale));
                    }
                    if (!empty($sale->rounding_to)) {
                        $newPrice = CarwashSales::roundSaleByValue($newPrice, $sale->rounding_to);
                    }
                    // Если после применения скидки цена стала меньше чем ожидалось - то возвращаем старую цену
                    if ($newPrice <= 0) {
                        $newPrice = $oldPrice;
                    }
//                    echo 'Скидка на услугу до суммирования ' . $service->name . ' Было: ' . $oldPrice . ' стало ' . $newPrice;

                    $serviceSaleArray[$service->id] = ['id' => $service->id, 'price' => $newPrice, 'oldPrice' => $oldPrice];
                }
            }
            $totalPrice = $this->calculateTotalPriceByApplicableSale($carType, $complexes, $services, $complexSaleArray, $serviceSaleArray);

            $canApplicableSales[$sale->id] = [
                'saleId' => $sale->id,
                'saleName' => $sale->name,
                'totalPrice' => $totalPrice,
                'isNeedSum' => $sale->sum_up_discount ? true : false,
                'applyGreater' => $sale->apply_greater ? true : false,
                'complexes' => $complexSaleArray,
                'services' => $serviceSaleArray
            ];
        }

        // Суммируем скидки
        $sumUpSalesIds = [];
        foreach ($canApplicableSales as $key => $canApplicableSaleItem) {
            if ($canApplicableSaleItem['isNeedSum'] === true) {
                $sumUpSalesIds[] = $canApplicableSaleItem['saleId'];
            }
        }
        // Если скидок, которые нужно просуммировать больше одной - значит их потребуется слить.
        // Для этого удаляем все сливаемые скидки из общего пула скидок
        if (count($sumUpSalesIds) > 1) {
            foreach ($sumUpSalesIds as $saleId) {
                unset($canApplicableSales[$saleId]);
            }
        }

        // Слияние скидок, которые можно применить
        if (!empty($sumUpSalesIds)) {
            $includedServices = $this->sumUpSalesByProducts($sumUpSalesIds, $carType, $complexes, $services);
            $complexSaleArray = $includedServices['complexes'] ?? [];
            $serviceSaleArray = $includedServices['services'] ?? [];
            $totalPrice = $this->calculateTotalPriceByApplicableSale($carType, $complexes, $services, $complexSaleArray, $serviceSaleArray);

            $saleName = '';
            $saleId = 0;
            foreach ($this->currentCarwashSales as $sale) {
                if (in_array($sale->id, $sumUpSalesIds)) {
                    $saleId = $sale->id;
                    if (empty($saleName)) {
                        $saleName = $sale->name;
                    } else {
                        $saleName .= ' + ' . $sale->name;
                    }
                }
            }

            $canApplicableSales[$sale->id] = [
                'saleId' => $saleId,
                'saleName' => $saleName,
                'totalPrice' => $totalPrice,
                'isNeedSum' => false, // Уже не требуется суммировать, т.к. оно прошло
                'applyGreater' => false, // По-умолчанию
                'complexes' => $complexSaleArray ?? [],
                'services' => $serviceSaleArray ?? []
            ];

        }
        // Удаляем пустые скидки, в которых нет никаких товаров
        foreach ($canApplicableSales as $saleId => $sale) {
            if (empty($sale['complexes']) && empty($sale['services'])) {
                unset($canApplicableSales[$saleId]);
            }
        }

        // Сортируем и отсеиваем только до одной возможной скидки для товаров
        sort($canApplicableSales);
        while (count($canApplicableSales) > 1) {
            if ($canApplicableSales[0]['applyGreater']) {
                if ($canApplicableSales[0]['totalPrice'] > $canApplicableSales[1]['totalPrice']) {
                    unset($canApplicableSales[1]);
                } else {
                    unset($canApplicableSales[0]);
                }
            } else {
                if ($canApplicableSales[0]['totalPrice'] < $canApplicableSales[1]['totalPrice']) {
                    unset($canApplicableSales[1]);
                } else {
                    unset($canApplicableSales[0]);
                }
            }
            sort($canApplicableSales);
        }
        reset($canApplicableSales);

        return $canApplicableSales[0] ?? false;
    }

    private function sumUpSalesByProducts($saleIds, $carType, $complexes, $services): array
    {
        $complexesSaleArray = [];
        $servicesSaleArray = [];
        /** @var CarwashSales $sale */
        foreach ($this->currentCarwashSales as $sale) {
            // Пропускаем все скидки, которые не потребуется обрабатывать
            if (!in_array($sale->id, $saleIds)) {
                continue;
            }
            $saleItems = CarwashSalesItem::findAll(['sale_id' => $sale->id]);
            $saleComplexesIds = ArrayHelper::map($saleItems, 'id', 'complex_id');
            $saleServicesIds = ArrayHelper::map($saleItems, 'id', 'service_id');

            foreach ($complexes as $complex) {
                // Если данный комплекс участвует в расчете скидок, то считаем скидку
                if (in_array($complex->id, $saleComplexesIds)) {
                    $oldPrice = $newPrice = $complex->{'type_' . $carType . '_price'};
                    if (isset($complexesSaleArray[$complex->id]) && !empty($complexesSaleArray[$complex->id]['price'])) {
                        $newPrice = $complexesSaleArray[$complex->id]['price'];
                    }
                    if ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE) {
                        $newPrice -= ($sale->sale ?? 0);
                    } elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT) {
                        $newPrice = floor(($newPrice / 100) * (100 - $sale->sale));
                    }
                    if (!empty($sale->rounding_to)) {
                        $newPrice = CarwashSales::roundSaleByValue($newPrice, $sale->rounding_to);
                    }
                    // Если после применения скидки цена стала меньше чем ожидалось - то возвращаем старую цену
                    if ($newPrice <= 0) {
                        $newPrice = $oldPrice;
                    }
                    $complexesSaleArray[$complex->id] = ['id' => $complex->id, 'price' => $newPrice, 'oldPrice' => $oldPrice];
                }
            }

            foreach ($services as $service) {
                // Если данная услуга участвует в расчете скидок, то считаем скидку
                if (in_array($service->id, $saleServicesIds)) {
                    $oldPrice = $newPrice = $service->{'type_' . $carType . '_price'};
                    if (isset($servicesSaleArray[$service->id]) && !empty($servicesSaleArray[$service->id]['price'])) {
                        $newPrice = $servicesSaleArray[$service->id]['price'];
                    }
                    if ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE) {
                        $newPrice -= ($sale->sale ?? 0);
                    } elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT) {
                        $newPrice = floor(($newPrice / 100) * (100 - $sale->sale));
                    }
                    if (!empty($sale->rounding_to)) {
                        $newPrice = CarwashSales::roundSaleByValue($newPrice, $sale->rounding_to);
                    }
                    // Если после применения скидки цена стала меньше чем ожидалось - то возвращаем старую цену
                    if ($newPrice <= 0) {
                        $newPrice = $oldPrice;
                    }
                    $servicesSaleArray[$service->id] = ['id' => $service->id, 'price' => $newPrice, 'oldPrice' => $oldPrice];
                }
            }
        }

        return ['complexes' => $complexesSaleArray, 'services' => $servicesSaleArray];
    }


    private function calculateTotalPriceByApplicableSale($carType, $complexes, $services, $complexSaleArray, $serviceSaleArray)
    {
        $totalPrice = 0;
        if (!empty($complexes)) {
            foreach ($complexes as $complex) {
                $tempPrice = $complex->{'type_' . $carType . '_price'};
                if (!empty($complexSaleArray) && is_array($complexSaleArray) && isset($complexSaleArray[$complex->id])) {
                    if (!empty($complexSaleArray[$complex->id]['price'])) {
                        $tempPrice = $complexSaleArray[$complex->id]['price'];
                    }
                }
                $totalPrice += $tempPrice;
            }
        }

        if (!empty($services)) {
            foreach ($services as $service) {
                $tempPrice = $service->{'type_' . $carType . '_price'};
                if (!empty($services) && is_array($serviceSaleArray) && isset($serviceSaleArray[$service->id])) {
                    if (!empty($serviceSaleArray[$service->id]['price'])) {
                        $tempPrice = $serviceSaleArray[$service->id]['price'];
                    }
                }
                $totalPrice += $tempPrice;
            }
        }

        return $totalPrice ?? 0;
    }
}