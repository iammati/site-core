<?php

declare(strict_types=1);

namespace Site\Core\Service;

use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserService
{
    /**
     * @var BackendUserRepository
     */
    protected $backendUserRepository;

    /**
     * Fetch the current logged-in Backend-User by reading the cookie which
     * represents the logged-in BE-User's session-id and via queries it fetches
     * the uid of that one and additionally a findByUid using the BackendUserRepository.
     *
     * @return null|BackendUser
     */
    public function getUser()
    {
        $backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
        $cookieSesId = $_COOKIE['be_typo_user'];

        if (!$cookieSesId) {
            return null;
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_sessions');
        $res = $queryBuilder
            ->select('ses_userid')
            ->from('be_sessions')
            ->where(
                $queryBuilder->expr()->eq('ses_id', $queryBuilder->createNamedParameter($cookieSesId))
            )
            ->execute()
            ->fetch(0)
        ;

        if (!$res) {
            return null;
        }

        $sesUserId = $res['ses_userid'];

        $backendUser = $backendUserRepository->findByUid($sesUserId);

        $backendUser->uc = $this->findUcByUid($sesUserId);

        return $backendUser;
    }

    /**
     * @return null|array
     */
    protected function findUcByUid(int $uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $res = $queryBuilder
            ->select('uc')
            ->from('be_users')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->execute()
            ->fetch(0)
        ;

        if (!$res) {
            return null;
        }

        $uc = unserialize($res['uc']);

        if (!$uc) {
            return [];
        }

        return $uc;
    }
}
