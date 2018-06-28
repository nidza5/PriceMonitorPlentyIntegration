<?php

 require_once __DIR__ . '/PriceMonitorHttpClient.php';
 require_once __DIR__ . '/FilterStorage.php';
 require_once __DIR__ . '/ProductService.php';
 require_once __DIR__ . '/TransactionStorage.php';
 require_once __DIR__ . '/ConfigService.php';
 require_once __DIR__ . '/MapperService.php';

 
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
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistory;
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryMasterFilter;
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistorySortFields;
 use Patagona\Pricemonitor\Core\Sync\TransactionHistory\TransactionHistoryMaster;
 use Patagona\Pricemonitor\Core\Sync\Filter\Condition;

 class PriceMonitorSdkHelper
 {
    /**
     * @var array
     */
    protected static $_statusMap = array(
        null => '',
        TransactionHistoryStatus::FAILED => 'Failed',
        TransactionHistoryStatus::FINISHED => 'Success',
        TransactionHistoryStatus::IN_PROGRESS => 'In Progress',
        TransactionHistoryStatus::FILTERED_OUT => 'Filtered Out',
    );

    public static $conditionMap = array(
        Condition::EQUAL => '=',
        Condition::NOT_EQUAL => '!=',
        Condition::GREATER_THAN => '>',
        Condition::LESS_THAN => '<',
        Condition::GREATER_OR_EQUAL => '>=',
        Condition::LESS_OR_EQUAL => '<='
    );

    protected static $_columnNames = array(
        "VariationN" => "name",
        "VariationNo" => 'number'
    );

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

    public static function registerConfigService($email,$password,$configService)
    {
       // ServiceRegister::registerConfigService(new ConfigService($email,$password));
       ServiceRegister::registerConfigService($configService);
    }

    public static function registerHttpService()
    {
        $client = new PriceMonitorHttpClient();
        ServiceRegister::registerHttpClient($client);
    }

    public static function registerMapperService($attributesMappings,$contract,$productsForExport,$productAttributes) 
    {
        ServiceRegister::registerMapperService(new MapperServices($attributesMappings,$contract,$productsForExport,$productAttributes));
    }

    public static function registerProductService($contract,$productForExport) 
    {
        ServiceRegister::registerProductService(new ProductService($contract,$productForExport));
    }

    public static function registerTransactionHistotyStorage($transactionHistoryDetailsRecord = null,$totalDetailedRecords = 0,$transactionHistoryRecords = null,$totalHistoryRecords = 0,$savedTransactionHistory = null)
    {
        ServiceRegister::registerTransactionHistoryStorage(new TransactionStorage($transactionHistoryDetailsRecord,$totalDetailedRecords,$transactionHistoryRecords,$totalHistoryRecords,$savedTransactionHistory));
    }

    public static function saveFilter($filterData, $filterType, $pricemonitorId,$productFilterRepo,$emailForConfig,$passwordForConfig)
    {

        try {
            // require_once __DIR__ . '/ConfigService.php';
            // ServiceRegister::registerConfigService(new ConfigService($emailForConfig,$passwordForConfig));

            $client = new PriceMonitorHttpClient();
            ServiceRegister::registerHttpClient($client);

           ServiceRegister::registerFilterStorage(new FilterStorage($productFilterRepo));
           $filter = self::getPopulatedFilter($filterData, $filterType);

            $filterRepository = new FilterRepository();
            $filterResult = $filterRepository->saveFilter($pricemonitorId, $filter);
           
            $returnedArray = [
                "contractId" => $pricemonitorId,
                "filterType" => $filterType,
                 "filter" => ['serializedFilter' => $filterResult ]
            ];

            return $returnedArray;

        } catch(\Exception $ex) 
        {
            $response = [
                'Code' => $ex->getCode(),
                'Message' => $ex->getMessage()
             ];

             return $response;
        }       
    }

    public static function getMappedAttributeCodes($attributeMapping)
    {
        $mappings = $attributeMapping;
        $mappingCodes = array_unique(array_column($mappings, 'attributeCode'));
        return $mappingCodes;
    }

    public static function getFilteredVariations($filterType, $pricemonitorId, $filterRepo,$attributeMapping, $allVariations,$attributesFromPlenty)
    {
         //  $mappedAttribute = self::getMappedAttributeCodes($attributeMapping);

            ServiceRegister::registerFilterStorage(new FilterStorage($filterRepo));

            $filterRepository = new FilterRepository();
            $filter = $filterRepository->getFilter($pricemonitorId, $filterType);

            $finalProductCollection = null;
            $parentGroup = [];
            $groupFilter = [];

            foreach ($filter->getExpressions() as $group) {
                $operator = null;
                $expressions = array();

                foreach ($group->getExpressions() as $expression) {
                    $condition = $expression->getCondition();

                    $field = $expression->getField();
                    $values = $expression->getValues();
                    $operator = $expression->getOperator();

                    $expressions[] = array(
                        'attribute' => $field,
                        'values' => $values,
                        'condition' => $condition,
                        'operator' => $operator
                    );
                }

                $groupFilter['expressions'] =  $expressions;
                $groupFilter['operator'] =  $group->getOperator();

                array_push($parentGroup,$groupFilter);
              

                // if($group->getOperator() == 'AND')
                //     $finalProductCollection = $productCollection;
                // else if($group->getOperator() == 'OR')            
                //     array_push($finalProductCollection,$productCollection);
       }
       
        if (!empty($parentGroup)) {
            $productCollection = self::addFilterByOperator($parentGroup, $group->getOperator(),$allVariations,$attributesFromPlenty);
        }

         return $productCollection;
    }

    public static function addFilterByOperator($parentGroup,$groupOperator,$variationArray,$attributesFromPlenty) 
    {
        try {
            
            $finalFilteredProduct = array();
          
            $parentFilteredGroup = [];
            $filteredGroup = [];

            foreach($parentGroup as $group)
            {
               $filterVAriationByConditions = [];

                foreach($group["expressions"] as $exp) {
                    $operator = $exp['operator'];
                    $values = $exp['values'];
                    $attribute = $exp['attribute'];
                    $condition = $exp['condition'];
    
                    $filterByColumn = $attributesFromPlenty[$attribute]; 
    
                    switch($attribute) {
    
                        case "Category" :
                            $filterByColumn = "category-" + $values[0];
                        break;
                        case "Manufacturer" :
                             $filterByColumn = "manufacturer-" + $values[0];
                        break;
    
                        default :
                            $filterByColumn = $attributesFromPlenty[$attribute]; 
                    }
        
                    $nameColumnInVariation = null;
    
                    switch($condition) {
    
                        case "equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "=",
                                                                "operator" =>  $operator];
                        break;
                        case "not_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "!=",
                                                                "operator" =>  $operator];
                        break;
                        case "greater_than" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => ">",
                                                                "operator" =>  $operator];
                        break;
                        case "less_than" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "<",
                                                                "operator" =>  $operator];
                        break;
                        case "greater_or_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => ">=",
                                                                "operator" =>  $operator];
                        break;
                        case "less_or_equal" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "<=",
                                                                "operator" =>  $operator];
                        break;
                        case "contains" :
                            $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                                "value" => $values[0],
                                                                "condition" => "stripos!=",
                                                                "operator" =>  $operator];
                        break;
                        case "contains_not" :
                        $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
                                                            "value" => $values[0],
                                                            "condition" => "stripos==",
                                                            "operator" =>  $operator];
                        break;
                    }
                }

                $filteredGroup['expressionFilter'] =  $filterVAriationByConditions;
                array_push($parentFilteredGroup,$filteredGroup);   
            } 

            $filteredProducts = array_filter($variationArray, function($value) use ($filterVAriationByConditions, $parentFilteredGroup) {

                foreach($parentFilteredGroup as $filterGroup) 
                {
                    $condition = null;
                    foreach($filterGroup["expressionFilter"] as $variationCondition) {

                        $filterByCondition = $variationCondition["condition"];
                        
                        switch($filterByCondition) {
    
                            case "=" :
                             if(isset($value[$variationCondition["filterByColumn"]])) 
                                if($condition) {
                                    if($variationCondition["operator"] == "AND")
                                        $condition = $condition && $value[$variationCondition["filterByColumn"]] == $variationCondition["value"];
                                    else if($variationCondition["operator"] == "OR")
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] == $variationCondition["value"];
                                } else
                                    $condition = $value[$variationCondition["filterByColumn"]] == $variationCondition["value"];
                             break;
                            case "!=" :
                              if($condition) {
                                 if($variationCondition["operator"] == "AND")
                                    $condition = $condition && $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                                 else if($variationCondition["operator"] == "OR")
                                    $condition = $condition || $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                             } else
                                   $condition = $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
                             break;
                            case ">" :
                               if($condition) {
                                 if($variationCondition["operator"] == "AND")
                                    $condition = $condition && $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                                 else if($variationCondition["operator"] == "OR")
                                     $condition = $condition || $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                                } else
                                    $condition =  $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
                            break;
                            case "<" :
                              if($condition) {
                                  if($variationCondition["operator"] == "AND")
                                     $condition = $condition && $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                                  else if($variationCondition["operator"] == "OR")
                                     $condition = $condition || $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                               } else
                                     $condition = $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
                                break;
                            case ">=" :
                                if($condition) {
                                    if($variationCondition["operator"] == "AND")
                                        $condition = $condition && $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                   else if($variationCondition["operator"] == "OR")
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                } else 
                                    $condition = $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
                                break;
                            case "<=" :
                                if($condition) {
                                    if($variationCondition["operator"] == "AND")
                                        $condition = $condition && $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                    else if($variationCondition["operator"] == "OR")
                                        $condition = $condition || $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                  } else
                                    $condition = $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
                                break;
                            case "stripos!=" :
                                if($condition) {                                
                                    if($variationCondition["operator"] == "AND")
                                        $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                    else if($variationCondition["operator"] == "OR")
                                        $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                } else
                                    $condition = (stripos($value[$variationCondition["filterByColumn"]]) !== false);
                                 break;
                            case "stripos==" :
                                if($condition) {
                                    if($variationCondition["operator"] == "AND")
                                        $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                     else if($variationCondition["operator"] == "OR")
                                        $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                } else
                                    $condition =  (stripos($value[$variationCondition["filterByColumn"]]) === false);
                                break;
                        }
                    }
                }                

                return $condition;                    
            });
    
             return  $filteredProducts;

        } catch (\Exception $ex)
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

    public static function getTransHistoryDetails($pricemonitorId, $masterId, $limit, $offset,$transactionHistoryDetailsRecord,$totalDetailedRecords,$transactionHistoryRecords, $totalHistoryRecords)
    {
        ServiceRegister::registerTransactionHistoryStorage(new TransactionStorage($transactionHistoryDetailsRecord,$totalDetailedRecords,$transactionHistoryRecords,$totalHistoryRecords,null));
       
        $transactionHistory = new TransactionHistory();

        $type = TransactionHistoryType::EXPORT_PRODUCTS;

        $detailed = $masterId !== null;

        if ($detailed) {
            $records = $transactionHistory->getTransactionHistoryDetails($pricemonitorId, $masterId, (int)$limit, (int)$offset);
            $total = $transactionHistory->getTransactionHistoryDetailsCount($pricemonitorId, $masterId);
        } else {
            $records = $transactionHistory->getTransactionHistoryMaster($pricemonitorId, $type, (int)$limit, (int)$offset);
            $total = $transactionHistory->getTransactionHistoryMasterCount($pricemonitorId, $type);
        }
        
        $records = self::transform($records, $type, $detailed);

        $finalHistoryDetails = ['records' => $records,
                                'total' => $total];

        return $finalHistoryDetails;
    }

    public static function transform($data, $type = TransactionHistoryType::EXPORT_PRODUCTS, $detailed = false)
    {
        if ($type === TransactionHistoryType::EXPORT_PRODUCTS) {
            return self::transformExport($data, $detailed);
        } else {
            return self::transformImport($data, $detailed);
        }
    }

    /**
     * @param array $data
     * @param bool $detailed
     *
     * @return array
     */
    public static function transformImport($data, $detailed = false)
    {
        if ($detailed) {
            $result = self::transformDetailImportData($data);
        } else {
            $result = self::transformMasterImportData($data);
        }

        return $result;
    }

     /**
     * @param array $data
     * @param bool $detailed
     *
     * @return array
     */
    public static function transformExport($data, $detailed = false)
    {
        if ($detailed) {
            $result = self::transformDetailExportData($data);
        } else {
            $result = self::transformMasterExportData($data);
        }

        return $result;
    }

     /**
     * @param array $data
     *
     * @return array
     */
    public static function transformDetailExportData($data)
    {
        $result = array();

        /** @var TransactionHistoryDetail $record */
        foreach ($data as $record) {
            $status = self::$_statusMap[$record->getStatus()];

            $result[] = array(
                'gtin' => (string)$record->getGtin(),
                'name' => (string)$record->getProductName(),
                'refPrice' => $record->getReferencePrice(),
                'minPrice' => $record->getMinPrice(),
                'maxPrice' => $record->getMaxPrice(),
                'status' => $status,
                'note' => (string)$record->getNote(),
            );
        }

        return $result;
    }

      /**
     * @param array $data
     *
     * @return array
     */
    public static function transformMasterExportData($data)
    {
        $result = array();

        /** @var TransactionHistoryMaster $record */
        foreach ($data as $record) {
            $status = self::$_statusMap[$record->getStatus()];

            $result[] = array(
                'id' => (int)$record->getId(),
                'exportTime' => $this->formatDate($record->getTime()),
                'successCount' => (int)$record->getSuccessCount(),
                'failedCount' => (int)$record->getFailedCount(),
                'status' => $status,
                'inProgress' => $record->getStatus() === TransactionHistoryStatus::IN_PROGRESS,
                'note' => (string)$record->getNote(),
            );
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function transformDetailImportData($data)
    {
        $result = array();

        /** @var TransactionHistoryDetail $record */
        foreach ($data as $record) {
            $status = self::$_statusMap[$record->getStatus()];
            $isUpdated = $record->isUpdatedInShop() ? 'Yes' : 'No';

            $result[] = array(
                'status' => $status,
                'gtin' => (string)$record->getGtin(),
                'name' => (string)$record->getProductName(),
                'isUpdated' => $isUpdated,
                'note' => (string)$record->getNote(),
            );
        }

        return $result;
    }

     /**
     * @param $data
     *
     * @return array
     */
    public static function transformMasterImportData($data)
    {
        $result = array();

        /** @var TransactionHistoryMaster $record */
        foreach ($data as $record) {
            $status = self::$_statusMap[$record->getStatus()];

            $result[] = array(
                'id' => (int)$record->getId(),
                'importTime' => $this->formatDate($record->getTime()),
                'importedPrices' => (int)$record->getTotalCount(),
                'updatedPrices' => (int)$record->getSuccessCount(),
                'failedCount' => (int)$record->getFailedCount(),
                'inProgress' => $record->getStatus() === TransactionHistoryStatus::IN_PROGRESS,
                'status' => $status,
                'note' => (string)$record->getNote(),
            );
        }

        return $result;
    }

 }


?>