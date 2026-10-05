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

namespace oglow\tools\Yacorapi\Data;

/**
 * @author ollily
 */
enum NamespacesEnum: string
{
    case ac = "http://atlassian.com/content";
    case ri = "http://atlassian.com/resource/identifier";

    /**
     * @return string
     */
    public function xmlNs(): string
    {
        return sprintf('xmlns:%s="%s"', $this->name, $this->value);
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return  [$this->name => $this->value];
    }
}
