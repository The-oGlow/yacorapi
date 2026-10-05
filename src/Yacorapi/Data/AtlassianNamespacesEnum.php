<?php

/*
 * Copyright 2026 GLO03.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace oglow\tools\Yacorapi\Data;

/**
 *
 * @author ollily
 */
enum AtlassianNamespacesEnum: string {

    case atlassian_content = "http://atlassian.com/content";
    case ac = "http://atlassian.com/content";
    case ri = "http://atlassian.com/resource/identifier";
    
    public function xmlNs(): string {
        return sprintf('xmlns:%s="%s"', strtolower($this->name), strtolower($this->value));
    }
}
