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
use Phalcon\Translate\Exception;
use Phalcon\Translate\InterpolatorFactory;
use UnitTester;

class TranslateCest
{
    use TranslateCsvMultiTrait;

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: t()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterCsvTranslate(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - t()');

        $csv = $this->getCsvConfig();

        $translate = new CsvMulti(
            $csv,
            "en_US",
            new InterpolatorFactory()
        );

        $translateFr = new CsvMulti(
            $csv,
            "fr_FR",
            new InterpolatorFactory()
        );

        $I->assertSame('non-key', $translate->t('non-key'));
        $I->assertSame('street', $translate->t('label_street'));
        $I->assertSame('rue', $translateFr->t('label_street'));

        $I->assertSame('non-key', $translate->_('non-key'));
        $I->assertSame('street', $translate->_('label_street'));
        $I->assertSame('rue', $translateFr->_('label_street'));
    }
}
