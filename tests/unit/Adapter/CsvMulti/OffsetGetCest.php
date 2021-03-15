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

namespace Phalcon\Incubator\Translate\Tests\Unit\Adapter\CsvMulti;

use Phalcon\Incubator\Translate\Adapter\CsvMulti;
use Phalcon\Incubator\Translate\Test\Fixtures\Traits\TranslateCsvMultiTrait;
use Phalcon\Translate\InterpolatorFactory;
use UnitTester;

class OffsetGetCest
{
    use TranslateCsvMultiTrait;

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: offsetGet()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterCsvOffsetGet(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - offsetGet()');

        $csv = $this->getCsvConfig();

        $translator = new CsvMulti(
            $csv,
            "fr_FR",
            new InterpolatorFactory()
        );

        $I->assertEquals(
            'maison',
            $translator->offsetGet('label_home')
        );
    }
}
