<?php

use Rogervila\ArrayDiffMultidimensional;

/**
 * PHPBench Benchmark Suite for ArrayDiffMultidimensional
 *
 * Run with:
 * ./vendor/bin/phpbench run tests/Benchmark --report=default
 * ./vendor/bin/phpbench run tests/Benchmark --report=aggregate
 * ./vendor/bin/phpbench run tests/Benchmark --iterations=5 --revs=1000 --report=default
 */
class ArrayCompareBenchmark
{
    private $smallArray1;
    private $smallArray2;
    private $mediumArray1;
    private $mediumArray2;
    private $largeArray1;
    private $largeArray2;
    private $deeplyNestedArray1;
    private $deeplyNestedArray2;
    private $wideArray1;
    private $wideArray2;

    /**
     * Setup method to prepare test data
     */
    public function setUp(): void
    {
        // Small arrays (simple structure)
        $this->smallArray1 = [
            'name' => 'John',
            'age' => 30,
            'active' => true,
        ];

        $this->smallArray2 = [
            'name' => 'John',
            'age' => 31,
            'active' => false,
        ];

        // Medium arrays (realistic user profile)
        $this->mediumArray1 = [
            'id' => 123,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30,
            'address' => [
                'street' => '123 Main St',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'USA',
            ],
            'hobbies' => ['reading', 'traveling', 'swimming'],
            'metadata' => [
                'created_at' => '2023-01-01',
                'updated_at' => '2023-06-15',
                'version' => 2,
                'flags' => ['premium' => true, 'verified' => true],
            ],
        ];

        $this->mediumArray2 = [
            'id' => 123,
            'name' => 'John Doe',
            'email' => 'john.new@example.com',
            'age' => 31,
            'address' => [
                'street' => '456 Oak Ave',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90001',
                'country' => 'USA',
            ],
            'hobbies' => ['reading', 'traveling', 'cycling'],
            'metadata' => [
                'created_at' => '2023-01-01',
                'updated_at' => '2023-12-20',
                'version' => 3,
                'flags' => ['premium' => true, 'verified' => false],
            ],
        ];

        // Large arrays (complex configuration)
        $this->largeArray1 = [
            'application' => [
                'name' => 'MyApp',
                'version' => '2.5.1',
                'environment' => 'production',
                'debug' => false,
            ],
            'database' => [
                'default' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'port' => 3306,
                    'database' => 'myapp',
                    'username' => 'root',
                    'password' => 'secret',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'options' => [
                        'timeout' => 30,
                        'retry' => 3,
                        'ssl' => false,
                    ],
                ],
                'redis' => [
                    'driver' => 'redis',
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                ],
            ],
            'cache' => [
                'driver' => 'redis',
                'ttl' => 3600,
                'prefix' => 'myapp_cache',
            ],
            'mail' => [
                'driver' => 'smtp',
                'host' => 'smtp.mailtrap.io',
                'port' => 2525,
                'encryption' => 'tls',
                'from' => [
                    'address' => 'noreply@example.com',
                    'name' => 'MyApp',
                ],
            ],
            'services' => [
                'analytics' => ['enabled' => true, 'key' => 'GA-12345'],
                'monitoring' => ['enabled' => true, 'endpoint' => 'https://monitor.example.com'],
                'cdn' => ['enabled' => false, 'url' => ''],
            ],
        ];

        $this->largeArray2 = [
            'application' => [
                'name' => 'MyApp',
                'version' => '2.6.0',
                'environment' => 'production',
                'debug' => false,
            ],
            'database' => [
                'default' => [
                    'driver' => 'mysql',
                    'host' => 'db.example.com',
                    'port' => 3306,
                    'database' => 'myapp_prod',
                    'username' => 'appuser',
                    'password' => 'new_secret',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'options' => [
                        'timeout' => 60,
                        'retry' => 5,
                        'ssl' => true,
                    ],
                ],
                'redis' => [
                    'driver' => 'redis',
                    'host' => 'redis.example.com',
                    'port' => 6379,
                    'database' => 1,
                ],
            ],
            'cache' => [
                'driver' => 'memcached',
                'ttl' => 7200,
                'prefix' => 'myapp_cache_v2',
            ],
            'mail' => [
                'driver' => 'ses',
                'region' => 'us-east-1',
                'from' => [
                    'address' => 'noreply@example.com',
                    'name' => 'MyApp Notifications',
                ],
            ],
            'services' => [
                'analytics' => ['enabled' => true, 'key' => 'GA-67890'],
                'monitoring' => ['enabled' => true, 'endpoint' => 'https://monitor.newrelic.com'],
                'cdn' => ['enabled' => true, 'url' => 'https://cdn.example.com'],
            ],
        ];

