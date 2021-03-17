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

namespace  Phalcon\Incubator\Translate\Tests\Functional\Adapter\Redis;

use FunctionalTester;

class ExistsCest extends Base
{
    public function translateAdapterRedisExists(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Redis - exists');

        $translator = $this->initTranslate();

        $I->assertTrue(
            $translator->offsetExists(self::TRANSLATE_KEY)
        );
    }

    public function translateAdapterMongoExistsNegative(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Redis - not exists');

        $translator = $this->initTranslate();

        $I->assertFalse(
            $translator->offsetExists("non-key")
        );
    }
}
