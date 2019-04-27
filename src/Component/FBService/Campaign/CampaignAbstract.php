<?php

namespace App\Component\FBService\Campaign;

use App\Entity\CampaignAction;
use FbTraffic\FbSDK\FbSDK;
use FbTraffic\FbSDK\Resources\Objects\AccountInterface;

class CampaignAbstract
{
    /**
     * AccountInterface $account
    */
    protected $account;

    /**
     * @var FbSDK $fbSDK;
    */
    protected $fbSDK;

    /**
     * @return $this
     * @throws \Exception
    */
    public function factory($type)
    {
        $class = __NAMESPACE__."\\".$type;
        if(!class_exists($class)) {
            throw new \Exception("Could not find class: $class");
        }
        return new $class();
    }

    public function isValidAction(CampaignAction $action)
    {
        return true;
    }

    public function useAccount(AccountInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    public function execute(CampaignAction $action)
    {
        if(!method_exists($this, $action->getMethodName())) {
            throw new \Exception("Could not find method: {$action->getMethodName()}");
        }

        if(!$this->isValidAction($action)) {
            throw new \Exception("Action is invalid! ActionID: {$action->getId()} with AccountID: {$this->account->getAccountId()}");
        }

        call_user_func(array($this, $action->getMethodName()));
    }

    /**
     * @return FbSDK
     */
    public function getFbSDK()
    {
        return $this->fbSDK;
    }

    /**
     * @param FbSDK $fbSDK
     */
    public function setFbSDK(FbSDK $fbSDK)
    {
        $this->fbSDK = $fbSDK;
    }
}