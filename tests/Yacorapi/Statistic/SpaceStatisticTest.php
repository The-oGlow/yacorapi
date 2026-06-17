<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace oglow\tools\Yacorapi\Statistic;

use PHPUnit\Framework\EasyGoingTestCase;

/**
 * Description of SpaceStatisticTest
 *

 */
class SpaceStatisticTest extends EasyGoingTestCase {

    #[\Override]
    protected function getCasto2t(): SpaceStatistic {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): mixed {
        return new SpaceStatistic('');
    }

    public function testToString(): void {
        $expected = sprintf("%s:[%s,%s,%s]",
                SpaceStatistic::class,
                SpaceStatistic::STATISTIC_NAME,
                SpaceStatistic::EXPORT_NAME, '{}');

        $actual = $this->getCasto2t()->__toString();

        $this->assertEquals($expected, $actual);
    }
}
