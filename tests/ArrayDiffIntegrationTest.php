<?php

use PHPUnit\Framework\TestCase;
use Rogervila\ArrayDiffMultidimensional;

class ArrayDiffIntegrationTest extends TestCase
{
    /** @test */
    public function it_handles_real_world_configuration_arrays()
    {
        $diff = new ArrayDiffMultidimensional();

        $newConfig = [
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'username' => 'user',
                'password' => 'new_password',
                'options' => [
                    'charset' => 'utf8mb4',
                    'timeout' => 30
                ]
            ],
            'cache' => [
                'driver' => 'redis',
                'connection' => [
                    'host' => '127.0.0.1',
                    'port' => 6379
                ]
            ],
            'features' => [
                'new_feature' => true,
                'old_feature' => false
            ]
        ];

        $oldConfig = [
            'database' => [
                'host' => 'localhost',
                'port' => 3306,
                'username' => 'user',
                'password' => 'old_password',
                'options' => [
                    'charset' => 'utf8mb4',
                    'timeout' => 60
                ]
            ],
            'cache' => [
                'driver' => 'file',
                'connection' => [
                    'path' => '/tmp/cache'
                ]
            ],
            'features' => [
                'old_feature' => true
            ]
        ];

        $result = $diff->compare($newConfig, $oldConfig);

