<?php

declare(strict_types=1);

namespace ITplusX\HeadlessContainer\DataProcessing;

/*
 * This file is part of the "headless_container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use B13\Container\Domain\Factory\Exception;
use B13\Container\Domain\Factory\PageView\Frontend\ContainerFactory;
use B13\Container\Domain\Model\Container;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use FriendsOfTYPO3\Headless\DataProcessing\DataProcessingTrait;
use TYPO3\CMS\Frontend\ContentObject\RecordsContentObject;

class ContainerProcessor extends \B13\Container\DataProcessing\ContainerProcessor
{
    use DataProcessingTrait;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(ContainerFactory $containerFactory, ContentDataProcessor $contentDataProcessor)
    {
        parent::__construct($containerFactory, $contentDataProcessor);

        $this->registry = GeneralUtility::makeInstance(\B13\Container\Tca\Registry::class);
    }


    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }
        if ($processorConfiguration['contentId.'] ?? false) {
            $contentId = (int)$cObj->stdWrap($processorConfiguration['contentId'], $processorConfiguration['contentId.']);
        } elseif ($processorConfiguration['contentId'] ?? false) {
            $contentId = (int)$processorConfiguration['contentId'];
        } else {
            $contentId = (int)$cObj->data['uid'];
        }

        try {
            $container = $this->containerFactory->buildContainer($contentId);
        } catch (Exception $e) {
            // do nothing
            return $processedData;
        }

        $gridItems = [];
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'gridItems');
        $processorConfiguration['as'] = $targetVariableName;
        if (empty($processorConfiguration['colPos']) && empty($processorConfiguration['colPos.'])) {
            $allColPos = $container->getChildrenColPos();
            foreach ($allColPos as $colPos) {
                $gridItems[] = $this->processColPos(
                    $cObj,
                    $container,
                    $colPos,
                    $targetVariableName,
                    $processedData,
                    $processorConfiguration
                );
            }

            $processedData[$targetVariableName] = $gridItems;

        } else {
            if ($processorConfiguration['colPos.'] ?? null) {
                $colPos = (int)$cObj->stdWrap($processorConfiguration['colPos'], $processorConfiguration['colPos.']);
            } else {
                $colPos = (int)$processorConfiguration['colPos'];
            }

            $gridItems = $this->processColPos(
                $cObj,
                $container,
                $colPos,
                $targetVariableName,
                $processedData,
                $processorConfiguration
            );

            $processedData[$targetVariableName] = $gridItems;
        }

        return $this->removeDataIfnotAppendInConfiguration($processorConfiguration, $processedData);
    }

    protected function processColPos(
        ContentObjectRenderer $cObj,
        Container $container,
        int $colPos,
        string $as,
        array $processedData,
        array $processorConfiguration
    ): array {
        $children = $container->getChildrenByColPos($colPos);

        $contentRecordRenderer = new RecordsContentObject($cObj);
        $conf = [
            'tables' => 'tt_content',
        ];

        $childElements = [];
        foreach ($children as &$child) {
            if ($child['l18n_parent'] > 0) {
                $conf['source'] = $child['l18n_parent'];
            } else {
                $conf['source'] = $child['uid'];
            }
            if ($child['t3ver_oid'] > 0) {
                $conf['source'] = $child['t3ver_oid'];
            }
            $child['renderedContent'] = $cObj->render($contentRecordRenderer, $conf);
            /** @var ContentObjectRenderer $recordContentObjectRenderer */
            $recordContentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $recordContentObjectRenderer->start($child, 'tt_content');
            $child = $this->contentDataProcessor->process($recordContentObjectRenderer, $processorConfiguration, $child);

            $childElements[] = \json_decode($child['renderedContent'], true);
        }

        return [
            'config' => [
                'name' => $this->getLanguageService()->sL($this->registry->getColPosName($container->getCType(), $colPos)),
                'colPos' => $colPos
            ],
            'children' => $childElements,
        ];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
