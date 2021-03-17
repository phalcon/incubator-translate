<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Tests\Functional\Adapter\Mongo;

use ArrayAccess;
use Phalcon\Incubator\Translate\Adapter\Mongo;
use Phalcon\Incubator\Translate\Test\Fixtures\Collections\Translate;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\InterpolatorFactory;
use UnitTester;

class ConstructCest extends Base
{

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\Mongo :: __construct()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterMongoConstruct(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\Mongo - constructor');

        $translator = new Mongo(
            new Translate(),
            "en",
            new InterpolatorFactory()
        );

        $I->assertInstanceOf(
            ArrayAccess::class,
            $translator
        );

        $I->assertInstanceOf(
            AdapterInterface::class,
            $translator
        );
    }
}