        $expected = [
            'database' => [
                'password' => 'new_password',
                'options' => [
                    'timeout' => 30
                ]
            ],
            'cache' => [
                'driver' => 'redis',
                'connection' => [
                    'host' => '127.0.0.1',
                    'port' => 6379
                ]
            ],
            'features' => [
                'new_feature' => true,
                'old_feature' => false
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_user_profile_updates()
    {
        $diff = new ArrayDiffMultidimensional();

        $newProfile = [
            'id' => 123,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'preferences' => [
                'theme' => 'dark',
                'language' => 'en',
                'notifications' => [
                    'email' => true,
                    'sms' => false,
                    'push' => true
                ]
            ],
            'addresses' => [
                [
                    'type' => 'home',
                    'street' => '123 Main St',
                    'city' => 'Anytown',
                    'zip' => '12345'
                ],
                [
                    'type' => 'work',
                    'street' => '456 Business Ave',
                    'city' => 'Corporate City',
                    'zip' => '67890'
                ]
            ]
        ];

        $oldProfile = [
            'id' => 123,
            'name' => 'John Doe',
            'email' => 'john.old@example.com',
            'preferences' => [
                'theme' => 'light',
                'language' => 'en',
                'notifications' => [
                    'email' => true,
                    'sms' => true,
                    'push' => false
                ]
            ],
            'addresses' => [
                [
                    'type' => 'home',
                    'street' => '789 Old St',
                    'city' => 'Oldtown',
                    'zip' => '54321'
                ]
            ]
        ];

        $result = $diff->compare($newProfile, $oldProfile);

        $expected = [
            'email' => 'john.doe@example.com',
            'preferences' => [
                'theme' => 'dark',
                'notifications' => [
                    'sms' => false,
                    'push' => true
                ]
            ],
            'addresses' => [
                [
                    'street' => '123 Main St',
                    'city' => 'Anytown',
                    'zip' => '12345'
                ],
                [
                    'type' => 'work',
                    'street' => '456 Business Ave',
                    'city' => 'Corporate City',
                    'zip' => '67890'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_api_response_comparison()
    {
        $diff = new ArrayDiffMultidimensional();

        $newResponse = [
            'status' => 'success',
            'data' => [
                'users' => [
                    ['id' => 1, 'name' => 'Alice', 'active' => true],
                    ['id' => 2, 'name' => 'Bob', 'active' => false],
                    ['id' => 3, 'name' => 'Charlie', 'active' => true]
                ],
                'meta' => [
                    'total' => 3,
                    'page' => 1,
                    'per_page' => 10,
                    'last_updated' => '2023-01-15T10:30:00Z'
                ]
            ]
        ];

        $oldResponse = [
            'status' => 'success',
            'data' => [
                'users' => [
                    ['id' => 1, 'name' => 'Alice', 'active' => false],
                    ['id' => 2, 'name' => 'Robert', 'active' => false]
                ],
                'meta' => [
                    'total' => 2,
                    'page' => 1,
                    'per_page' => 10,
                    'last_updated' => '2023-01-14T15:20:00Z'
                ]
            ]
        ];

        $result = $diff->compare($newResponse, $oldResponse);

        $expected = [
            'data' => [
                'users' => [
                    ['active' => true],
                    ['name' => 'Bob'],
                    ['id' => 3, 'name' => 'Charlie', 'active' => true]
                ],
                'meta' => [
                    'total' => 3,
                    'last_updated' => '2023-01-15T10:30:00Z'
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_shopping_cart_updates()
    {
        $diff = new ArrayDiffMultidimensional();

        $newCart = [
            'items' => [
                [
                    'id' => 'item_1',
                    'name' => 'Product A',
                    'price' => 29.99,
                    'quantity' => 2,
                    'options' => ['size' => 'L', 'color' => 'blue']
                ],
                [
                    'id' => 'item_2',
                    'name' => 'Product B',
                    'price' => 15.50,
                    'quantity' => 1,
                    'options' => ['variant' => 'premium']
                ]
            ],
            'totals' => [
                'subtotal' => 75.48,
                'tax' => 7.55,
                'shipping' => 5.99,
                'total' => 89.02
            ],
            'shipping_address' => [
                'name' => 'Jane Doe',
                'street' => '456 Oak Ave',
                'city' => 'Springfield',
                'zip' => '12345'
            ]
        ];

        $oldCart = [
            'items' => [
                [
                    'id' => 'item_1',
                    'name' => 'Product A',
                    'price' => 29.99,
                    'quantity' => 1,
                    'options' => ['size' => 'M', 'color' => 'blue']
                ]
            ],
            'totals' => [
                'subtotal' => 29.99,
                'tax' => 3.00,
                'shipping' => 5.99,
                'total' => 38.98
            ]
        ];

        $result = $diff->compare($newCart, $oldCart);

        $expected = [
            'items' => [
                [
                    'quantity' => 2,
                    'options' => ['size' => 'L']
                ],
                [
                    'id' => 'item_2',
                    'name' => 'Product B',
                    'price' => 15.50,
                    'quantity' => 1,
                    'options' => ['variant' => 'premium']
                ]
            ],
            'totals' => [
                'subtotal' => 75.48,
                'tax' => 7.55,
                'total' => 89.02
            ],
            'shipping_address' => [
                'name' => 'Jane Doe',
                'street' => '456 Oak Ave',
                'city' => 'Springfield',
                'zip' => '12345'
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_handles_form_data_validation_errors()
    {
        $diff = new ArrayDiffMultidimensional();

        $newErrors = [
            'email' => ['Invalid email format'],
            'password' => ['Too short', 'Must contain special characters'],
            'profile' => [
                'age' => ['Must be a number'],
                'preferences' => [
                    'newsletter' => ['Must be true or false']
                ]
            ]
        ];

        $oldErrors = [
            'email' => ['Required field'],
            'profile' => [
                'age' => ['Required field'],
                'preferences' => [
                    'theme' => ['Invalid option']
                ]
            ]
        ];

        $result = $diff->compare($newErrors, $oldErrors);

        $expected = [
            'email' => ['Invalid email format'],
            'password' => ['Too short', 'Must contain special characters'],
            'profile' => [
                'age' => ['Must be a number'],
                'preferences' => [
                    'newsletter' => ['Must be true or false']
                ]
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function it_maintains_performance_with_complex_real_world_data()
    {
        $diff = new ArrayDiffMultidimensional();

        // Simulate a complex application state
        $newState = $this->generateComplexApplicationState(1000);
        $oldState = $this->generateComplexApplicationState(1000);

        // Make some targeted changes
        $newState['modules']['user_management']['users'][500]['status'] = 'inactive';
        $newState['configuration']['features']['new_dashboard'] = true;
        $newState['cache']['invalidated_keys'][] = 'user_500_profile';

        $start = microtime(true);
        $result = $diff->compare($newState, $oldState);
        $end = microtime(true);

        $this->assertArrayHasKey('modules', $result);
        $this->assertArrayHasKey('configuration', $result);
        $this->assertArrayHasKey('cache', $result);
        $this->assertLessThan(2.0, $end - $start, 'Should handle complex real-world data efficiently');
    }

    private function generateComplexApplicationState($userCount)
    {
        $state = [
            'modules' => [
                'user_management' => [
                    'users' => [],
                    'permissions' => array_fill_keys(range(1, 50), ['read', 'write']),
                    'groups' => array_fill_keys(range(1, 10), ['name' => 'Group', 'permissions' => []])
                ],
                'content_management' => [
                    'articles' => array_fill_keys(range(1, 200), ['title' => 'Article', 'content' => 'Content']),
                    'categories' => array_fill_keys(range(1, 20), ['name' => 'Category'])
                ]
            ],
            'configuration' => [
                'database' => ['host' => 'localhost', 'port' => 3306],
                'cache' => ['ttl' => 3600, 'driver' => 'redis'],
                'features' => array_fill_keys(range(1, 30), true)
            ],
            'cache' => [
                'keys' => array_fill_keys(range(1, 500), 'cached_value'),
                'invalidated_keys' => []
            ]
        ];

        // Generate users
        for ($i = 1; $i <= $userCount; $i++) {
            $state['modules']['user_management']['users'][$i] = [
                'id' => $i,
                'name' => "User $i",
                'email' => "user$i@example.com",
                'status' => 'active',
                'profile' => [
                    'age' => rand(18, 80),
                    'preferences' => [
                        'theme' => 'light',
                        'notifications' => true
                    ]
                ]
            ];
        }

        return $state;
    }
}
