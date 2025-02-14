<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\Scoper\Composer;

use Generator;
use function Humbug\PhpScoper\create_fake_patcher;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Scoper\FakeScoper;
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper
 * @covers \Humbug\PhpScoper\Scoper\Composer\AutoloadPrefixer
 */
class InstalledPackagesScoperTest extends TestCase
{
    public function test_it_is_a_Scoper(): void
    {
        $this->assertTrue(is_a(InstalledPackagesScoper::class, Scoper::class, true));
    }

    public function test_delegates_scoping_to_the_decorated_scoper_if_is_not_a_installed_file(): void
    {
        $filePath = 'file.php';
        $fileContents = '';
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, true, true, 'Foo');

        /** @var Scoper|ObjectProphecy $decoratedScoperProphecy */
        $decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $decoratedScoperProphecy
            ->scope($filePath, $fileContents, $prefix, $patchers, $whitelist)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;
        /** @var Scoper $decoratedScoper */
        $decoratedScoper = $decoratedScoperProphecy->reveal();

        $scoper = new InstalledPackagesScoper($decoratedScoper);

        $actual = $scoper->scope($filePath, $fileContents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);

        $decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @dataProvider provideInstalledPackagesFiles
     */
    public function test_it_prefixes_the_composer_autoloaders(string $fileContents, string $expected): void
    {
        $filePath = 'composer/installed.json';

        $scoper = new InstalledPackagesScoper(new FakeScoper());

        $prefix = 'Foo';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, true, true, 'Foo');

        $actual = $scoper->scope($filePath, $fileContents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);
    }

    public function provideInstalledPackagesFiles(): Generator
    {
        yield [
            <<<'JSON'
[
    {
        "name": "beberlei/assert",
        "version": "v2.7.6",
        "version_normalized": "2.7.6.0",
        "source": {
            "type": "git",
            "url": "https://github.com/beberlei/assert.git",
            "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563"
        },
        "dist": {
            "type": "zip",
            "url": "https://api.github.com/repos/beberlei/assert/zipball/8726e183ebbb0169cb6cb4832e22ebd355524563",
            "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563",
            "shasum": ""
        },
        "require": {
            "ext-mbstring": "*",
            "php": ">=5.3"
        },
        "require-dev": {
            "friendsofphp/php-cs-fixer": "^2.1.1",
            "phpunit/phpunit": "^4|^5"
        },
        "time": "2017-05-04T02:00:24+00:00",
        "type": "library",
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Assert\\": "lib/Assert"
            },
            "files": [
                "lib/Assert/functions.php"
            ]
        },
        "notification-url": "https://packagist.org/downloads/",
        "license": [
            "BSD-2-Clause"
        ],
        "authors": [
            {
                "name": "Benjamin Eberlei",
                "email": "kontakt@beberlei.de",
                "role": "Lead Developer"
            },
            {
                "name": "Richard Quadling",
                "email": "rquadling@gmail.com",
                "role": "Collaborator"
            }
        ],
        "description": "Thin assertion library for input validation in business models.",
        "keywords": [],
        "platform": {}
    }
]

JSON
            ,
            <<<'JSON'
[
    {
        "name": "beberlei\/assert",
        "version": "v2.7.6",
        "version_normalized": "2.7.6.0",
        "source": {
            "type": "git",
            "url": "https:\/\/github.com\/beberlei\/assert.git",
            "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563"
        },
        "dist": {
            "type": "zip",
            "url": "https:\/\/api.github.com\/repos\/beberlei\/assert\/zipball\/8726e183ebbb0169cb6cb4832e22ebd355524563",
            "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563",
            "shasum": ""
        },
        "require": {
            "ext-mbstring": "*",
            "php": ">=5.3"
        },
        "require-dev": {
            "friendsofphp\/php-cs-fixer": "^2.1.1",
            "phpunit\/phpunit": "^4|^5"
        },
        "time": "2017-05-04T02:00:24+00:00",
        "type": "library",
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Foo\\Assert\\": "lib\/Assert"
            },
            "files": [
                "lib\/Assert\/functions.php"
            ]
        },
        "notification-url": "https:\/\/packagist.org\/downloads\/",
        "license": [
            "BSD-2-Clause"
        ],
        "authors": [
            {
                "name": "Benjamin Eberlei",
                "email": "kontakt@beberlei.de",
                "role": "Lead Developer"
            },
            {
                "name": "Richard Quadling",
                "email": "rquadling@gmail.com",
                "role": "Collaborator"
            }
        ],
        "description": "Thin assertion library for input validation in business models.",
        "keywords": [],
        "platform": {}
    }
]
JSON
        ];

        yield [
            <<<'JSON'
[
    {
        "name": "fideloper\/proxy",
        "version": "4.0.0",
        "version_normalized": "4.0.0.0",
        "source": {
            "type": "git",
            "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
            "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
        },
        "dist": {
            "type": "zip",
            "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
            "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
            "shasum": ""
        },
        "require": {
            "illuminate\/contracts": "~5.0",
            "php": ">=5.4.0"
        },
        "require-dev": {
            "illuminate\/http": "~5.6",
            "mockery\/mockery": "~1.0",
            "phpunit\/phpunit": "^6.0"
        },
        "time": "2018-02-07T20:20:57+00:00",
        "type": "library",
        "extra": {
            "laravel": {
                "providers": [
                    "Fideloper\\Proxy\\TrustedProxyServiceProvider"
                ]
            }
        },
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Fideloper\\Proxy\\": "src\/"
            }
        },
        "notification-url": "https:\/\/packagist.org\/downloads\/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "Chris Fidao",
                "email": "fideloper@gmail.com"
            }
        ],
        "description": "Set trusted proxies for Laravel",
        "keywords": [
            "load balancing",
            "proxy",
            "trusted proxy"
        ]
    }
]

JSON
            ,
            <<<'JSON'
[
    {
        "name": "fideloper\/proxy",
        "version": "4.0.0",
        "version_normalized": "4.0.0.0",
        "source": {
            "type": "git",
            "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
            "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
        },
        "dist": {
            "type": "zip",
            "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
            "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
            "shasum": ""
        },
        "require": {
            "illuminate\/contracts": "~5.0",
            "php": ">=5.4.0"
        },
        "require-dev": {
            "illuminate\/http": "~5.6",
            "mockery\/mockery": "~1.0",
            "phpunit\/phpunit": "^6.0"
        },
        "time": "2018-02-07T20:20:57+00:00",
        "type": "library",
        "extra": {
            "laravel": {
                "providers": [
                    "Foo\\Fideloper\\Proxy\\TrustedProxyServiceProvider"
                ]
            }
        },
        "installation-source": "dist",
        "autoload": {
            "psr-4": {
                "Foo\\Fideloper\\Proxy\\": "src\/"
            }
        },
        "notification-url": "https:\/\/packagist.org\/downloads\/",
        "license": [
            "MIT"
        ],
        "authors": [
            {
                "name": "Chris Fidao",
                "email": "fideloper@gmail.com"
            }
        ],
        "description": "Set trusted proxies for Laravel",
        "keywords": [
            "load balancing",
            "proxy",
            "trusted proxy"
        ]
    }
]
JSON
        ];
    }
}
