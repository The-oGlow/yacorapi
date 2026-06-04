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

namespace oglow\tools\Addon\Projectdoc\Traits;

use oglow\tools\Yacorapi\ConstData;
use oglow\tools\Yacorapi\IResponse;
use Psr\Log\LoggerInterface;

trait ProjectdocTrait
{
    private static LoggerInterface $logger;

    private static bool $traitInit = false;

    /**
     * @SuppressWarnings("php:S115")
     */
    private function init(): void
    {
        if (!self::$traitInit) {
            define(__NAMESPACE__ . '\CSV_LINE_PDT_PROPERTY_HEADER', 'name;value,controls;document-key;document-url');
            define(__NAMESPACE__ . '\CSV_LINE_PDT_PROPERTY', '%s;%s;%s;%s;%s');

            define(__NAMESPACE__ . '\PDT_RES_MOD_STORAGE', 'storage');
            define(__NAMESPACE__ . '\PDT_RES_MOD_HTML', 'html');
            define(__NAMESPACE__ . '\PDT_RES_MOD_VALUE', 'value');

            define(__NAMESPACE__ . '\PDT_MEDIA_TYPE_JSON', 'json');
            define(__NAMESPACE__ . '\PDT_MEDIA_TYPE_XML', 'xml');

            define(__NAMESPACE__ . '\PDT_RESULT_START_INDEX', 0);
            define(__NAMESPACE__ . '\PDT_RESULT_PAGE_SIZE', 10);
            define(__NAMESPACE__ . '\PDT_RESULT_MAX_SIZE', 20);

            define(__NAMESPACE__ . '\PDT_DOCUMENT_URL', $this->constData->c(ConstData::KEY_CONF_BASE_URL) . '/rest/projectdoc/1/document');
            define(__NAMESPACE__ . '\PDT_PROPERTY_URL', \oglow\tools\Addon\Projectdoc\Traits\PDT_DOCUMENT_URL . '/%s/property');

            define(__NAMESPACE__ . '\PDT_PROP_DOCTYPE', 'Doctype');
            define(__NAMESPACE__ . '\PDT_PROP_NAME', 'Name');
            define(__NAMESPACE__ . '\PDT_PROP_SHORT_NAME', 'Short Name');
            define(__NAMESPACE__ . '\PDT_PROP_SHORT_DESCRIPTION', 'Short Description');
            define(__NAMESPACE__ . '\PDT_PROP_PARENT', 'Parent');
            define(__NAMESPACE__ . '\PDT_PROP_TYPE', 'Type');
            define(__NAMESPACE__ . '\PDT_PROP_ITERATION', 'Iteration');
            define(__NAMESPACE__ . '\PDT_PROP_AUDIENCE', 'Audience');
            define(__NAMESPACE__ . '\PDT_PROP_CATEGORIES', 'Categories');
            define(__NAMESPACE__ . '\PDT_PROP_SUBJECT', 'Subject');
            define(__NAMESPACE__ . '\PDT_PROP_TAGS', 'Tags');
            define(__NAMESPACE__ . '\PDT_PROP_FLAGS', 'Flags');
            define(__NAMESPACE__ . '\PDT_PROP_SORT_KEY', 'Sort Key');

            define(
                __NAMESPACE__ . '\PDT_PROP_ALL_DEFAULT',
                [
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_NAME,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_SHORT_NAME,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_SHORT_DESCRIPTION,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_PARENT,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_TYPE,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_ITERATION,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_AUDIENCE,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_CATEGORIES,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_SUBJECT,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_TAGS,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_FLAGS,
                    \oglow\tools\Addon\Projectdoc\Traits\PDT_PROP_SORT_KEY,
                ]
            );
            self::$traitInit = true;
        }
    }

    /**
     * @param string[] $propertyNames
     * @param string   $spaceKey
     * @param string   $where
     * @param string   $mediaType
     * @param string   $resourceMode
     *
     * @return string
     */
    private function preparePdtDocumentReadUrl(
        array $propertyNames,
        string $spaceKey,
        string $where = "",
        string $mediaType = 'json', // \oglow\tools\Addon\Projectdoc\Traits\PDT_MEDIA_TYPE_JSON
        string $resourceMode = 'storage' // \oglow\tools\Addon\Projectdoc\Traits\PDT_RES_MOD_STORAGE
    ): string {
        $this->init();

        $prepareUrl = sprintf(
            '%s.%s?resource-mode=%s&select=%s&from=%s',
            \oglow\tools\Addon\Projectdoc\Traits\PDT_DOCUMENT_URL,
            $mediaType,
            $resourceMode,
            rawurlencode(implode(',', $propertyNames)),
            $spaceKey
        );
        if (!empty($where)) {
            $prepareUrl .= sprintf('&where=%s', urlencode("\"" . $where . "\""));
        }

        $prepareUrl .= sprintf('&max-hit-count=%s', \oglow\tools\Addon\Projectdoc\Traits\PDT_RESULT_MAX_SIZE);
        $prepareUrl .= sprintf('&start-index=%s', \oglow\tools\Addon\Projectdoc\Traits\PDT_RESULT_START_INDEX);
        $prepareUrl .= sprintf('&max-result=%s', \oglow\tools\Addon\Projectdoc\Traits\PDT_RESULT_PAGE_SIZE);
        $prepareUrl .= sprintf('&expand=%s', 'property');

        return $prepareUrl;
    }