        // Deeply nested arrays (10 levels deep)
        $this->deeplyNestedArray1 = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => [
                                'level6' => [
                                    'level7' => [
                                        'level8' => [
                                            'level9' => [
                                                'level10' => ['value' => 'deep_value_1', 'id' => 1],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->deeplyNestedArray2 = [
            'level1' => [
                'level2' => [
                    'level3' => [
                        'level4' => [
                            'level5' => [
                                'level6' => [
                                    'level7' => [
                                        'level8' => [
                                            'level9' => [
                                                'level10' => ['value' => 'deep_value_2', 'id' => 2],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Wide arrays (many keys at same level)
        $this->wideArray1 = [];
        $this->wideArray2 = [];

        for ($i = 0; $i < 100; $i++) {
            $this->wideArray1["key_$i"] = [
                'id' => $i,
                'value' => "value_$i",
                'active' => true,
                'metadata' => ['created' => '2023-01-01', 'updated' => '2023-06-15'],
            ];

            $this->wideArray2["key_$i"] = [
                'id' => $i,
                'value' => "value_" . ($i + 1),
                'active' => $i % 2 === 0,
                'metadata' => ['created' => '2023-01-01', 'updated' => '2023-12-20'],
            ];
        }
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSmallArrayStrictComparison(): void
    {
        ArrayDiffMultidimensional::strictComparison($this->smallArray1, $this->smallArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSmallArrayLooseComparison(): void
    {
        ArrayDiffMultidimensional::looseComparison($this->smallArray1, $this->smallArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMediumArrayStrictComparison(): void
    {
        ArrayDiffMultidimensional::strictComparison($this->mediumArray1, $this->mediumArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchMediumArrayLooseComparison(): void
    {
        ArrayDiffMultidimensional::looseComparison($this->mediumArray1, $this->mediumArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchLargeArrayStrictComparison(): void
    {
        ArrayDiffMultidimensional::strictComparison($this->largeArray1, $this->largeArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchLargeArrayLooseComparison(): void
    {
        ArrayDiffMultidimensional::looseComparison($this->largeArray1, $this->largeArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchDeeplyNestedArrayComparison(): void
    {
        ArrayDiffMultidimensional::compare($this->deeplyNestedArray1, $this->deeplyNestedArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(200)
     * @Iterations(5)
     */
    public function benchWideArrayComparison(): void
    {
        ArrayDiffMultidimensional::compare($this->wideArray1, $this->wideArray2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchIdenticalArrays(): void
    {
        ArrayDiffMultidimensional::compare($this->smallArray1, $this->smallArray1);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchCompletelyDifferentArrays(): void
    {
        $a = ['name' => 'Alice', 'age' => 25, 'city' => 'Boston'];
        $b = ['country' => 'USA', 'zip' => '02101', 'active' => true];

        ArrayDiffMultidimensional::compare($a, $b);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchMixedDataTypes(): void
    {
        $a = [
            'int' => 42,
            'float' => 3.14159,
            'string' => 'hello',
            'bool' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'nested' => [
                'deep' => [
                    'value' => 'test',
                    'number' => 123.456,
                ],
            ],
        ];

        $b = [
            'int' => 43,
            'float' => 2.71828,
            'string' => 'world',
            'bool' => false,
            'null' => null,
            'array' => [1, 2, 4],
            'nested' => [
                'deep' => [
                    'value' => 'test',
                    'number' => 789.012,
                ],
            ],
        ];

        ArrayDiffMultidimensional::compare($a, $b);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(200)
     * @Iterations(5)
     */
    public function benchRealisticUserDataset(): void
    {
        $users1 = [];
        $users2 = [];

        for ($i = 0; $i < 50; $i++) {
            $users1[$i] = [
                'id' => $i,
                'name' => "User $i",
                'email' => "user$i@example.com",
                'age' => 20 + $i,
                'active' => true,
                'profile' => [
                    'bio' => "Biography for user $i",
                    'preferences' => [
                        'theme' => 'light',
                        'notifications' => true,
                        'language' => 'en',
                    ],
                ],
            ];

            $users2[$i] = [
                'id' => $i,
                'name' => "User $i",
                'email' => "user$i@example.com",
                'age' => 21 + $i,
                'active' => $i % 2 === 0,
                'profile' => [
                    'bio' => "Updated biography for user $i",
                    'preferences' => [
                        'theme' => $i % 3 === 0 ? 'dark' : 'light',
                        'notifications' => true,
                        'language' => 'en',
                    ],
                ],
            ];
        }

        ArrayDiffMultidimensional::compare($users1, $users2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchComplexNestedConfiguration(): void
    {
        $config1 = $this->generateComplexConfig(1);
        $config2 = $this->generateComplexConfig(2);

        ArrayDiffMultidimensional::compare($config1, $config2);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchEmptyArrayComparison(): void
    {
        $a = ['key1' => 'value1', 'key2' => 'value2'];
        $b = [];

        ArrayDiffMultidimensional::compare($a, $b);
    }

    /**
     * @BeforeMethods({"setUp"})
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchArrayWithNumericKeys(): void
    {
        $a = [
            ['id' => 1, 'name' => 'Item 1', 'value' => 100],
            ['id' => 2, 'name' => 'Item 2', 'value' => 200],
            ['id' => 3, 'name' => 'Item 3', 'value' => 300],
        ];

        $b = [
            ['id' => 1, 'name' => 'Item 1', 'value' => 150],
            ['id' => 2, 'name' => 'Item 2 Updated', 'value' => 200],
            ['id' => 3, 'name' => 'Item 3', 'value' => 350],
        ];

        ArrayDiffMultidimensional::compare($a, $b);
    }

    /**
     * Helper method to generate complex configuration
     */
    private function generateComplexConfig($variant)
    {
        return [
            'app' => [
                'name' => 'TestApp',
                'version' => "1.0.$variant",
                'env' => 'production',
                'debug' => false,
                'url' => "https://example$variant.com",
            ],
            'database' => [
                'connections' => [
                    'mysql' => [
                        'driver' => 'mysql',
                        'host' => "db$variant.example.com",
                        'port' => 3306,
                        'database' => "app_db_$variant",
                        'username' => 'dbuser',
                        'password' => "secret_$variant",
                        'options' => [
                            'timeout' => 30 * $variant,
                            'retry' => 3,
                            'pool' => [
                                'min' => 5,
                                'max' => 20 + $variant,
                            ],
                        ],
                    ],
                    'pgsql' => [
                        'driver' => 'pgsql',
                        'host' => "pg$variant.example.com",
                        'port' => 5432,
                        'database' => "app_pg_$variant",
                    ],
                ],
            ],
            'cache' => [
                'default' => 'redis',
                'stores' => [
                    'redis' => [
                        'driver' => 'redis',
                        'connection' => [
                            'host' => "redis$variant.example.com",
                            'port' => 6379,
                            'database' => $variant,
                        ],
                        'options' => [
                            'ttl' => 3600 * $variant,
                            'prefix' => "cache_v$variant",
                        ],
                    ],
                    'memcached' => [
                        'driver' => 'memcached',
                        'servers' => [
                            ['host' => "mc1.example.com", 'port' => 11211, 'weight' => 100],
                            ['host' => "mc2.example.com", 'port' => 11211, 'weight' => 50 * $variant],
                        ],
                    ],
                ],
            ],
            'services' => [
                'mailer' => [
                    'driver' => 'smtp',
                    'host' => "smtp$variant.example.com",
                    'port' => 587,
                    'encryption' => 'tls',
                    'from' => [
                        'address' => "noreply@example$variant.com",
                        'name' => "TestApp v$variant",
                    ],
                ],
                'queue' => [
                    'driver' => 'sqs',
                    'region' => 'us-east-1',
                    'queue' => "app-queue-$variant",
                ],
            ],
            'logging' => [
                'channels' => [
                    'stack' => ['driver' => 'stack', 'channels' => ['single', 'slack']],
                    'single' => ['driver' => 'single', 'path' => "/var/log/app-$variant.log", 'level' => 'debug'],
                    'slack' => ['driver' => 'slack', 'url' => "https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXX$variant"],
                ],
            ],
        ];
    }
}
