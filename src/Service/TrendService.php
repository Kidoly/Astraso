<?php

namespace App\Service;

use App\Repository\HashtagpcRepository;

class TrendService
{
    private $hashtagpcRepository;

    public function __construct(HashtagpcRepository $hashtagpcRepository)
    {
        $this->hashtagpcRepository = $hashtagpcRepository;
    }

    public function getTrends()
    {
        return $this->hashtagpcRepository->findPopularHashtags();
    }
}
