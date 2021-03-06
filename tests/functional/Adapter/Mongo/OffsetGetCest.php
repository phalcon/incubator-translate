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

namespace  Phalcon\Incubator\Translate\Tests\Functional\Adapter\Mongo;

use FunctionalTester;

class OffsetGetCest extends Base
{
    public function translateAdapterMongoOffsetGet(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Mongo - offset get');

        $translator = $this->initTranslate();

        $I->assertSame(
            $translator->offsetGet(self::TRANSLATE_KEY),
            self::TRANSLATE_VALUE_EN
        );

        $I->assertSame(
            $translator->offsetGet("no-key"),
            "no-key"
        );
    }
}
