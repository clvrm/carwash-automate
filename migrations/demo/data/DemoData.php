<?php

namespace app\migrations\demo\data;

/**
 * Фиксированные данные для демо-автомойки (без Faker).
 */
class DemoData
{
    public const CARWASH_NAME = 'ЧистоИТочка';
    public const CARWASH_ADDRESS = 'ул. МойДоДыра, 4';
    public const CITY_NAME = 'Москва';
    public const PASSWORD = 'stepcar2025';

    public const STAFF = [
        [
            'firstname' => 'Иван',
            'lastname' => 'Петров',
            'patronymic' => 'Сергеевич',
            'email' => 'petrov@chistoitochka.local',
            'post' => 10,
            'role' => 'owner',
            'permissions' => [],
            'salary' => 80000,
            'salary_type' => 1,
        ],
        [
            'firstname' => 'Анна',
            'lastname' => 'Смирнова',
            'patronymic' => 'Викторовна',
            'email' => 'smirnova@chistoitochka.local',
            'post' => 20,
            'role' => 'manager',
            'permissions' => [
                'perm_create_edit_orders',
                'perm_close_orders',
                'perm_view_analytics',
                'perm_create_edit_sales',
                'perm_change_record_setting',
            ],
            'salary' => 55000,
            'salary_type' => 1,
        ],
        [
            'firstname' => 'Дмитрий',
            'lastname' => 'Козлов',
            'patronymic' => 'Алексеевич',
            'email' => 'kozlov@chistoitochka.local',
            'post' => 30,
            'role' => 'admin',
            'permissions' => [
                'perm_create_edit_orders',
                'perm_close_orders',
            ],
            'salary' => 45000,
            'salary_type' => 1,
        ],
        [
            'firstname' => 'Павел',
            'lastname' => 'Волков',
            'patronymic' => 'Игоревич',
            'email' => 'volkov@chistoitochka.local',
            'post' => 40,
            'role' => 'washer',
            'permissions' => [],
            'salary' => 35000,
            'salary_type' => 1,
        ],
        [
            'firstname' => 'Мария',
            'lastname' => 'Орлова',
            'patronymic' => 'Петровна',
            'email' => 'orlova@chistoitochka.local',
            'post' => 40,
            'role' => 'washer',
            'permissions' => [],
            'salary' => 32000,
            'salary_type' => 1,
        ],
        [
            'firstname' => 'Артём',
            'lastname' => 'Никитин',
            'patronymic' => 'Олегович',
            'email' => 'nikitin@chistoitochka.local',
            'post' => 40,
            'role' => 'washer',
            'permissions' => [],
            'salary' => 33000,
            'salary_type' => 1,
        ],
    ];

    public const CAR_BRANDS = [
        'Toyota' => ['Camry', 'Corolla', 'RAV4'],
        'LADA (ВАЗ)' => ['Granta', 'Vesta', 'Niva'],
        'Kia' => ['Rio', 'Sportage'],
        'Hyundai' => ['Solaris', 'Creta'],
        'BMW' => ['3 серии', 'X5'],
        'Mercedes-Benz' => ['C-класс', 'GLC'],
        'Renault' => ['Logan', 'Duster'],
        'Volkswagen' => ['Polo', 'Tiguan'],
        'Skoda' => ['Octavia', 'Rapid'],
        'Ford' => ['Focus', 'Kuga'],
        'Nissan' => ['Qashqai', 'Almera'],
        'Chery' => ['Tiggo 7', 'Tiggo 4'],
    ];

