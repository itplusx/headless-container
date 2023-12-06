<?php

namespace ITplusX\HeadlessContainer\UserFunctions;

/*
 * This file is part of the "headless_container" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class GridRecords
{

    /**
     * List all grid colPos values on the current page
     *
     * @param string $content Empty string (no content to process)
     * @param array $conf TypoScript configuration
     * @return string Comma-separated list of colPos values associated to a grid column on the current page
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getColPosList(string $content, array $conf): string
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $result = $queryBuilder
            ->select('colPos', 'tx_container_parent')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($GLOBALS['TSFE']->contentPid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->gt(
                    'tx_container_parent',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
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
