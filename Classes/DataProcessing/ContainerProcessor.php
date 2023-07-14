<?php

declare(strict_types=1);

namespace ITplusX\HeadlessContainer\DataProcessing;

/*
 * This file is part of the "headless_container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use B13\Container\Domain\Model\Container;
use B13\Container\Tca\Registry;
use FriendsOfTYPO3\Headless\DataProcessing\DataProcessingTrait;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ContainerProcessor extends \B13\Container\DataProcessing\ContainerProcessor
{
    use DataProcessingTrait;

    private LanguageServiceFactory $languageServiceFactory;

    public function injectLanguageServiceFactory(LanguageServiceFactory $languageServiceFactory): void
    {
        $this->languageServiceFactory = $languageServiceFactory;
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        try {
            $contentId = (int)$cObj->stdWrapValue('contentId', $processorConfiguration, $cObj->data['uid']);

            /** @var Container $container */
            $container = $this->containerFactory->buildContainer($contentId);
        } catch (Exception $e) {
            return $processedData;
        }

        $parentProcessedData = parent::process($cObj, $contentObjectConfiguration, $processorConfiguration, $processedData);

        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'items');
        $sourceColPos = $cObj->stdWrapValue('colPos', $processorConfiguration);

        $regexPattern = '\b(children_(\d+))\b';
        if (empty($sourceColPos) === false) {
            $regexPattern = '\b(children)\b';

            if (empty($targetVariableName) === false) {
                $regexPattern = '\b(' . $targetVariableName . ')\b';
            }
        }

        $containerElements = array_filter($parentProcessedData, function ($key) use ($regexPattern) {
            return preg_match('/' . $regexPattern . '/', $key) === 1;
        }, ARRAY_FILTER_USE_KEY);

        /** @var Registry $containerRegistry */
        $containerRegistry = GeneralUtility::makeInstance(Registry::class);

        $items = [];
        foreach ($containerElements as $key => $children) {
            $contentElements = [];

            preg_match('/' . $regexPattern . '/', $key, $matches);
            $colPos = (int)$sourceColPos ?: (int)$matches[2];

            foreach ($children as $child) {
                $contentElements[] = \json_decode($child['renderedContent'], true);
            }

            $items[] = [
                'config' => [
                    'name' => $this->getLanguageService($cObj->getRequest())->sL(
                        $containerRegistry->getColPosName(
                            $container->getCType(),
                            $colPos
                        )
                    ),
                    'colPos' => $colPos
                ],
                'contentElements' => $contentElements
            ];
        }

        if (empty($sourceColPos) === false) {
            $items = $items[0];
        }

        $processedData[$targetVariableName] = $items;
        $processorConfiguration['as'] = $targetVariableName;
        return $this->removeDataIfnotAppendInConfiguration($processorConfiguration, $processedData);
    }

    protected function getLanguageService(ServerRequestInterface $request): LanguageService
    {
        return $this->languageServiceFactory->createFromSiteLanguage(
            $request->getAttribute('language')
            ?? $request->getAttribute('site')->getDefaultLanguage()
        );
    }
}