    public const CLIENTS = [
        ['full_name' => 'Алексеев Константин Владимирович', 'phone' => '+7 (916) 123-45-67', 'email' => 'k.alekseev@mail.ru', 'subscribed' => true],
        ['full_name' => 'Борисова Елена Игоревна', 'phone' => '+7 (903) 234-56-78', 'email' => 'borisova.e@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Власов Михаил Андреевич', 'phone' => '8 (925) 345-67-89', 'email' => null, 'subscribed' => false],
        ['full_name' => 'Громова Ольга Сергеевна', 'phone' => '+7 (926) 456-78-90', 'email' => 'gromova@inbox.ru', 'subscribed' => true],
        ['full_name' => 'Данилов Роман Петрович', 'phone' => '+7 (917) 567-89-01', 'email' => 'danilov.r@gmail.com', 'subscribed' => false],
        ['full_name' => 'Егорова Наталья Дмитриевна', 'phone' => '+7 (915) 678-90-12', 'email' => 'egorova.n@mail.ru', 'subscribed' => true],
        ['full_name' => 'Жуков Игорь Николаевич', 'phone' => '8 (903) 789-01-23', 'email' => null, 'subscribed' => false],
        ['full_name' => 'Зайцева Татьяна Викторовна', 'phone' => '+7 (916) 890-12-34', 'email' => 'zaitseva@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Иванов Сергей Александрович', 'phone' => '+7 (926) 901-23-45', 'email' => 'ivanov.sa@gmail.com', 'subscribed' => true],
        ['full_name' => 'Киселёв Артур Олегович', 'phone' => '+7 (903) 012-34-56', 'email' => 'kiselev@mail.ru', 'subscribed' => false],
        ['full_name' => 'Лебедева Марина Павловна', 'phone' => '+7 (917) 123-45-78', 'email' => 'lebedeva@inbox.ru', 'subscribed' => true],
        ['full_name' => 'Морозов Виктор Степанович', 'phone' => '8 (925) 234-56-89', 'email' => 'morozov.v@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Новикова Ксения Алексеевна', 'phone' => '+7 (915) 345-67-90', 'email' => 'novikova.k@gmail.com', 'subscribed' => false],
        ['full_name' => 'Осипов Григорий Иванович', 'phone' => '+7 (916) 456-78-01', 'email' => null, 'subscribed' => true],
        ['full_name' => 'Павлова Светлана Юрьевна', 'phone' => '+7 (903) 567-89-12', 'email' => 'pavlova@mail.ru', 'subscribed' => false],
        ['full_name' => 'Романов Денис Евгеньевич', 'phone' => '+7 (926) 678-90-23', 'email' => 'romanov.d@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Соколова Вера Михайловна', 'phone' => '+7 (917) 789-01-34', 'email' => 'sokolova@inbox.ru', 'subscribed' => true],
        ['full_name' => 'Титов Павел Романович', 'phone' => '8 (925) 890-12-45', 'email' => 'titov@mail.ru', 'subscribed' => false],
        ['full_name' => 'Ушакова Людмила Фёдоровна', 'phone' => '+7 (915) 901-23-56', 'email' => 'ushakova@gmail.com', 'subscribed' => false],
        ['full_name' => 'Фёдоров Андрей Геннадьевич', 'phone' => '+7 (916) 012-34-67', 'email' => 'fedorov@yandex.ru', 'subscribed' => true],
        ['full_name' => 'Харитонов Олег Владиславович', 'phone' => '+7 (903) 123-45-89', 'email' => 'haritonov@mail.ru', 'subscribed' => false],
        ['full_name' => 'Цветкова Ирина Борисовна', 'phone' => '+7 (926) 234-56-90', 'email' => 'cvetkova@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Чернов Максим Анатольевич', 'phone' => '8 (917) 345-67-01', 'email' => 'chernov@gmail.com', 'subscribed' => true],
        ['full_name' => 'Шарова Юлия Константиновна', 'phone' => '+7 (915) 456-78-12', 'email' => 'sharova@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Щербаков Никита Павлович', 'phone' => '+7 (916) 567-89-23', 'email' => 'scherbakov@mail.ru', 'subscribed' => false],
        ['full_name' => 'Яковлев Владимир Олегович', 'phone' => '+7 (903) 678-90-34', 'email' => null, 'subscribed' => false],
        ['full_name' => 'Абрамов Станислав Игоревич', 'phone' => '+7 (925) 789-01-45', 'email' => 'abramov@inbox.ru', 'subscribed' => true],
        ['full_name' => 'Белова Алина Сергеевна', 'phone' => '+7 (917) 890-12-56', 'email' => 'belova@gmail.com', 'subscribed' => false],
        ['full_name' => 'Воронов Евгений Дмитриевич', 'phone' => '8 (915) 901-23-67', 'email' => 'voronov@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Галкина Полина Андреевна', 'phone' => '+7 (916) 012-34-78', 'email' => 'galkina@mail.ru', 'subscribed' => true],
        ['full_name' => 'Дорофеев Кирилл Викторович', 'phone' => '+7 (903) 123-45-90', 'email' => 'dorofeev@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Ефимова Валентина Григорьевна', 'phone' => '+7 (926) 234-56-01', 'email' => 'efimova@gmail.com', 'subscribed' => false],
        ['full_name' => 'Жданов Тимур Рашидович', 'phone' => '+7 (917) 345-67-12', 'email' => 'zhdanov@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Зуева Карина Олеговна', 'phone' => '8 (925) 456-78-23', 'email' => 'zueva@mail.ru', 'subscribed' => true],
        ['full_name' => 'Ильин Арсений Петрович', 'phone' => '+7 (915) 567-89-34', 'email' => 'ilin@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Калинина Екатерина Михайловна', 'phone' => '+7 (916) 678-90-45', 'email' => 'kalinina@gmail.com', 'subscribed' => false],
        ['full_name' => 'Лазарев Борис Семёнович', 'phone' => '+7 (903) 789-01-56', 'email' => 'lazarev@yandex.ru', 'subscribed' => true],
        ['full_name' => 'Медведев Илья Алексеевич', 'phone' => '+7 (926) 890-12-67', 'email' => 'medvedev@mail.ru', 'subscribed' => false],
        ['full_name' => 'Назарова Дарья Ивановна', 'phone' => '8 (917) 901-23-78', 'email' => null, 'subscribed' => false],
        ['full_name' => 'Одинцов Фёдор Николаевич', 'phone' => '+7 (915) 012-34-89', 'email' => 'odincov@inbox.ru', 'subscribed' => true],
        ['full_name' => 'Панфилов Георгий Владимирович', 'phone' => '+7 (916) 123-45-90', 'email' => 'panfilov@gmail.com', 'subscribed' => false],
        ['full_name' => 'Рыбакова Анжела Степановна', 'phone' => '+7 (903) 234-56-01', 'email' => 'rybakova@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Семёнов Вячеслав Юрьевич', 'phone' => '+7 (925) 345-67-12', 'email' => 'semenov@mail.ru', 'subscribed' => true],
        ['full_name' => 'Тарасова Лариса Борисовна', 'phone' => '+7 (917) 456-78-23', 'email' => 'tarasova@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Устинов Руслан Маратович', 'phone' => '8 (915) 567-89-34', 'email' => 'ustinov@gmail.com', 'subscribed' => false],
        ['full_name' => 'Фомина Анастасия Денисовна', 'phone' => '+7 (916) 678-90-45', 'email' => 'fomina@yandex.ru', 'subscribed' => true],
        ['full_name' => 'Хохлов Степан Аркадьевич', 'phone' => '+7 (903) 789-01-56', 'email' => 'hohlov@mail.ru', 'subscribed' => false],
        ['full_name' => 'Черкасов Леонид Геннадьевич', 'phone' => '+7 (926) 890-12-67', 'email' => 'cherkasov@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Шубин Валерий Павлович', 'phone' => '+7 (917) 901-23-78', 'email' => 'shubin@gmail.com', 'subscribed' => true],
        ['full_name' => 'Юдин Александр Сергеевич', 'phone' => '8 (925) 012-34-89', 'email' => 'yudin@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Якушева Нина Владимировна', 'phone' => '+7 (915) 123-45-90', 'email' => 'yakusheva@mail.ru', 'subscribed' => false],
        ['full_name' => 'Крылов Даниил Олегович', 'phone' => '+7 (916) 234-56-01', 'email' => 'krylov@inbox.ru', 'subscribed' => false],
        ['full_name' => 'Мельникова Оксана Игоревна', 'phone' => '+7 (903) 345-67-12', 'email' => 'melnikova@gmail.com', 'subscribed' => true],
        ['full_name' => 'Савельев Тарас Викторович', 'phone' => '+7 (926) 456-78-23', 'email' => 'savelyev@yandex.ru', 'subscribed' => false],
        ['full_name' => 'Гусев Пётр Андреевич', 'phone' => '8 (917) 567-89-34', 'email' => null, 'subscribed' => false],
    ];

