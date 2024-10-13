<?php

use Tests\TestCase;
use App\Mappers\RaceScheduleMapper;

class RaceScheduleMapperTest extends TestCase
{
    private const EXAMPLE_URL = 'http://example.com';
    private const CIRCUIT_URL = 'http://example.com/circuit';
    private const RACENAME = 'Australian Grand Prix';
    private const CIRCUIT_NAME = 'Albert Park Grand Prix Circuit';
    private const LATITUDE = '-37.8497';
    private const LONGITUDE = '144.968';
    private const DATE = '2023-03-19';
    private const TIME = '06:00:00Z';

    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new RaceScheduleMapper();
    }

    public function testMapWithValidData()
    {
        $data = [
            'MRData' => [
                'RaceTable' => [
                    'season' => '2023',
                    'Races' => [
                        [
                            'season' => '2023',
                            'round' => '1',
                            'url' => self::EXAMPLE_URL,
                            'raceName' => self::RACENAME,
                            'Circuit' => [
                                'circuitId' => 'albert_park',
                                'url' => self::CIRCUIT_URL,
                                'circuitName' => self::CIRCUIT_NAME,
                                'Location' => [
                                    'lat' => self::LATITUDE,
                                    'long' => self::LONGITUDE,
                                    'locality' => 'Melbourne',
                                    'country' => 'Australia'
                                ]
                            ],
                            'date' => self::DATE,
                            'time' => self::TIME,
                            'FirstPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'SecondPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'ThirdPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'Qualifying' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ]
                        ]
                    ]
                ],
                'total' => '1',
                'limit' => '30',
                'offset' => '0'
            ]
        ];

        $expected = [
            'ApiColapinto' => [
                'series' => 'f1',
                'limit' => '30',
                'offset' => '0',
                'total' => '1',
                'RaceTable' => [
                    'season' => '2023',
                    'Races' => [
                        [
                            'season' => '2023',
                            'round' => '1',
                            'url' => self::EXAMPLE_URL,
                            'raceName' => self::RACENAME,
                            'Circuit' => [
                                'circuitId' => 'albert_park',
                                'url' => self::CIRCUIT_URL,
                                'circuitName' => self::CIRCUIT_NAME,
                                'Location' => [
                                    'lat' => self::LATITUDE,
                                    'long' => self::LONGITUDE,
                                    'locality' => 'Melbourne',
                                    'country' => 'Australia'
                                ]
                            ],
                            'date' => '2023-03-19',
                            'time' => '06:00:00Z',
                            'FirstPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'SecondPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'ThirdPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'Qualifying' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->mapper->map($data);
        $this->assertEquals($expected, $result);
    }

    public function testMapWithNoRaces()
    {
        $data = [
            'MRData' => [
                'RaceTable' => []
            ]
        ];

        $expected = ['error' => 'No se encontraron carreras en los datos proporcionados.'];

        $result = $this->mapper->map($data);
        $this->assertEquals($expected, $result);
    }

    public function testMapWithNoSeasonYear()
    {
        $data = [
            'MRData' => [
                'RaceTable' => [
                    'Races' => [
                        [
                            'season' => '2023',
                            'round' => '1',
                            'url' => self::EXAMPLE_URL,
                            'raceName' => self::RACENAME,
                            'Circuit' => [
                                'circuitId' => 'albert_park',
                                'url' => self::CIRCUIT_URL,
                                'circuitName' => self::CIRCUIT_NAME,
                                'Location' => [
                                    'lat' => self::LATITUDE,
                                    'long' => self::LONGITUDE,
                                    'locality' => 'Melbourne',
                                    'country' => 'Australia'
                                ]
                            ],
                            'date' => self::DATE,
                            'time' => self::TIME,
                            'FirstPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'SecondPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'ThirdPractice' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ],
                            'Qualifying' => [
                                'date' => self::DATE,
                                'time' => self::TIME
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expected = ['error' => 'El año de la temporada no está definido.'];

        $result = $this->mapper->map($data);
        $this->assertEquals($expected, $result);
    }

    public function testMapWithInvalidRace()
    {
        $data = [
            'MRData' => [
                'RaceTable' => [
                    'season' => '2023',
                    'Races' => [
                        [
                            'season' => '2023',
                            'round' => '1',
                            'url' => self::EXAMPLE_URL,
                            'raceName' => self::RACENAME,
                            'Circuit' => [] // Circuito inválido
                        ]
                    ]
                ],
                'total' => '1',
                'limit' => '30',
                'offset' => '0'
            ]
        ];

        $expected = [
            'ApiColapinto' => [
                'series' => 'f1',
                'limit' => '30',
                'offset' => '0',
                'total' => '1',
                'RaceTable' => [
                    'season' => '2023',
                    'Races' => [] // No debería haber carreras válidas
                ]
            ]
        ];

        $result = $this->mapper->map($data);
        $this->assertEquals($expected, $result);
    }
}
