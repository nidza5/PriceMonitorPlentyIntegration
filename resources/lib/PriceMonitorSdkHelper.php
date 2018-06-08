<?php

 require_once __DIR__ . '/PriceMonitorHttpClient.php';
 require_once __DIR__ . '/FilterStorage.php';
//  require_once $_SERVER['DOCUMENT_ROOT'] . '/PriceMonitorPlentyIntegration/src/Repositories/ProductFilterRepository.php';


 use Patagona\Pricemonitor\Core\Infrastructure\ServiceRegister;
 use Patagona\Pricemonitor\Core\Infrastructure\Proxy;
 use Patagona\Pricemonitor\Core\Infrastructure\Logger;
 use Patagona\Pricemonitor\Core\Sync\Filter\Filter;
 use Patagona\Pricemonitor\Core\Sync\Filter\FilterRepository;
 use Patagona\Pricemonitor\Core\Sync\Filter\Group;
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryType;
 use Patagona\Pricemonitor\Core\Sync\Filter\Expression;
 use PriceMonitorPlentyIntegration\Contracts\ProductFilterRepositoryContract;
 use PriceMonitorPlentyIntegration\Repositories\ProductFilterRepository;

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


    public static function saveFilter($filterData, $filterType, $pricemonitorId,$productFilterRepo)
    {

        try {
            require_once __DIR__ . '/ConfigService.php';
            ServiceRegister::registerConfigService(new ConfigService());

            $client = new PriceMonitorHttpClient();
            ServiceRegister::registerHttpClient($client);

           ServiceRegister::registerFilterStorage(new FilterStorage($productFilterRepo));
           $filter = self::getPopulatedFilter($filterData, $filterType);

            $filterRepository = new FilterRepository();
            $filterResult = $filterRepository->saveFilter($pricemonitorId, $filter);
            
            return $filter;

        } catch(\Exception $ex) 
        {
            $response = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
             ];

             return $response;
        }       
    }

    public static function getFilter($filterType, $pricemonitorId, $filterRepo)
    {
        try {

            ServiceRegister::registerFilterStorage(new FilterStorage($filterRepo));

            $result = array('type' => $filterType, 'filters' => array());
            $filterRepository = new FilterRepository();
            $filter = $filterRepository->getFilter($pricemonitorId, $filterType);

            if ($filter === null) {
                return $result;
            }

            /** @var Group $group */
            foreach ($filter->getExpressions() as $group) {
                $current = array(
                    'name' => $group->getName(),
                    'groupOperator' => $group->getOperator(),
                    'expressions' => array()
                );

                /** @var Expression $expression */
                foreach ($group->getExpressions() as $expression) {
                    $current['operator'] = $expression->getOperator();
                    $current['expressions'][] = array(
                        'code' => $expression->getField(),
                        'condition' => $expression->getCondition(),
                        'type' => $expression->getValueType(),
                        'value' => $expression->getValues(),
                    );
                }

                $result['filters'][] = $current;
            }

        return $result;

        } catch(\Exception $ex)
        {
            return $ex->getMessage();
        }        
    }

    /**
     * Populates filter object with provided filter data and filter type.
     *
     * @param array $filterData
     * @param string $filterType
     *
     * @return Filter
     */
    public static function getPopulatedFilter($filterData, $filterType)
    {

        try {

            $filterGroups = array();
            foreach ($filterData as $key => $filterGroup) {
                if (empty($filterGroup['expressions'])) {
                    continue;
                }

                echo "u foreach-u";

                $name = isset($filterGroup['name']) ? $filterGroup['name'] : ('Group ' . (++$key));
                $group = new Group($name, $filterGroup['groupOperator']);

                $expressions = array();
                foreach ($filterGroup['expressions'] as $expression) {
                    $expressions[] = new Expression(
                        $expression['code'],
                        $expression['condition'],
                        $expression['type'],
                        $expression['value'],
                        $filterGroup['operator']
                    );
                }

                $group->setExpressions($expressions);
                $filterGroups[] = $group;
            }

            $filter = new Filter('Filter', $filterType);
            $filter->setExpressions($filterGroups);
            return $filter;
            
        } catch(\Exception $ex) 
        {    
            $response = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
             ];

             return $response;
        }        
    }
 }

?>