    public const CAR_COLORS = ['белый', 'чёрный', 'серебристый', 'серый', 'синий', 'красный', 'зелёный', 'бежевый'];

    public const CAR_REGIONS = [77, 97, 99, 50];

    public const CAR_LETTERS = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'];

    /** @var array<int, array{name: string, price: int}> */
    public const WASH_MATERIALS = [
        ['name' => 'Автошампунь pH-neutral', 'price' => 80],
        ['name' => 'Пена активная', 'price' => 90],
        ['name' => 'Очиститель дисков', 'price' => 60],
        ['name' => 'Полироль для пластика салона', 'price' => 45],
        ['name' => 'Стеклоочиститель', 'price' => 35],
        ['name' => 'Воск для кузова', 'price' => 120],
    ];

    /** @var array<int, array{name: string, price: int}> */
    public const DETAILING_MATERIALS = [
        ['name' => 'Состав для химчистки салона', 'price' => 250],
        ['name' => 'Очиститель кожи', 'price' => 180],
        ['name' => 'Антидождь', 'price' => 350],
        ['name' => 'Силикон для уплотнителей', 'price' => 95],
        ['name' => 'Чернитель шин', 'price' => 70],
    ];

    /**
     * Привязка материалов к услугам: название услуги => [['material' => ..., 'price' => ...], ...]
     *
     * @var array<string, array<int, array{material: string, price: int}>>
     */
    public const SERVICE_MATERIAL_LINKS = [
        'Мойка кузова с шампунем' => [
            ['material' => 'Автошампунь pH-neutral', 'price' => 80],
            ['material' => 'Пена активная', 'price' => 50],
        ],
        'Мойка колесных дисков изнутри' => [
            ['material' => 'Очиститель дисков', 'price' => 60],
        ],
        'Очистка стекол' => [
            ['material' => 'Стеклоочиститель', 'price' => 35],
        ],
        'Очистка стекол от насекомых' => [
            ['material' => 'Стеклоочиститель', 'price' => 35],
        ],
        'Очистка пластиковых элементов салона' => [
            ['material' => 'Полироль для пластика салона', 'price' => 45],
        ],
        'Влажная уборка салона' => [
            ['material' => 'Полироль для пластика салона', 'price' => 45],
        ],
        'Нанесение защитного покрытия "антидождь"' => [
            ['material' => 'Антидождь', 'price' => 350],
        ],
        'Чернение шин' => [
            ['material' => 'Чернитель шин', 'price' => 70],
        ],
        'Обработка уплотнителей дверей силиконом' => [
            ['material' => 'Силикон для уплотнителей', 'price' => 95],
        ],
        'Очистка кожи салона' => [
            ['material' => 'Очиститель кожи', 'price' => 180],
        ],
        'Химчистка сидений с сушкой (1 шт.)' => [
            ['material' => 'Состав для химчистки салона', 'price' => 200],
        ],
    ];

