<?php


 class PriceMonitorSdkHelper
 {


    // public static function getFilteredVariations($filterType, $pricemonitorId, $filterRepo,$attributeMapping, $allVariations,$attributesFromPlenty)
    // {
    //      //  $mappedAttribute = self::getMappedAttributeCodes($attributeMapping);

    //         ServiceRegister::registerFilterStorage(new FilterStorage($filterRepo));

    //         $filterRepository = new FilterRepository();
    //         $filter = $filterRepository->getFilter($pricemonitorId, $filterType);

    //         $finalProductCollection = null;
    //         $parentGroup = [];
    //         $groupFilter = [];

    //         foreach ($filter->getExpressions() as $group) {
    //             $operator = null;
    //             $expressions = array();

    //             foreach ($group->getExpressions() as $expression) {
    //                 $condition = $expression->getCondition();

    //                 $field = $expression->getField();
    //                 $values = $expression->getValues();
    //                 $operator = $expression->getOperator();

    //                 $expressions[] = array(
    //                     'attribute' => $field,
    //                     'values' => $values,
    //                     'condition' => $condition,
    //                     'operator' => $operator
    //                 );
    //             }

    //             $groupFilter['expressions'] =  $expressions;
    //             $groupFilter['operator'] =  $group->getOperator();

    //             array_push($parentGroup,$groupFilter);
              

    //             // if($group->getOperator() == 'AND')
    //             //     $finalProductCollection = $productCollection;
    //             // else if($group->getOperator() == 'OR')            
    //             //     array_push($finalProductCollection,$productCollection);
    //    }
       
    //     if (!empty($parentGroup)) {
    //         $productCollection = self::addFilterByOperator($parentGroup, $group->getOperator(),$allVariations,$attributesFromPlenty);
    //     }

    //      return $productCollection;
    // }

    // public static function addFilterByOperator($parentGroup,$groupOperator,$variationArray,$attributesFromPlenty) 
    // {
    //     try {
            
    //         $finalFilteredProduct = array();
          
    //         $parentFilteredGroup = [];
    //         $filteredGroup = [];

    //         foreach($parentGroup as $group)
    //         {
    //            $filterVAriationByConditions = [];

    //             foreach($group["expressions"] as $exp) {
    //                 $operator = $exp['operator'];
    //                 $values = $exp['values'];
    //                 $attribute = $exp['attribute'];
    //                 $condition = $exp['condition'];
    
    //                 $filterByColumn = $attributesFromPlenty[$attribute]; 
    
    //                 switch($attribute) {
    
    //                     case "Category" :
    //                         $filterByColumn = "category-".$values[0];
    //                     break;
    //                     case "Manufacturer" :
    //                          $filterByColumn = "manufacturer-".$values[0];
    //                     break;
    //                     case "Supplier" :
    //                           $filterByColumn = "supplier-".$values[0];
    //                     break;
    
    //                     default :
    //                         $filterByColumn = $attributesFromPlenty[$attribute]; 
    //                 }
        
    //                 $nameColumnInVariation = null;
    
    //                 switch($condition) {
    
    //                     case "equal" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => "=",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "not_equal" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => "!=",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "greater_than" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => ">",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "less_than" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => "<",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "greater_or_equal" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => ">=",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "less_or_equal" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => "<=",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "contains" :
    //                         $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                             "value" => $values[0],
    //                                                             "condition" => "stripos!=",
    //                                                             "operator" =>  $operator];
    //                     break;
    //                     case "contains_not" :
    //                     $filterVAriationByConditions [] =  ["filterByColumn" => $filterByColumn,
    //                                                         "value" => $values[0],
    //                                                         "condition" => "stripos==",
    //                                                         "operator" =>  $operator];
    //                     break;
    //                 }
    //             }

    //             $filteredGroup['expressionFilter'] =  $filterVAriationByConditions;
    //             $filteredGroup['operator'] = $group["operator"];
    //             array_push($parentFilteredGroup,$filteredGroup);   
    //         } 

    //         $filteredProducts = array_filter($variationArray, function($value) use ($filterVAriationByConditions, $parentFilteredGroup) {

    //             $groupCondition = null;
    //             foreach($parentFilteredGroup as $filterGroup) 
    //             {
    //                 $condition = null;
    //                 foreach($filterGroup["expressionFilter"] as $variationCondition) {

    //                     $filterByCondition = $variationCondition["condition"];
                        
    //                     switch($filterByCondition) {
    
    //                         case "=" :                               
    //                                 if($condition !== null) {
    //                                     if($variationCondition["operator"] === "AND")
    //                                     {
    //                                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                                             $condition = $condition && ($value[$variationCondition["filterByColumn"]] === $variationCondition["value"]);
    //                                         else 
    //                                             $condition = $condition &&  false;
    //                                     }
    //                                     else if($variationCondition["operator"] === "OR")
    //                                     {
    //                                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                                             $condition = $condition || $value[$variationCondition["filterByColumn"]] === $variationCondition["value"];
    //                                         else 
    //                                             $condition = $condition ||  false;
    //                                     }
    //                                 } else {
    //                                     if(isset($value[$variationCondition["filterByColumn"]])) 
    //                                         $condition = ($value[$variationCondition["filterByColumn"]] === $variationCondition["value"]);
    //                                     else 
    //                                         $condition = false;   
    //                                 }
                                 
    //                            break;
    //                         case "!=" :
    //                           if(isset($value[$variationCondition["filterByColumn"]])) 
    //                           {
    //                                 if($condition !== null) {
    //                                     if($variationCondition["operator"] == "AND")
    //                                         $condition = $condition && $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
    //                                     else if($variationCondition["operator"] == "OR")
    //                                         $condition = $condition || $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
    //                                 } else
    //                                     $condition = $value[$variationCondition["filterByColumn"]] != $variationCondition["value"];
    //                             }
    //                         break;
    //                         case ">" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                            if($condition !== null) {
    //                              if($variationCondition["operator"] == "AND")
    //                                 $condition = $condition && $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
    //                              else if($variationCondition["operator"] == "OR")
    //                                  $condition = $condition || $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
    //                             } else
    //                                 $condition =  $value[$variationCondition["filterByColumn"]] > $variationCondition["value"];
    //                         }
    //                         break;
    //                         case "<" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                           if($condition !== null) {
    //                               if($variationCondition["operator"] == "AND")
    //                                  $condition = $condition && $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
    //                               else if($variationCondition["operator"] == "OR")
    //                                  $condition = $condition || $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
    //                            } else
    //                                  $condition = $value[$variationCondition["filterByColumn"]] < $variationCondition["value"];
    //                          }
    //                           break;
    //                         case ">=" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                             if($condition !== null) {
    //                                 if($variationCondition["operator"] == "AND")
    //                                     $condition = $condition && $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
    //                                else if($variationCondition["operator"] == "OR")
    //                                     $condition = $condition || $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
    //                             } else 
    //                                 $condition = $value[$variationCondition["filterByColumn"]] >= $variationCondition["value"];
    //                         }
    //                         break;
    //                         case "<=" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                             if($condition !== null) {
    //                                 if($variationCondition["operator"] == "AND")
    //                                     $condition = $condition && $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
    //                                 else if($variationCondition["operator"] == "OR")
    //                                     $condition = $condition || $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
    //                               } else
    //                                 $condition = $value[$variationCondition["filterByColumn"]] <= $variationCondition["value"];
    //                         }
    //                          break;
    //                         case "stripos!=" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                             if($condition !== null) {                                
    //                                 if($variationCondition["operator"] == "AND")
    //                                     $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) !== false);
    //                                 else if($variationCondition["operator"] == "OR")
    //                                     $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) !== false);
    //                             } else
    //                                 $condition = (stripos($value[$variationCondition["filterByColumn"]]) !== false);
    //                          }
    //                           break;
    //                         case "stripos==" :
    //                         if(isset($value[$variationCondition["filterByColumn"]])) 
    //                         {
    //                             if($condition !== null) {
    //                                 if($variationCondition["operator"] == "AND")
    //                                     $condition =  $condition && (stripos($value[$variationCondition["filterByColumn"]]) === false);
    //                                  else if($variationCondition["operator"] == "OR")
    //                                     $condition =  $condition || (stripos($value[$variationCondition["filterByColumn"]]) === false);
    //                             } else
    //                                 $condition =  (stripos($value[$variationCondition["filterByColumn"]]) === false);
    //                         }
    //                         break;
    //                     }
    //                 }
                   
    //                 $operatorGroup = $filterGroup["operator"];
    //                 if($operatorGroup == "AND")
    //                     $groupCondition = $groupCondition === null ? ($condition) : $groupCondition && ($condition);
    //                 else if($operatorGroup == "OR")
    //                     $groupCondition = $groupCondition === null ? ($condition) : $groupCondition || ($condition);

    //             }                

    //             return $groupCondition;                    
    //         });
    
    //          return  array_values($filteredProducts);

    //     } catch (\Exception $ex)
    //     {
    //         $response = [
    //             'Code' => $ex->getCode(),
    //             'Message' => $ex->getMessage()
    //          ];

    //          return $response;

    //     }      
    // } 

 }


?>