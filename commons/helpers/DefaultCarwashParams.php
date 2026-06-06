<?php

namespace app\commons\helpers;

use app\models\ar\Clients;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexServices;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use app\models\ar\Users;

class DefaultCarwashParams
{
    protected $carwashId;

    public function __construct($carwashId)
    {
        $this->carwashId = $carwashId;
    }

    public function createDefaultPrices()
    {
        $washServices = [
            ['name' => 'Мойка кузова напором', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 200, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 250, 'time' => 10],
                CarTypeHelper::TYPE_SUV => ['price' => 300, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 370, 'time' => 20],
                CarTypeHelper::TYPE_OTHER => ['price' => 200, 'time' => 10],
            ]],
            ['name' => 'Мойка кузова с шампунем', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 300, 'time' => 20],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 350, 'time' => 20],
                CarTypeHelper::TYPE_SUV => ['price' => 400, 'time' => 30],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 500, 'time' => 40],
                CarTypeHelper::TYPE_OTHER => ['price' => 300, 'time' => 20],
            ]],
            ['name' => 'Мойка двигателя', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 350, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 350, 'time' => 15],
                CarTypeHelper::TYPE_SUV => ['price' => 350, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 350, 'time' => 15],
                CarTypeHelper::TYPE_OTHER => ['price' => 350, 'time' => 15],
            ]],
            ['name' => 'Мойка ковров', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 150, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 200, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 250, 'time' => 5],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 300, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 150, 'time' => 5],
            ]],
            ['name' => 'Очистка стекол', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 110, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 130, 'time' => 7],
                CarTypeHelper::TYPE_SUV => ['price' => 150, 'time' => 7],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_OTHER => ['price' => 110, 'time' => 15],
            ]],
            ['name' => 'Очистка стекол от насекомых', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 150, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 170, 'time' => 15],
                CarTypeHelper::TYPE_SUV => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 300, 'time' => 30],
                CarTypeHelper::TYPE_OTHER => ['price' => 150, 'time' => 5],
            ]],
            ['name' => 'Влажная уборка салона', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 170, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 220, 'time' => 15],
                CarTypeHelper::TYPE_SUV => ['price' => 270, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 350, 'time' => 20],
                CarTypeHelper::TYPE_OTHER => ['price' => 170, 'time' => 15],
            ]],
            ['name' => 'Уборка салона пылесосом', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 210, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 260, 'time' => 10],
                CarTypeHelper::TYPE_SUV => ['price' => 310, 'time' => 10],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 360, 'time' => 20],
                CarTypeHelper::TYPE_OTHER => ['price' => 210, 'time' => 10],
            ]],
            ['name' => 'Уборка багажника', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 250, 'time' => 7],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 250, 'time' => 7],
                CarTypeHelper::TYPE_SUV => ['price' => 300, 'time' => 7],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 370, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 250, 'time' => 7],
            ]],
            ['name' => 'Очистка пластиковых элементов салона', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 183, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 223, 'time' => 12],
                CarTypeHelper::TYPE_SUV => ['price' => 250, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 350, 'time' => 20],
                CarTypeHelper::TYPE_OTHER => ['price' => 183, 'time' => 10],
            ]],
            ['name' => 'Мойка моторного отсека', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 900, 'time' => 60],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1000, 'time' => 60],
                CarTypeHelper::TYPE_SUV => ['price' => 1200, 'time' => 90],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 1400, 'time' => 90],
                CarTypeHelper::TYPE_OTHER => ['price' => 900, 'time' => 60],
            ]],
            ['name' => 'Продувка воздухом', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 50, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 50, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 60, 'time' => 10],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 70, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 50, 'time' => 5],
            ]],
            ['name' => 'Мойка колесных дисков изнутри', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_SUV => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 100, 'time' => 10],
            ]],
            ['name' => 'Мойка запасного колеса', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 70, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 70, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 80, 'time' => 5],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 90, 'time' => 5],
                CarTypeHelper::TYPE_OTHER => ['price' => 70, 'time' => 5],
            ]],
            ['name' => 'Подкачка колес с проверкой давления (за 1 колесо)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 20, 'time' => 2],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 20, 'time' => 2],
                CarTypeHelper::TYPE_SUV => ['price' => 20, 'time' => 3],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 20, 'time' => 3],
                CarTypeHelper::TYPE_OTHER => ['price' => 20, 'time' => 2],
            ]],
            ['name' => 'Мойка арок колес напором', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_SUV => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 100, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 100, 'time' => 10],
            ]],
            ['name' => 'Мойка днища', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 800, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 800, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 800, 'time' => 5],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 800, 'time' => 5],
                CarTypeHelper::TYPE_OTHER => ['price' => 800, 'time' => 5],
            ]],
        ];

        $washComplexs = [
            ['name' => 'Экспресс',
                'services' => [
                    'Мойка кузова напором', 'Мойка ковров',
                ],
                'prices' => [
                    CarTypeHelper::TYPE_SEDAN => ['price' => 150, 'time' => 5],
                    CarTypeHelper::TYPE_CROSSOVERS => ['price' => 150, 'time' => 7],
                    CarTypeHelper::TYPE_SUV => ['price' => 200, 'time' => 10],
                    CarTypeHelper::TYPE_MINIBUSES => ['price' => 200, 'time' => 10],
                    CarTypeHelper::TYPE_OTHER => ['price' => 150, 'time' => 5],
                ]
            ],
            ['name' => 'Эконом',
                'services' => [
                    'мойка кузова напором', 'мойка кузова с  шампунем', 'мойка ковров',
                ],
                'prices' => [
                    CarTypeHelper::TYPE_SEDAN => ['price' => 400, 'time' => 35],
                    CarTypeHelper::TYPE_CROSSOVERS => ['price' => 500, 'time' => 35],
                    CarTypeHelper::TYPE_SUV => ['price' => 550, 'time' => 40],
                    CarTypeHelper::TYPE_MINIBUSES => ['price' => 650, 'time' => 40],
                    CarTypeHelper::TYPE_OTHER => ['price' => 400, 'time' => 35],
                ]
            ],
            ['name' => 'Стандарт',
                'services' => [
                    'мойка кузова напором', 'мойка кузова с шампунем', 'мойка ковров', 'влажная уборка салона'
                ],
                'prices' => [
                    CarTypeHelper::TYPE_SEDAN => ['price' => 650, 'time' => 45],
                    CarTypeHelper::TYPE_CROSSOVERS => ['price' => 700, 'time' => 50],
                    CarTypeHelper::TYPE_SUV => ['price' => 800, 'time' => 55],
                    CarTypeHelper::TYPE_MINIBUSES => ['price' => 950, 'time' => 55],
                    CarTypeHelper::TYPE_OTHER => ['price' => 650, 'time' => 45],
                ]
            ],
            ['name' => 'Люкс',
                'services' => [
                    'мойка кузова напором', 'мойка кузова с шампунем', 'мойка ковров', 'влажная уборка салона', 'уборка салона пылесосом', 'уборка багажника'
                ],
                'prices' => [
                    CarTypeHelper::TYPE_SEDAN => ['price' => 1000, 'time' => 70],
                    CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1250, 'time' => 80],
                    CarTypeHelper::TYPE_SUV => ['price' => 1450, 'time' => 90],
                    CarTypeHelper::TYPE_MINIBUSES => ['price' => 1550, 'time' => 90],
                    CarTypeHelper::TYPE_OTHER => ['price' => 1000, 'time' => 70],
                ]
            ],
        ];

        $detailServices = [
            ['name' => 'Обработка замков спецсоставом', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 100, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 100, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 110, 'time' => 5],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 110, 'time' => 5],
                CarTypeHelper::TYPE_OTHER => ['price' => 100, 'time' => 5],
            ]],
            ['name' => 'Чистка стекол салона изнутри', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 200, 'time' => 20],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 200, 'time' => 25],
                CarTypeHelper::TYPE_SUV => ['price' => 250, 'time' => 30],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 300, 'time' => 30],
                CarTypeHelper::TYPE_OTHER => ['price' => 200, 'time' => 20],
            ]],
            ['name' => 'Удаление битумных, масляных пятен (1 деталь)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_SUV => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_OTHER => ['price' => 200, 'time' => 15],
            ]],
            ['name' => 'Обработка уплотнителей дверей силиконом', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 200, 'time' => 5],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 200, 'time' => 5],
                CarTypeHelper::TYPE_SUV => ['price' => 200, 'time' => 5],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 200, 'time' => 5],
                CarTypeHelper::TYPE_OTHER => ['price' => 200, 'time' => 5],
            ]],
            ['name' => 'Чернение шин', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 200, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 250, 'time' => 15],
                CarTypeHelper::TYPE_SUV => ['price' => 300, 'time' => 15],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 300, 'time' => 15],
                CarTypeHelper::TYPE_OTHER => ['price' => 200, 'time' => 15],
            ]],
            ['name' => 'Нанесение защитного покрытия "антидождь"', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 1500, 'time' => 15],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1500, 'time' => 20],
                CarTypeHelper::TYPE_SUV => ['price' => 1700, 'time' => 20],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 2000, 'time' => 25],
                CarTypeHelper::TYPE_OTHER => ['price' => 1500, 'time' => 15],
            ]],
            ['name' => 'Очистка кожи салона', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 300, 'time' => 25],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 300, 'time' => 25],
                CarTypeHelper::TYPE_SUV => ['price' => 400, 'time' => 30],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 500, 'time' => 30],
                CarTypeHelper::TYPE_OTHER => ['price' => 300, 'time' => 25],
            ]],
            ['name' => 'Нанесение защитного крема на сидения', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 800, 'time' => 20],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1000, 'time' => 30],
                CarTypeHelper::TYPE_SUV => ['price' => 1200, 'time' => 40],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 1500, 'time' => 50],
                CarTypeHelper::TYPE_OTHER => ['price' => 800, 'time' => 20],
            ]],
            ['name' => 'Химчистка сидений с сушкой (1 шт.)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 700, 'time' => 120],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1000, 'time' => 120],
                CarTypeHelper::TYPE_SUV => ['price' => 1500, 'time' => 120],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 2000, 'time' => 120],
                CarTypeHelper::TYPE_OTHER => ['price' => 700, 'time' => 120],
            ]],
            ['name' => 'Химчистка дверей с сушкой (1шт)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 500, 'time' => 180],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 500, 'time' => 180],
                CarTypeHelper::TYPE_SUV => ['price' => 500, 'time' => 180],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 500, 'time' => 180],
                CarTypeHelper::TYPE_OTHER => ['price' => 500, 'time' => 180],
            ]],
            ['name' => 'Химчистка потолка (с сушкой)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 2000, 'time' => 180],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 2500, 'time' => 180],
                CarTypeHelper::TYPE_SUV => ['price' => 2500, 'time' => 180],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 3000, 'time' => 180],
                CarTypeHelper::TYPE_OTHER => ['price' => 2000, 'time' => 180],
            ]],
            ['name' => 'Химчистка багажника (с сушкой)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 1200, 'time' => 60],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1350, 'time' => 60],
                CarTypeHelper::TYPE_SUV => ['price' => 1350, 'time' => 60],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 1500, 'time' => 60],
                CarTypeHelper::TYPE_OTHER => ['price' => 1200, 'time' => 60],
            ]],
            ['name' => 'Химчистка колесных дисков (4 шт)', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 1000, 'time' => 10],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1000, 'time' => 10],
                CarTypeHelper::TYPE_SUV => ['price' => 1000, 'time' => 10],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 1000, 'time' => 10],
                CarTypeHelper::TYPE_OTHER => ['price' => 1000, 'time' => 10],
            ]],
            ['name' => 'Мойка двигателя паром', 'prices' => [
                CarTypeHelper::TYPE_SEDAN => ['price' => 1500, 'time' => 20],
                CarTypeHelper::TYPE_CROSSOVERS => ['price' => 1500, 'time' => 20],
                CarTypeHelper::TYPE_SUV => ['price' => 1500, 'time' => 20],
                CarTypeHelper::TYPE_MINIBUSES => ['price' => 1500, 'time' => 20],
                CarTypeHelper::TYPE_OTHER => ['price' => 1500, 'time' => 20],
            ]]
        ];

        $detailComplexs = [
            ['name' => 'Химчистка полная',
                'services' => ['уборка салона пылесосом', 'химчистка сидений с сушкой', 'мойка ковров',
                    'чистка стекол салона изнутри', 'химчистка дверей с сушкой', 'химчистка потолка (с сушкой)',
                    'химчистка багажника (с сушкой)', 'химчистка колесных дисков', 'мойка двигателя паром'
                ],
                'prices' => [
                    CarTypeHelper::TYPE_SEDAN => ['price' => 4500, 'time' => 480],
                    CarTypeHelper::TYPE_CROSSOVERS => ['price' => 5000, 'time' => 480],
                    CarTypeHelper::TYPE_SUV => ['price' => 6000, 'time' => 480],
                    CarTypeHelper::TYPE_MINIBUSES => ['price' => 7000, 'time' => 480],
                    CarTypeHelper::TYPE_OTHER => ['price' => 4500, 'time' => 480],
                ]
            ],
        ];

        foreach ($washServices as $data) {
            $service = new Services();
            $service->carwash_id = $this->carwashId;
            $service->is_detailing = false;
            $service->name = $data['name'];

            $service->type_1_price = $data['prices'][CarTypeHelper::TYPE_SEDAN]['price'];
            $service->type_1_time = $data['prices'][CarTypeHelper::TYPE_SEDAN]['time'];
            $service->type_2_price = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['price'];
            $service->type_2_time = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['time'];
            $service->type_3_price = $data['prices'][CarTypeHelper::TYPE_SUV]['price'];
            $service->type_3_time = $data['prices'][CarTypeHelper::TYPE_SUV]['time'];
            $service->type_4_price = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['price'];
            $service->type_4_time = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['time'];
            $service->type_5_price = $data['prices'][CarTypeHelper::TYPE_OTHER]['price'];
            $service->type_5_time = $data['prices'][CarTypeHelper::TYPE_OTHER]['time'];
            $service->save();
        }

        foreach ($detailServices as $data) {
            $service = new Services();
            $service->carwash_id = $this->carwashId;
            $service->is_detailing = true;
            $service->name = $data['name'];

            $service->type_1_price = $data['prices'][CarTypeHelper::TYPE_SEDAN]['price'];
            $service->type_1_time = $data['prices'][CarTypeHelper::TYPE_SEDAN]['time'];
            $service->type_2_price = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['price'];
            $service->type_2_time = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['time'];
            $service->type_3_price = $data['prices'][CarTypeHelper::TYPE_SUV]['price'];
            $service->type_3_time = $data['prices'][CarTypeHelper::TYPE_SUV]['time'];
            $service->type_4_price = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['price'];
            $service->type_4_time = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['time'];
            $service->type_5_price = $data['prices'][CarTypeHelper::TYPE_OTHER]['price'];
            $service->type_5_time = $data['prices'][CarTypeHelper::TYPE_OTHER]['time'];
            $service->save();
        }

        foreach ($washComplexs as $data) {
            $complex = new Complexes();
            $complex->carwash_id = $this->carwashId;
            $complex->is_detailing = false;
            $complex->name = $data['name'];

            $complex->type_1_price = $data['prices'][CarTypeHelper::TYPE_SEDAN]['price'];
            $complex->type_1_time = $data['prices'][CarTypeHelper::TYPE_SEDAN]['time'];
            $complex->type_2_price = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['price'];
            $complex->type_2_time = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['time'];
            $complex->type_3_price = $data['prices'][CarTypeHelper::TYPE_SUV]['price'];
            $complex->type_3_time = $data['prices'][CarTypeHelper::TYPE_SUV]['time'];
            $complex->type_4_price = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['price'];
            $complex->type_4_time = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['time'];
            $complex->type_5_price = $data['prices'][CarTypeHelper::TYPE_OTHER]['price'];
            $complex->type_5_time = $data['prices'][CarTypeHelper::TYPE_OTHER]['time'];
            $complex->save();

            foreach ($data['services'] as $serviceData) {
                $serviceFinded = Services::find()->where(['carwash_id' => $this->carwashId, 'is_detailing' => false])->andWhere(['name' => $serviceData])->one();
                if ($serviceFinded) {
                    $complexService = new ComplexServices();
                    $complexService->complex_id = $complex->id;
                    $complexService->service_id = $serviceFinded->id;
                    $complexService->save();
                }
            }
        }

        foreach ($detailComplexs as $data) {
            $complex = new Complexes();
            $complex->carwash_id = $this->carwashId;
            $complex->is_detailing = true;
            $complex->name = $data['name'];

            $complex->type_1_price = $data['prices'][CarTypeHelper::TYPE_SEDAN]['price'];
            $complex->type_1_time = $data['prices'][CarTypeHelper::TYPE_SEDAN]['time'];
            $complex->type_2_price = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['price'];
            $complex->type_2_time = $data['prices'][CarTypeHelper::TYPE_CROSSOVERS]['time'];
            $complex->type_3_price = $data['prices'][CarTypeHelper::TYPE_SUV]['price'];
            $complex->type_3_time = $data['prices'][CarTypeHelper::TYPE_SUV]['time'];
            $complex->type_4_price = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['price'];
            $complex->type_4_time = $data['prices'][CarTypeHelper::TYPE_MINIBUSES]['time'];
            $complex->type_5_price = $data['prices'][CarTypeHelper::TYPE_OTHER]['price'];
            $complex->type_5_time = $data['prices'][CarTypeHelper::TYPE_OTHER]['time'];
            $complex->save();

            foreach ($data['services'] as $serviceData) {
                $serviceFinded = Services::find()->where(['carwash_id' => $this->carwashId, 'is_detailing' => true])->andWhere(['name' => $serviceData])->one();
                if ($serviceFinded) {
                    $complexService = new ComplexServices();
                    $complexService->complex_id = $complex->id;
                    $complexService->service_id = $serviceFinded->id;
                    $complexService->save();
                }
            }
        }
    }


    public function defaultOrders()
    {
        $orders = [
            ['post' => 1, 'startAt' => '10:00', 'endAt' => '10:30', 'client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => null, 'carMarkId' => null,
                'color' => '', 'number' => 'К423КУ', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '', 'wash' => [], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '15:00', 'endAt' => '15:13', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => 169, 'carMarkId' => 1966,
                'color' => 'белый', 'number' => 'Х000СТ', 'region' => '136', 'clientFullname' => 'Иванов Иван Иванович',
                'clientPhone' => '+7(888)888-88-88', 'wash' => ['Экспресс'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '11:00', 'endAt' => '12:00', 'client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'красный', 'number' => 'А015СТ', 'region' => '177', 'clientFullname' => 'Маринина Марина Александровна',
                'clientPhone' => '+7(888)888-88-89', 'wash' => [], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '09:00', 'endAt' => '09:50', 'client' => 'Алексеев', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => null, 'carMarkId' => null,
                'color' => '', 'number' => 'С780СС', 'region' => '32', 'clientFullname' => 'Белова Раиса Степановна',
                'clientPhone' => '+7(888)888-88-90', 'wash' => ['Эконом'], 'detailing' => ['Обработка замков спецсоставом'],
                'comment' => '', 'review' => 'Доброго времени суток,давно хотела оставить отзыв о автомойке "Автолига", работаю в центре города, соответственно искала автомойку по близости со своей работой и нашла))) Но дело конечно же не в расположении, хотя это тоже плюс, а в качестве как выполняемых услуг, так и в целом в отношении к клиенту.Всегда вежливый,внимательный и улыбчивый персонал, обращают внимание на мельчайшие детали, одним словом профессионалы!!!Выражаю огромную благодарность мастеру Александру и администратору Надежде.',
                'reviewAnswer' => 'Раиса, спасибо за высокую оценку! Заезжайте вновь.'
            ],
            ['post' => 3, 'startAt' => '09:55', 'endAt' => '10:10', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_OTHER, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'черный', 'number' => 'М884ЕЕ', 'region' => '', 'clientFullname' => 'Фурсова Екатерина Петровна',
                'clientPhone' => '', 'wash' => ['Мойка кузова напором'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '13:00', 'endAt' => '14:00', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => 84, 'carMarkId' => 943,
                'color' => 'серебристый', 'number' => 'О575РМ', 'region' => '77', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-88-81', 'wash' => ['Стандарт'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '15:00', 'endAt' => '16:10', 'client' => 'Алексеев', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => 172, 'carMarkId' => 2002,
                'color' => 'зеленый', 'number' => 'Е075ОО', 'region' => '799', 'clientFullname' => 'Басков Николай Петрович',
                'clientPhone' => '+7(888)888-88-82', 'wash' => ['Мойка кузова напором', 'Мойка ковров', 'Мойка запасного колеса'], 'detailing' => ['Чистка стекол салона изнутри'],
                'comment' => 'Очень спешу, просьба постараться побыстрее помыть!', 'review' => 'Сегодня мне поцарапали кожаный руль новой машины.. я в шоке.. даже не извинились! и проблему естественно не решили.. больше в эту автомойку ни ногой!! мойка находится у кафе Березовая роща',
                'reviewAnswer' => 'Приносим свои извинения за данную ситуацию. Позвольте в качестве компенсации предложить Вам 15% скидку на все услуги автомоечного комплекса на 2 посещения. '
            ],
            ['post' => 1, 'startAt' => '18:00', 'endAt' => '18:35', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => 196, 'carMarkId' => 2201,
                'color' => 'синий', 'number' => 'М975УУ', 'region' => '', 'clientFullname' => 'Сидоров Сидор Сидорович',
                'clientPhone' => '+7(888)888-88-83', 'wash' => [], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '17:15', 'endAt' => '17:30', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'красный', 'number' => 'Р888РР', 'region' => '045', 'clientFullname' => 'Фурсова Екатерина Петровна',
                'clientPhone' => '+7(888)888-88-84', 'wash' => [], 'detailing' => [],
                'comment' => 'Постоянный клиент', 'review' => 'Уже не первый год обслуживаюсь в данной компании и отдельно хотелось бы выделить Автомойку, мне с первого раза понравилось качество выполняемых услуг, работники отличные, разбираются в своем деле хорошо, к каждому имеют свой подход.А совсем недавно оставлял авто на химчистку, результатом остался очень доволен.',
                'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '12:00', 'endAt' => '13:00', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => 127, 'carMarkId' => 1339,
                'color' => 'белый', 'number' => 'М433ММ', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-88-85', 'wash' => ['Мойка кузова напором'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '18:00', 'endAt' => '19:40', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => 49, 'carMarkId' => 465,
                'color' => 'желтый', 'number' => 'Е055ОA', 'region' => '763', 'clientFullname' => 'Маркин Михаил Альбертович',
                'clientPhone' => '+7(888)888-88-86', 'wash' => ['Мойка кузова с шампунем', 'Очистка стекол от насекомых'], 'detailing' => ['Нанесение защитного покрытия "антидождь"', 'Чистка стекол салона изнутри'],
                'comment' => '', 'review' => 'Обслуживаюсь в Автолиге не первый год.В прошлом году узнал о детейлинг центре на Московской,очень заинтересовался,оставил авто на полный комплекс и обработку керамик про,не ожидал такого эффекта,автомобиль стал как новый.В этом году решил повторить,ребята держут свой стасус,молодцы,спасибо вам,буду советовать вас друзьям.',
                'reviewAnswer' => 'Спасибо за добрые слова, Ваше мнение важно для нас.'
            ],
            ['post' => 1, 'startAt' => '11:00', 'endAt' => '14:30', 'client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => 244, 'carMarkId' => 2697,
                'color' => 'белый', 'number' => 'В112АА', 'region' => '', 'clientFullname' => 'Петров Петр Петрович',
                'clientPhone' => '+7(888)888-88-85', 'wash' => ['Мойка кузова напором', 'Влажная уборка салона'], 'detailing' => ['Химчистка потолка (с сушкой)'],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '14:40', 'endAt' => '14:50', 'dateChanger' => '-1', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_OTHER, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'белый', 'number' => 'К777СР', 'region' => '048', 'clientFullname' => 'Афанасьева Галина Ивановна',
                'clientPhone' => '', 'wash' => ['Мойка ковров'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '10:15', 'endAt' => '19:30', 'dateChanger' => '-1','client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => 29, 'carMarkId' => 260,
                'color' => 'черный', 'number' => 'О000ОО', 'region' => '777', 'clientFullname' => 'Кораблин Марк Федорович',
                'clientPhone' => '+7(888)889-88-88', 'wash' => ['Люкс'], 'detailing' => ['Химчистка полная'],
                'comment' => 'Требовательный клиент!', 'review' => 'Спасибо за прекрасную работу. Буду обращаться еще.',
                'reviewAnswer' => 'Благодарим за Ваш отзыв, Ваше мнение важно для нас.
Будем рады видеть Вас снова.'
            ],
            ['post' => 1, 'startAt' => '10:40', 'endAt' => '12:00','dateChanger' => '-1', 'client' => 'Алексеев', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'серебристый', 'number' => 'Н234РО', 'region' => '', 'clientFullname' => 'Андреева Тамара Петровна',
                'clientPhone' => '', 'wash' => [], 'detailing' => [],
                'comment' => 'Прошу назначить мне мойщика Дмитрия.', 'review' => 'Все нравится! Сервис отменный. Пожелания: работа зала ожидания - 24 часа!!! Спасибо', 'reviewAnswer' => 'Благодарим за Ваш отзыв, Ваше мнение важно для нас.
Будем рады видеть Вас снова.'
            ],
            ['post' => 2, 'startAt' => '18:00', 'endAt' => '18:50', 'dateChanger' => '-1', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'белый', 'number' => 'О99ОО', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '', 'wash' => ['Стандарт'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '13:00', 'endAt' => '13:10', 'client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'черный', 'number' => 'У314ОО', 'region' => '', 'clientFullname' => 'Сидоров Артем',
                'clientPhone' => '+7(888)888-78-81', 'wash' => ['Мойка днища'], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '13:20', 'endAt' => '14:00', 'client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => 178, 'carMarkId' => 2088,
                'color' => 'черный', 'number' => 'А124АА', 'region' => '', 'clientFullname' => 'Алексей',
                'clientPhone' => '+7(888)888-78-82', 'wash' => ['Мойка двигателя', 'Мойка запасного колеса'], 'detailing' => ['Удаление битумных, масляных пятен (1 деталь)'],
                'comment' => '', 'review' => 'НЕ ПОЛЬЗУЙТЕСЬ автомойкой Берёзовая роща (Щербинки). Хотел помыть машину, так они умудрились поцарапать мой автомобиль. А самое главное пошли в отказ. «Это не мы, это так и было». Пока разбирался выяснил, что днем раньше, здесь же, у другого клиента сломали люк бензобака. НЕ ПОЛЬЗУЙТЕСЬ автомойкой.',
                'reviewAnswer' => 'Алексей, приносим извинения за данный инцидент. Свяжитесь, пожалуйста, с нами для разбора данной ситуации.'
            ],
            ['post' => 2, 'startAt' => '07:00', 'endAt' => '07:40', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_OTHER, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'белый', 'number' => 'У535АА', 'region' => '', 'clientFullname' => 'Иванов Евгений Александрович',
                'clientPhone' => '+7(888)888-78-83', 'wash' => [], 'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '15:30', 'endAt' => '18:00', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => 84, 'carMarkId' => 883,
                'color' => 'желтый', 'number' => 'В511ОУ', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-78-84', 'wash' => ['Мойка кузова с шампунем', 'Очистка стекол от насекомых', 'Очистка пластиковых элементов салона'],
                'detailing' => ['Нанесение защитного покрытия "антидождь"', 'Чистка стекол салона изнутри'],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '13:20', 'endAt' => '16:00', 'dateChanger' => '-2','client' => 'Маринина', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_MINIBUSES, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'белый', 'number' => 'В312ВВ', 'region' => '', 'clientFullname' => 'Сидоренко Евгений Максимович',
                'clientPhone' => '+7(888)888-78-85', 'wash' => [], 'detailing' => [],
                'comment' => 'Добрый день. Прошу помыть пороги, стекла изнутри. Срециально прописываю в комментарии, чтобы не упустить этот момент.', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '09:00', 'endAt' => '09:45', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => 127, 'carMarkId' => 1348,
                'color' => 'белый', 'number' => 'Р621ОУ', 'region' => '', 'clientFullname' => 'Зюзина Марина Николаевна',
                'clientPhone' => '+7(888)888-78-86', 'wash' => [],
                'detailing' => [],
                'comment' => '', 'review' => '10/10', 'reviewAnswer' => 'Спасибо за высокую оценку! Заезжайте вновь.'
            ],
            ['post' => 2, 'startAt' => '14:20', 'endAt' => '14:50', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_CROSSOVERS, 'carBrandId' => 196, 'carMarkId' => 2207,
                'color' => 'желтый', 'number' => 'О842УУ', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-78-84', 'wash' => [],
                'detailing' => ['Очистка кожи салона'],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 2, 'startAt' => '08:15', 'endAt' => '17:30', 'dateChanger' => '+1', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => 153, 'carMarkId' => 1575,
                'color' => 'красный', 'number' => 'А050АА', 'region' => '777', 'clientFullname' => 'Вероника',
                'clientPhone' => '+7(888)888-78-86', 'wash' => ['Люкс'],
                'detailing' => ['Химчистка полная'],
                'comment' => 'Сильное загрязнение!', 'review' => 'Работу выполнили оперативно и качественно. Подробно объяснили технологию и требования к выполняемым работам. Результатом очень доволен. Спасибо!', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '16:00', 'endAt' => '16:15', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => 275, 'carMarkId' => 2888,
                'color' => 'синий', 'number' => 'У998УУ', 'region' => '', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-78-84', 'wash' => ['Экспресс'],
                'detailing' => [],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 3, 'startAt' => '08:00', 'endAt' => '10:20', 'dateChanger' => '-1', 'client' => 'Алексеев', 'personal' => '',
                'carType' => CarTypeHelper::TYPE_SEDAN, 'carBrandId' => 15, 'carMarkId' => 123,
                'color' => 'синий', 'number' => 'О111ОР', 'region' => '', 'clientFullname' => 'Ягодкина Елена',
                'clientPhone' => '+7(888)888-78-86', 'wash' => [],
                'detailing' => [],
                'comment' => '', 'review' => 'Нормальная мойка) Если не придираться к мелочам то все супер!', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '18:40', 'endAt' => '20:50', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_OTHER, 'carBrandId' => null, 'carMarkId' => null,
                'color' => 'зеленый', 'number' => 'А233УУ', 'region' => '32', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-78-84', 'wash' => ['Мойка кузова с шампунем', 'Очистка стекол от насекомых', 'Очистка пластиковых элементов салона'],
                'detailing' => ['Нанесение защитного покрытия "антидождь"', 'Чистка стекол салона изнутри'],
                'comment' => '', 'review' => '', 'reviewAnswer' => ''
            ],
            ['post' => 1, 'startAt' => '10:15', 'endAt' => '19:30', 'dateChanger' => '+1', 'client' => '', 'personal' => 'Алексей',
                'carType' => CarTypeHelper::TYPE_SUV, 'carBrandId' => 49, 'carMarkId' => 487,
                'color' => '', 'number' => 'А555ТК', 'region' => '31', 'clientFullname' => '',
                'clientPhone' => '+7(888)888-78-86', 'wash' => [],
                'detailing' => [],
                'comment' => 'Постоянный клиент', 'review' => 'Постоянно моюсь только тут, здесь и расценки приемлемые, да и отношение персонала приятное, не грубят, не хамят, относятся максимально уважительно. Рекомендовать таким образом эту автомойку могу смело, как мне кажется вполне заслуженно. Я тут недавно химчистку делал, так тоже сделали все качественно, чем доказали свой профессионализм, смело 5 звезд из 5.',
                'reviewAnswer' => 'Спасибо за регулярный уход и заезды к нам.'
            ]
        ];

        foreach ($orders as $orderData) {
            $order = new Orders();
            $order->carwash_id = $this->carwashId;
            if (!empty($orderData['client'])) {
                $order->status = Orders::STATUS_NEW_FROM_CLIENT;
                $client = Clients::find()->where(['carwash_id' => $this->carwashId])->andWhere(['like', 'full_name',
                    $orderData['client']])->one();
                $order->client_id = $client->id;
            } else {
                $order->status = Orders::STATUS_NEW_FROM_WASH;
                $user = Users::find()->where(['guid' => 'testedUser_1_ForRegister_' . $this->carwashId])->one();
                $personal = Personal::find()->where(['carwash_id' => $this->carwashId])->andWhere(['user_id' => $user->id])->one();
                $order->personal_id = $personal->id;
                $order->personal_fullname = ($user->lastname ?? '') . ' ' . ($user->firstname ?? '') . ' ' . ($user->patronymic ?? '');
            }
            if (!isset($orderData['dateChanger'])) {
                $orderData['dateChanger'] = 0;
            }
            $order->date = date('Y-m-d', strtotime($orderData['dateChanger'] . ' days'));
            $order->post = $orderData['post'];
            $order->start_time = TimeHelper::convertTimeToMin($orderData['startAt']);
            $order->end_time = TimeHelper::convertTimeToMin($orderData['endAt']);
            $order->car_type = $orderData['carType'];
            $order->car_number = $orderData['number'];
            $order->car_region = $orderData['region'];
            $order->color = $orderData['color'];

            $order->car_brand_id = $orderData['carBrandId'];
            $order->car_model_id = $orderData['carMarkId'];
            $order->client_fullname = $orderData['clientFullname'];
            $order->client_phone = $orderData['clientPhone'];
            $order->total_price = 1000;
            $order->admin_comment = $orderData['comment'] ?? '';
            $order->save();
        }
    }

    public function defaultClients()
    {
        $client = new Clients();
        $client->guid = 'testClient_1_For_' . $this->carwashId;
        $client->carwash_id = $this->carwashId;
        $client->is_subscribed = true;
        $client->full_name = 'Алексеев Юрий Михайлович';
        $client->reputation = 10;
        $client->save();

        $client = new Clients();
        $client->guid = 'testClient_2_For_' . $this->carwashId;
        $client->carwash_id = $this->carwashId;
        $client->is_subscribed = false;
        $client->full_name = 'Маринина Марина Александровна';
        $client->reputation = -1;
        $client->save();
    }

    public function defaultPersonal()
    {
        $user = new Users();
        $user->setPassword(rand(111111, 999999));
        $user->status = Users::STATUS_ACTIVE;
        $user->guid = "testedUser_1_ForRegister_" . $this->carwashId;
        $user->phone_verified = true;
        $user->firstname = 'Тестовый';
        $user->lastname = 'Алексей';
        $user->patronymic = 'Мойщикович';
        $user->auth_token = "testedUser_1_ForRegister_" . $this->carwashId;
        $user->save();

        $personal = new Personal();
        $personal->user_id = $user->id;
        $personal->carwash_id = $this->carwashId;
        $personal->is_approved = Personal::IS_APPROVED;
        $personal->post = Personal::POST_WASHER;
        $personal->salary_type = Personal::SALARY_TYPE_NONE;
        $personal->save();
        $authManager = \Yii::$app->authManager;
        \Yii::$app->authManager->assign($authManager->getRole('washer'), $personal->id);
    }

}