    /**
     * Привязка материалов к комплексам: название комплекса => [['material' => ..., 'price' => ...], ...]
     *
     * @var array<string, array<int, array{material: string, price: int}>>
     */
    public const COMPLEX_MATERIAL_LINKS = [
        'Эконом' => [
            ['material' => 'Автошампунь pH-neutral', 'price' => 80],
            ['material' => 'Пена активная', 'price' => 50],
        ],
        'Стандарт' => [
            ['material' => 'Автошампунь pH-neutral', 'price' => 80],
            ['material' => 'Пена активная', 'price' => 50],
            ['material' => 'Полироль для пластика салона', 'price' => 45],
        ],
        'Люкс' => [
            ['material' => 'Автошампунь pH-neutral', 'price' => 80],
            ['material' => 'Пена активная', 'price' => 50],
            ['material' => 'Полироль для пластика салона', 'price' => 45],
            ['material' => 'Воск для кузова', 'price' => 120],
        ],
        'Химчистка полная' => [
            ['material' => 'Состав для химчистки салона', 'price' => 400],
            ['material' => 'Очиститель кожи', 'price' => 180],
            ['material' => 'Стеклоочиститель', 'price' => 35],
        ],
    ];

    public const ORDER_COMMENTS = [
        'Постараться не намочить салон',
        'Грязные колёса, нужна отдельная мойка дисков',
        'Клиент просил позвонить за 10 минут',
        'Пятно на капоте — аккуратно',
        '',
        'Записался через сайт',
        'Постоянный клиент, знает расклад',
        'Оплата картой',
    ];

    public const CHAT_DIALOGS = [
        [
            'client_index' => 0,
            'messages' => [
                ['from' => 'client', 'text' => 'Здравствуйте, можно перенести запись на завтра?'],
                ['from' => 'staff', 'text' => 'Добрый день! Да, на какое время удобно?'],
                ['from' => 'client', 'text' => 'На 14:00, если свободно'],
                ['from' => 'staff', 'text' => 'Записала, ждём вас'],
            ],
        ],
        [
            'client_index' => 5,
            'messages' => [
                ['from' => 'client', 'text' => 'Сколько по времени займёт комплекс Стандарт?'],
                ['from' => 'staff', 'text' => 'Примерно 45–50 минут для седана'],
                ['from' => 'client', 'text' => 'Спасибо'],
            ],
        ],
        [
            'client_index' => 12,
            'messages' => [
                ['from' => 'client', 'text' => 'У вас сегодня работаете?'],
                ['from' => 'staff', 'text' => 'Да, до 22:00'],
            ],
        ],
    ];
}
