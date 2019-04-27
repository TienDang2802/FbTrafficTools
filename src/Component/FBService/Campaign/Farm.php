<?php

namespace App\Component\FBService\Campaign;


use FbTraffic\FbSDK\FbSDK;

class Farm extends CampaignAbstract
{
    const METHOD_FARM_LIKE = 'like';

    const METHOD_FARM_SHARE = 'share';

    public function like()
    {
        /** @var FbSDK $fbSDK */
        $fbSDK = new FbSDK();
        $fbSDK->account($this->account)->getNewsFeed();
        $feedBackID = 1;
        $fbSDK->account()->likePost($feedBackID);
    }

    public function share()
    {
        /** @var FbSDK $fbSDK */
        $fbSDK = new FbSDK();
        $fbSDK->account($this->account)->getNewsFeed();
        $feedBackID = 1;
        $fbSDK->account()->sharePost($feedBackID);
    }
}