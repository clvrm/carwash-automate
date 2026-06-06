<?php


namespace app\commons\helpers;

/**
 * Class CarTypeHelper
 * @package app\commons\helpers
 */
class CarTypeHelper
{
    public const TYPE_SEDAN = 1;
    public const TYPE_CROSSOVERS = 2;
    public const TYPE_SUV = 3;
    public const TYPE_MINIBUSES = 4;
    public const TYPE_OTHER = 5;

    /**
     * @param $type
     * @return string
     */
    public static function getIcon($type): string
    {
        $types = [
            1 => '/media/service/car-type/car1.png',
            2 => '/media/service/car-type/car2.png',
            3 => '/media/service/car-type/car3.png',
            4 => '/media/service/car-type/car4.png',
            5 => '/media/service/car-type/car5.png',
        ];

        return $types[$type] ?? '';
    }

    /**
     * Название типа ТС по id
     * @param $id
     * @return string
     */
    public static function getLabelType($id): string
    {
        $labels = [
            1 => 'Седан',
            2 => 'Кроссовер',
            3 => 'Внедорожник',
            4 => 'Микроавтобус',
            5 => 'Иное',
        ];

        return $labels[$id] ?? '';
    }

    /**
     * @return array
     */
    public static function getMap(): array
    {
        $types = [
            self::TYPE_SEDAN => self::getLabelType(self::TYPE_SEDAN),
            self::TYPE_CROSSOVERS => self::getLabelType(self::TYPE_CROSSOVERS),
            self::TYPE_SUV => self::getLabelType(self::TYPE_SUV),
            self::TYPE_MINIBUSES => self::getLabelType(self::TYPE_MINIBUSES),
            self::TYPE_OTHER => self::getLabelType(self::TYPE_OTHER),

        ];

        return $types;
    }


}