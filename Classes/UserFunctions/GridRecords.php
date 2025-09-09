<?php

namespace ITplusX\HeadlessContainer\UserFunctions;

/*
 * This file is part of the "headless_container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class GridRecords
{

    /**
     * List all grid colPos values on the current page
     *
     * @param string $content Empty string (no content to process)
     * @param array $conf TypoScript configuration
     * @param ServerRequestInterface $request
     * @return string Comma-separated list of colPos values associated to a grid column on the current page
     * @throws Exception
     */
    public function getColPosList(string $content, array $conf, ServerRequestInterface $request): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $currentContentObject = $request->getAttribute('currentContentObject');

        if ($currentContentObject->data['content_from_pid']) {
            $pid = $currentContentObject->data['content_from_pid'];
        } else {
            $pid = $currentContentObject->data['uid'];
        }

        $result = $queryBuilder
            ->select('colPos','pid', 'tx_container_parent')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->gt(
                    'tx_container_parent',
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                )
            )
            ->executeQuery();

        $colPosList = [];
        while ($row = $result->fetchAssociative()) {
            $colPosList[] = $row['colPos'];
        }

        return implode(',', array_unique($colPosList));
    }
}
