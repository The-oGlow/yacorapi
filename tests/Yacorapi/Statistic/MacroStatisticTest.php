<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace oglow\tools\Yacorapi\Statistic;

use PHPUnit\Framework\EasyGoingTestCase;

/**
 * Description of MacroStatisticTest
 *

 */
class MacroStatisticTest extends EasyGoingTestCase {

    #[\Override]
    protected function getCasto2t(): MacroStatistic {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): mixed {
        return new MacroStatistic('');
    }

    public function testToString(): void {
        $expected = sprintf("%s:[%s,%s,%s]",
                MacroStatistic::class,
                MacroStatistic::STATISTIC_NAME,
                MacroStatistic::EXPORT_NAME, '{}');

        $actual = $this->getCasto2t()->__toString();

        $this->assertEquals($expected, $actual);
    }
}
