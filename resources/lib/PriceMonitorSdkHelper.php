<?php
 
 require_once __DIR__ . '/PriceMonitorHttpClient.php';
 require_once __DIR__ . '/FilterStorage.php';

 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;
 use Patagona\Pricemonitor\Core\Sync\Filter\Filter;
 use Patagona\Pricemonitor\Core\Sync\Filter\FilterRepository;
 use Patagona\Pricemonitor\Core\Sync\Filter\Group;
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryType;
 use Patagona\Pricemonitor\Core\Sync\Filter\Expression;

 class PriceMonitorSdkHelper
 {
    public static function loginInPriceMonitor($email,$password)
   { 
        try {
            new ServiceRegister();

            $client = new PriceMonitorHttpClient();
            ServiceRegister::registerHttpClient($client);

            $proxy = Proxy::createFor($email,$password);      
            $contracts = $proxy->getContracts();
            
            return $contracts;

        } catch(\Exception $ex)
        {

            $response = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
             ];

            return $response;
        }
    }

    public static function setUpCredentials($email,$password)
    {
        ServiceRegister::getConfigService()->setCredentials($email, $password);
    }

    // public static function getFilter($filterType, $pricemonitorId)
    // {
    //     ServiceRegister::registerFilterStorage(new FilterStorage());

    //     $result = array('type' => $filterType, 'filters' => array());
    //     $filterRepository = new FilterRepository();
    //     $filter = $filterRepository->getFilter($pricemonitorId, $filterType);

    //     if ($filter === null) {
    //         return $result;
    //     }

    //     /** @var Group $group */
    //     foreach ($filter->getExpressions() as $group) {
    //         $current = array(
    //             'name' => $group->getName(),
    //             'groupOperator' => $group->getOperator(),
    //             'expressions' => array()
    //         );

    //         /** @var Expression $expression */
    //         foreach ($group->getExpressions() as $expression) {
    //             $current['operator'] = $expression->getOperator();
    //             $current['expressions'][] = array(
    //                 'code' => $expression->getField(),
    //                 'condition' => $expression->getCondition(),
    //                 'type' => $expression->getValueType(),
    //                 'value' => $expression->getValues(),
    //             );
    //         }

    //         $result['filters'][] = $current;
    //     }

    //     return $result;
    // }
 }

?>