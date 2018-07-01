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

return [
    'meta' => [
        'title' => 'Native constant calls',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-functions' => true,
    ],

    'Internal function in a namespace: make the call into a FQ call' => <<<'PHP'
<?php

namespace Acme;

$x = DIRECTORY_SEPARATOR;

----
<?php

namespace Humbug\Acme;

$x = \DIRECTORY_SEPARATOR;

PHP
    ,

    'Namespaced function having the same name as an internal function: prefix & make the call into a FQ call' => <<<'PHP'
<?php

namespace Acme;

use const Acme\DIRECTORY_SEPARATOR;

$x = DIRECTORY_SEPARATOR;

----
<?php

namespace Humbug\Acme;

use const Humbug\Acme\DIRECTORY_SEPARATOR;
$x = \Humbug\Acme\DIRECTORY_SEPARATOR;

PHP
    ,
];
