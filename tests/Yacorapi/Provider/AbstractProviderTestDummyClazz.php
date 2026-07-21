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

namespace oglow\tools\Yacorapi\Provider;

use Ds\Map;
use oglow\tools\Yacorapi\Request\RequestTypeEnum;

class AbstractProviderTestDummyClazz extends AbstractProvider
{
    /**
     * @param string          $execUrl
     * @param RequestTypeEnum $reqType
     *
     * @return array<mixed,mixed>
     */
    #[\Override]
    protected function execInternal(string $execUrl, RequestTypeEnum $reqType): array
    {
        return [];
    }

    /**
     * @param string           $execUrl
     * @param Map<mixed,mixed> $parameters
     * @param RequestTypeEnum  $reqType
     *
     * @return array<mixed,mixed>
     */
    #[\Override]
    protected function execPostInternal(string $execUrl, Map $parameters, RequestTypeEnum $reqType): array
    {
        return [];
    }

    // Change visibility

    /**
     * @param string          $execUrl
     * @param RequestTypeEnum $reqType
     *
     * @return array<mixed,mixed>
     */
    public function publicExecInternal(string $execUrl, RequestTypeEnum $reqType): array
    {
        return $this->execInternal($execUrl, $reqType);
    }

    /**
     * @param string           $execUrl
     * @param Map<mixed,mixed> $parameters
     * @param RequestTypeEnum  $reqType
     *
     * @return array<mixed,mixed>
     */
    public function publicExecPostInternal(string $execUrl, Map $parameters, RequestTypeEnum $reqType): array
    {
        return $this->execPostInternal($execUrl, $parameters, $reqType);
    }

    /**
     * @return string
     */
    public function publicGetTokenValue(): string
    {
        return parent::getTokenValue();
    }

    /**
     * @return string
     */
    public function publicGetAuthValue(): string
    {
        return parent::getAuthValue();
    }
}
