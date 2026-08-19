<?php

declare(strict_types=1);

/*
 * This file is part of ezlogging
 *
 * (c) 2024 Oliver Glowa, coding.glowa.com
 *
 * This source file is subject to the Apache-2.0 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace oglow\tools\Yacorapi\Extension;

use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\Data\ItemTypeEnum;
use oglow\tools\Yacorapi\Data\QueryExtensionEnum;
use oglow\tools\Yacorapi\Data\SpaceTypeEnum;

trait PrepSpaceTrait
{
    public function prepareSpacePagesUrl(
        string $space,
        ItemTypeEnum $pageType = ItemTypeEnum::PAGE,
        int $start = ConstData::PAGE_START,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return sprintf(
            '%s/%s/content/%s?start=%s&limit=%s&%s',
            $this->constData->c(ConstData::KEY_CONF_SPACE_URL),
            $space,
            $pageType->value,
            $start,
            $limit,
            QueryExtensionEnum::REQP_FULL->value
        );
    }

    public function prepareSpaceListUrl(
        SpaceTypeEnum $spaceType = SpaceTypeEnum::SPACE_TYPE_GLOBAL,
        int $limit = ConstData::PAGE_LIMIT
    ): string {
        return sprintf(
            '%s?%s&type=%s&limit=%s',
            $this->constData->c(ConstData::KEY_CONF_SPACE_URL),
            QueryExtensionEnum::REQP_SPACE_LIST->value,
            $spaceType->value,
            $limit
        );
    }

    //    public function prepareSpaceArray(?array $results, bool $noArchived = true, bool $asCsv = true): array
    //    {
    //        $spaces = [];
    //
    //        if (is_array($results))
    //        {
    //            if ($asCsv)
    //            {
    //                $idx = 0;
    //                foreach ($results as $result)
    //                {
    //                    $line = '';
    //                    if (is_array($result))
    //                    {
    //                        $addResult = true;
    //                        if ($noArchived)
    //                        {
    //                            $descr = $result['description']['plain']['value'];
    //                            if (false !== stripos($descr, SpaceResponseAdapter::SPACE_ARCH_FLAG1) || (false !== stripos(
    //                                        $descr,
    //                                        SpaceResponseAdapter::SPACE_ARCH_FLAG2
    //                                    ))
    //                            )
    //                            {
    //                                $addResult = false;
    //                            }
    //                        }
    //                        if ($addResult)
    //                        {
    //                            $spaces[] = $result['key'];
    //
    //                            $line .= sprintf(
    //                                '%s;%s;%s;%s',
    //                                $idx++,
    //                                $result['key'],
    //                                $result['type'],
    //                                'status'
    //                            );
    //                            $line .= sprintf(
    //                                ";\"%s\";\"%s\"",
    //                                $result['name'],
    //                                htmlentities(
    //                                    implode(
    //                                        explode(
    //                                            PHP_EOL,
    //                                            $result['description']['plain']['value']
    //                                        )
    //                                    )
    //                                )
    //                            );
    //                        } else
    //                        {
    //                            self::$logger->info('  ++ Space already archived', [$result['key']]);
    //                        }
    //                    }
    //                    self::$logger->debug($line);
    //                }
    //            } else
    //            {
    //                foreach ($results as $result)
    //                {
    //                    if (is_array($result))
    //                    {
    //                        $spaces[] = $result['key'];
    //                    }
    //                }
    //            }
    //            natcasesort($spaces);
    //        }
    //
    //        return $spaces;
    //    }

    //    public function prepareMySpaceFile(array $spaces): string
    //    {
    //        $line = "<?php\ndeclare(strict_types=1);\nfunction _getSpaceListAll(): array {\nreturn [\n";
    //        foreach ($spaces as $space)
    //        {
    //            $line .= sprintf("' % s',\n", $space[SpaceResponseAdapter::SPACE_KEY]);
    //        }
    //        $line .= "\n];}\n";
    //
    //        return $line;
    //    }
}
