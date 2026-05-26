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

class AbstractProviderTestDummyClazz extends AbstractProvider
{
    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function execInternal($execUrl, $reqType)
    {
        return [];
    }

    /**
     * @param string               $execUrl
     * @param \Ds\Map<mixed,mixed> $parameters
     * @param int                  $reqType
     *
     * @return array<mixed,mixed>
     */
    protected function execPostInternal($execUrl, $parameters, $reqType)
    {
        return [];
    }

    // Change visibility

    /**
     * @param string $execUrl
     * @param int    $reqType
     *
     * @return array<mixed,mixed>
     */
    public function publicExecInternal(string $execUrl, int $reqType)
    {
        return $this->execInternal($execUrl, $reqType);
    }

    /**
     * @param string               $execUrl
     * @param \Ds\Map<mixed,mixed> $parameters
     * @param int                  $reqType
     *
     * @return array<mixed,mixed>
     */
    public function publicExecPostInternal(string $execUrl, $parameters, int $reqType)
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