    private function preparePdtPropertyReadUrl(
        int $pageId,
        string $propertyName,
        string $resourceMode = 'storage' // \oglow\tools\Addon\Projectdoc\Traits\PDT_RES_MOD_STORAGE
    ): string {
        $this->init();

        return sprintf(
            '%s/%s?resource-mode=%s',
            sprintf(\oglow\tools\Addon\Projectdoc\Traits\PDT_PROPERTY_URL, $pageId),
            rawurlencode($propertyName),
            $resourceMode
        );
    }

    public function checkDataPdtDocument(IResponse $response, bool $isQuite = false): bool
    {
        $hasData = $response->checkStatus();
        if ($hasData) {
            if (!($response->keyExists('document') && $response->keyExists('key-list'))) {
                $hasData = false;
            } else {
                if (!$isQuite) {
                    $this->showTotalsPdtDocument($response);
                }
            }
        }

        return $hasData;
    }

    /**
     * @param IResponse $response
     */
    private function showTotalsPdtDocument(IResponse $response): void
    {
        $total = $response->getValue(IResponse::KEY_MAX_RESULT);
        $size  = $response->getValue(IResponse::KEY_SIZE);
        $start = $response->getValue(IResponse::KEY_START_INDEX);
        $limit = $response->getValue(IResponse::KEY_MAX_RESULT);

        self::$logger->info('Total,Start,Size,Limit', [$total, $start, $size, $limit]);
    }

    /**
     * @param IResponse $response
     *
     * @return bool
     */
    public function checkDataPdtProperty(IResponse $response): bool
    {
        $status = $response->checkStatus();
        if ($status && !($response->keyExists(IResponse::KEY_NAME) && $response->keyExists(IResponse::KEY_VALUE))) {
            $status = false;
        }

        return $status;
    }

    public function showResultsPdt(IResponse $response, string $propertyName, ?int $idx = null): string
    {
        $line = '';

        if (isset($idx)) {
            $line = sprintf('%s;', $idx);
        }
        $line .= $this->prepareCsvLinePdtProperty($response, $propertyName);

        return $line;
    }

    /**
     * @param IResponse $response
     * @param string    $propertyName
     *
     * @return string
     */
    private function prepareCsvLinePdtProperty(IResponse $response, string $propertyName = ""): string
    {
        $line = "";
        if ($response->isResultsAvailable() && $response->keyExists('name')) {
            $line .= $this->prepareCsvLine(
                \oglow\tools\Addon\Projectdoc\Traits\CSV_LINE_PDT_PROPERTY,
                $response->getValue('name'),
                $response->getValue('value'),
                $response->getValue('controls'),
                $response->getValue('document-key') ?? '',
                $response->getValue('document-url') ?? ''
            );
        } else {
            if (!empty($propertyName)) {
                $line .= $this->prepareCsvLine(\oglow\tools\Addon\Projectdoc\Traits\CSV_LINE_PDT_PROPERTY, $propertyName, '', '', '', '');
            }
        }

        return $line;
    }

    /**
     * @param string                     $format
     * @param null|bool|float|int|string $param
     *
     * @return false|string
     */
    private function prepareCsvLine(string $format, ...$param): bool|string
    {
        return sprintf($format . "\n", ...$param);
    }

    /**
     * @param int    $pageId
     * @param string $propertyName
     *
     * @return IResponse
     */
    public function pdtReadProperty(int $pageId, string $propertyName): IResponse
    {
        $prepareUrl = $this->preparePdtPropertyReadUrl($pageId, $propertyName);

        return $this->exec($prepareUrl);
    }

    /**
     * @param string[] $propertyNames
     * @param string   $spaceKey
     * @param string   $where
     * @param string   $mediaType
     * @param string   $resourceMode
     *
     * @return IResponse
     */
    public function pdtReadDocument(
        array $propertyNames,
        string $spaceKey,
        string $where,
        string $mediaType = PDT_MEDIA_TYPE_JSON,
        string $resourceMode = PDT_RES_MOD_STORAGE
    ): IResponse {
        $prepareUrl = $this->preparePdtDocumentReadUrl($propertyNames, $spaceKey, $where, $mediaType, $resourceMode);

        return $this->exec($prepareUrl);
    }
}
