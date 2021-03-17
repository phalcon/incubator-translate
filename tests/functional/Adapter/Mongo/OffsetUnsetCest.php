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
use Phalcon\Incubator\Translate\Test\Fixtures\Collections\Translate;

class OffsetUnsetCest extends Base
{
    public function translateAdapterMongoOffsetUnset(FunctionalTester $I): void
    {
        $I->wantToTest('Translate\Adapter\Mongo - offset unset');

        $translator = $this->initTranslate();

        $translator->offsetUnset(self::TRANSLATE_KEY);

        $translate = Translate::findFirst([["key" => self::TRANSLATE_KEY]]);

        $I->assertSame($translate->ru, self::TRANSLATE_VALUE_RU);
        $I->assertSame($translator->_(self::TRANSLATE_KEY), self::TRANSLATE_KEY);
        $I->assertEmpty($translate->en);
    }
}
