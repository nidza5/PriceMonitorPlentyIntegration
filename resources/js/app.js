
   var Pricemonitor = Pricemonitor || {};
    toastr.options = {
      "closeButton": true,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }    
    
    function showTabContentContent(evt, nameTab,el) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontentprestaprice");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinksprestaprice");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(nameTab).style.display = "block";
        evt.currentTarget.className += " active";
    
        if(nameTab == "Contracts") {
            $("#tabContractInfo").click();
            $("#tabContractInfo").addClass("active");
            assignDataToContract(el);        
        } else if(nameTab == "MyAccount") {
            getAccountInformation();
        }
    }
    
    function assignDataToContract(el) {
    
        $el = $(el);
    
        var idContract = $el.attr("data-Id");
        var contractId = $el.attr("data-contractId");
        var contractName = $el.text();
        var salesPricesImportIn = $el.attr("data-salesPrice");
        var insertSalesPriceVar = $el.attr("data-insertPrices");
    
        var insertPricesValue = insertSalesPriceVar == "1" ? "true" : "false";
    
        var insertSalesPriceValue = "";
    
        if(salesPricesImportIn != null && salesPricesImportIn != "")
            insertSalesPriceValue = salesPricesImportIn.split(',');
    
        setDataContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue);
    }
    
    function setDataContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue) {
    
        $("#idContract").val(idContract);    
        $("#contractId").val(contractId);
    
        if(contractName != null && contractName != "")
            $("#contractName").val(contractName);
        
        $("#salesPriceVariationSelect").val(insertPricesValue);
        
        $("#salesPrice").val(insertSalesPriceValue).change();
    
        //set sales price value on attribute mapping tab
        adjustOptionPrices(insertSalesPriceValue,"attribute-ref-price");
        addOptionToSelectIfNotExist(insertSalesPriceValue,"attribute-ref-price");
        
        adjustOptionPrices(insertSalesPriceValue,"attribute-min-price");
        addOptionToSelectIfNotExist(insertSalesPriceValue,"attribute-min-price");
    
        adjustOptionPrices(insertSalesPriceValue,"attribute-max-price");
        addOptionToSelectIfNotExist(insertSalesPriceValue,"attribute-max-price");
    }
    
    function updateDataAttributeContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue)
    {
        var $el = $(".secondLevelButton[data-Id=" + idContract + "]");
     
        $el.attr("data-contractId",contractId);
        $el.attr("data-salesPrice",insertSalesPriceValue);
        $el.attr("data-insertPrices",insertPricesValue);
    }
    
    var allPossibileValuesSalesPrices = [];
    
    function adjustOptionPrices(insertSalesPriceValue,idSelect) {
        var selectobject=document.getElementById(idSelect);
    
        for (var i=0; i<selectobject.length; i++) {
            var salesOption = {
                Value : selectobject.options[i].value,
                Text : selectobject.options[i].text
            };
            
            allPossibileValuesSalesPrices.push(salesOption);
    
         if (!isInArray(selectobject.options[i].value.split('-')[0], insertSalesPriceValue)) {
                selectobject.remove(i);
         }       
      }
    }
    
    function addOptionToSelectIfNotExist(insertSalesPriceValue,idSelect) {
    
        var selectobject=document.getElementById(idSelect);
        for (var i=0; i<insertSalesPriceValue.length; i++) { 
            if ($("#" + idSelect).find('option[value="'+insertSalesPriceValue[i] +'-price"]').length == 0) {
    
                var optonsForAdd = allPossibileValuesSalesPrices.filter(function( obj ) {
                    return obj.Value == insertSalesPriceValue[i] + "-price" ;
                });
    
                if(optonsForAdd != null) {
                    var option = document.createElement("option");
                    option.text = optonsForAdd[0].Text;
                    option.value = optonsForAdd[0].Value;                
                    selectobject.appendChild(option);
                }            
            }
        }
    }
    
    function isInArray(value, array) {
        return array.indexOf(value) > -1;
      }
    
    $(document).ready(function() {
       
         $('.js-example-basic-multiple').select2({
            placeholder: "Select sales prices",
            allowClear: true,
            width: '100%'
         });
    
        // Get the element with id="defaultOpen" and click on it
          document.getElementById("defaultOpen").click(); 
    
          document.getElementById("tabItemSelection").addEventListener("click", getFilter);
          document.getElementById("tabPriceSelection").addEventListener("click", getFilter);
    
          $('#tableModal').DataTable();      
      });
    
    function expandCollapseMenu(el) {
    
        var $el = $(el);
        $el.css("display","block");
        $(".tablinksprestaprice").removeClass("active");
    
        $el.addClass("active");
        var liExpand = $el.next('ul');
    
        liExpand.toggle();
    }
    
    function showTabContent(evt, tabName) {
    
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        
        document.getElementById(tabName).style.display = "block";
    
        if(evt != null)
        {
            if(evt.currentTarget != null)
                evt.currentTarget.className += " active";
        }
    
        if(tabName == "ItemSelection") {
    
             template = document.getElementsByClassName('pricemonitor-filter-groups-wrapper')[0];
             parentTemplateId = "pricemonitor-product-selection";
             parentTemplate = document.getElementById(parentTemplateId);
             formName = "pricemonitorProductSelection";
             filterQueryParams['filterType'] = 'export_products';
    
        } else if(tabName == "PriceSelection") {
             
            template = document.getElementsByClassName('pricemonitor-filter-groups-wrapper-price')[0];
             parentTemplateId = "pricemonitor-price-selection";
             parentTemplate = document.getElementById(parentTemplateId);
             formName = "pricemonitorPriceSelection";
             filterQueryParams['filterType'] = 'import_prices';
            
        }
    }
    
     function updateContractInfo() {
    
            var idContract = $("#idContract").val();
            var priceMonitorId = $("#contractId").val();
            var salesPriceImportIn = $("#salesPrice").val();
            var isInsertSalesPrice = $("#salesPriceVariationSelect").val();
    
            var data = {
                'id' : idContract,
                'priceMonitorId': priceMonitorId,
                'salesPricesImport': salesPriceImportIn.join(),
                'isInsertSalesPrice' : isInsertSalesPrice
            };
    
            $.ajax({
                type: "POST",
                url: "/updateContractInfo",
                data: data,
                success: function(data)
                {
                    if(data == null) 
                       return;
                    
                    var data = jQuery.parseJSON( data );
                    if(data != null)
                    {
                      var insertPricesValue = data.isInsertSalesPrice == "1" ? "true" : "false";
    
                      if(data.salesPricesImport != null && data.salesPricesImport != "")
                         data.salesPricesImport = data.salesPricesImport.split(',');
                        
                       setDataContractInfo(data.id,data.priceMonitorId,data.name,insertPricesValue,data.salesPricesImport);
                       updateDataAttributeContractInfo(data.id,data.priceMonitorId,data.name,data.isInsertSalesPrice,data.salesPricesImport)
    
                       toastr["success"]("Data are successfully saved!", "Successfully saved!");
                    }
                    else
                    {
                        alert("ERROR");
                    }
                },
                error: function(data)
                {
                    
                }
            });
        }
    
    
        function getFilter() {
    
          var dataOption = {
              'priceMonitorId' : $("#contractId").val(),
              'filterType' :  filterQueryParams.filterType
          };
    
            $.ajax({
                type: "GET",
                url: "/getFilters" ,
                data: dataOption,
                success: function(data)
                {
                    createFiltersForm(data);    
                },
                error: function(xhr)
                {
                    
                }
            });
        }
    
        var allGroups = null;
        var dropdownInnerHtml = "";
        var addedAttributeDropdownsFieldNameValues = []; 
        var template = document.getElementsByClassName('pricemonitor-filter-groups-wrapper')[0];
        var parentTemplateId = "pricemonitor-product-selection";
        var parentTemplate = document.getElementById(parentTemplateId);
        var attributesCache = {};
        var specificSystemAttributes = {};
        var  addedDateFieldID = null;
        var formName = "pricemonitorProductSelection";
        var  filterQueryParams = {
            'pricemonitorId': $("#contractId").val(),
            'filterType': 'export_products'
        };
    
        function createFiltersForm(responseData) {
    
            var dataResponse = "";
    
            if(responseData != null)
                dataResponse = jQuery.parseJSON(responseData);
    
            if(dataResponse != null)
                 allGroups = dataResponse.filters;
    
            $.ajax({
                type: "GET",
                url: "/getAttributes",
                success: function(data)
                {
                    var dataResult = null;
                    if(data != null)
                        dataResult = jQuery.parseJSON(data);
    
                    fillFormWithData(dataResult);
                },
                error: function(xhr)
                {
                
                }
            });     
        }
    
        /**
         * Sets all options in filterable drop-downs.
         */
        function fillFormWithData(response) {
             dropdownInnerHtml = generateAllAttributesCacheAndDropdownInnerHtml(response);
             renderFiltersForm();
        }
    
        function generateAllAttributesCacheAndDropdownInnerHtml(allAttributes) {
            var dropdownInnerHtml = '<option disabled selected>Please choose values</option>';
    
            for (var k in allAttributes){
                if (allAttributes.hasOwnProperty(k)) {
    
                    dropdownInnerHtml += "<optgroup label= "+k+">";
    
                    for(var n in allAttributes[k])
                    {
                        if (allAttributes[k].hasOwnProperty(n)) {
                            var splitedAttr = allAttributes[k][n].split('-');
                            dropdownInnerHtml += " <option value="+n+" data-type="+splitedAttr[1]+" >"+ splitedAttr[0]  + "</option>";
                            attributesCache["Code-"+ n] = n;
                         }
                     }
    
                    dropdownInnerHtml += " </optgroup>";
                }
            }
            
            return dropdownInnerHtml;
        }
    
        function renderFiltersForm()
        {
            addedAttributeDropdownsFieldNameValues = [];
            template.innerHTML = generateAllGroupsFormFieldsHtml();
    
            // After the template is rendered append event handlers.
            initializeAllFilterableDropdowns();
    
           // After form is rendered append callbacks on all needed buttons.
            appendCallbacksOnAddExpressionButtons();
            appendCallbacksOnRemoveExpressionButtons();
            appendCallbacksOnAddGroupButtons();
            appendCallbacksOnRemoveGroupButtons();
    
            // var submitButton = document.getElementById(parentTemplateId + '-submit'),
            //     previewButton = document.getElementById(parentTemplateId + '-preview');
    
            // submitButton.addEventListener('click', saveFilter);
            // previewButton.addEventListener('click', preview);
        }
    
        function initializeAllFilterableDropdowns()
        {
            $(".js-example-basic-single").select2({
                width: '100%'
             });
    
             for(i = 0; i <addedAttributeDropdownsFieldNameValues.length; i++) {
                cancelLoadAttributesValues = true;
                $("#" + addedAttributeDropdownsFieldNameValues[i].name).val(addedAttributeDropdownsFieldNameValues[i].value).trigger('change');
            }
        }
    
        function generateAllGroupsFormFieldsHtml()
        {
            var groupsHtml = '';
            groupsHtml += generateAllGroupsWithFilterRows(allGroups);
    
            if (allGroups.length === 0) {
                allGroups.push({'groupOperator': 'AND', 'operator': 'OR', 'expressions': []});
                groupsHtml += generateInitialEmptyGroup();
            }
    
            groupsHtml += generateAddGroupButton();
    
            return groupsHtml;
        }
    
        function generateAddGroupButton()
        {
            return '<div class="form-row">' +
                '<button class="' + parentTemplateId + 'add-new-group-button btn btn-info" ' +
                'id="' + parentTemplateId +'addNewGroup_' + allGroups.length + '">' +
                   'Add group' +
                '</button>' +
                '</div>';
        }
    
        function generateInitialEmptyGroup()
        {
            var firstGroupIndex = 0;
    
            // If there aren't any filters groups saved, render an empty group
            return '<div class="' + parentTemplateId + '-single-group-wrapper" ' +
                'id="' + parentTemplateId +'-groupWrapper_' + 0 + '">' +
                createGroup(firstGroupIndex, [], false) +
                '</div>';
        }
    
        function generateAllGroupsWithFilterRows(allGroups)
        {
            var groupsHtml = '';
    
            for (var i = 0; i < allGroups.length; i++) {
                groupsHtml +=
                    '<div class="' + parentTemplateId + '-single-group-wrapper" ' +
                    'id="' + parentTemplateId +'-groupWrapper_'  + i + '">' +
                    createGroup(i, allGroups[i]['expressions'], true) +
                    '</div>';
            }
    
            return groupsHtml;
        }
    
        function createGroup(groupIndex, groupExpressions, groupOperatorDisabled)
        {
            return generateGroupFormRowWithoutSavedValues(groupOperatorDisabled, groupIndex) +
                generateSavedExpressionsRows(groupExpressions, groupIndex) +
                generateAddNewExpressionRow(groupIndex, groupExpressions);
        }
    
         function generateGroupFormRowWithoutSavedValues(groupOperatorDisabled, groupIndex)
            {
    
                if(groupIndex === 0) {
                    return '<div class="form-row">' +
                    '<select style="visibility:hidden;" style="margin-bottom:2%;" class="pricemonitor-form-field form-control col-sm-3" ' +
                    (groupOperatorDisabled ? 'disabled ' : '') +
                    'name="' + parentTemplateId + 'GroupOperator_' + groupIndex + '" ' +
                    ' required/>' +
                    '<option value="AND"' +
                    ((allGroups[groupIndex].groupOperator === "AND") ? " selected" : "") + '>' +
                    'AND' +
                    '</option>' +
                    '<option value="OR"' +
                    ((allGroups[groupIndex].groupOperator === "OR") ? " selected" : "") + '>' +
                    'OR' +
                    '</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="' + parentTemplateId + '-single-group" ' +
                    'id="' + parentTemplateId + 'Group_' + groupIndex + '">' +
                    '<h3>' + 'Group' + ' ' + (groupIndex + 1) +
                    '<button class="' + parentTemplateId + 'remove-group" ' +
                    'id="' + parentTemplateId + 'RemoveGroup_' + groupIndex + '">X' +
                    '</button>' +
                    '</h3>' +
                    '<div class="form-row">' +
                    '<label style="padding-right:1.5%;" for="' + parentTemplateId + 'Operator_' + groupIndex + '">' +
                    'Group type ' + '</label> ' +
                    '<select class="pricemonitor-form-field form-control" ' +
                    'name="' + parentTemplateId + 'Operator_' + groupIndex + '" ' +
                    'id="' + parentTemplateId + 'Operator_' + groupIndex + '"' +
                    ' required>' +
                    '<option value="AND" ' + (allGroups[groupIndex].operator === "AND" ? " selected" : "") + '>' +
                    'AND' +
                    '</option>' +
                    '<option value="OR" ' + (allGroups[groupIndex].operator === "OR" ? " selected" : "") + '>' +
                    'OR' +
                    '</option>' +
                    '</select>' +
                    '</div>';
                } else
                {
                    return '<div class="form-row">' +
                    '<select style="margin-bottom:2%;" class="pricemonitor-form-field form-control col-sm-3" ' +
                    (groupOperatorDisabled ? 'disabled ' : '') +
                    'name="' + parentTemplateId + 'GroupOperator_' + groupIndex + '" ' +
                    ' required/>' +
                    '<option value="AND"' +
                    ((allGroups[groupIndex].groupOperator === "AND") ? " selected" : "") + '>' +
                    'AND' +
                    '</option>' +
                    '<option value="OR"' +
                    ((allGroups[groupIndex].groupOperator === "OR") ? " selected" : "") + '>' +
                    'OR' +
                    '</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="' + parentTemplateId + '-single-group" ' +
                    'id="' + parentTemplateId + 'Group_' + groupIndex + '">' +
                    '<h3>' + 'Group' + ' ' + (groupIndex + 1) +
                    '<button class="' + parentTemplateId + 'remove-group" ' +
                    'id="' + parentTemplateId + 'RemoveGroup_' + groupIndex + '">X' +
                    '</button>' +
                    '</h3>' +
                    '<div class="form-row">' +
                    '<label style="padding-right:1.5%;" for="' + parentTemplateId + 'Operator_' + groupIndex + '">' +
                    'Group type ' + '</label> ' +
                    '<select class="pricemonitor-form-field form-control" ' +
                    'name="' + parentTemplateId + 'Operator_' + groupIndex + '" ' +
                    'id="' + parentTemplateId + 'Operator_' + groupIndex + '"' +
                    ' required>' +
                    '<option value="AND" ' + (allGroups[groupIndex].operator === "AND" ? " selected" : "") + '>' +
                    'AND' +
                    '</option>' +
                    '<option value="OR" ' + (allGroups[groupIndex].operator === "OR" ? " selected" : "") + '>' +
                    'OR' +
                    '</option>' +
                    '</select>' +
                    '</div>';
                }
    
                
            }
    
        function generateSavedExpressionsRows(groupExpressions, groupIndex)
        {
            var expressionsHtml = '';
    
            for (var j = 0; j < groupExpressions.length; j++) {
                var removeButtonId = parentTemplateId + 'RemoveExpression_' + groupIndex + '-' + j;
                expressionsHtml += '<div class="form-row">' +
                    createSavedExpressionRowHTML(groupExpressions[j], groupIndex, j, removeButtonId) +
                    '</div>';
            }
    
            return expressionsHtml;
        }
    
        function generateAddNewExpressionRow(groupIndex, groupExpressions)
        {
            var emptyExpression = {
                'code': '',
                'type': 'string',
                'value': [],
                'condition': 'equal'
            };
    
            var buttonRemoveIdd = parentTemplateId + 'RemoveExpression_' + groupIndex + '-' + groupExpressions.length;
    
            return '<div class="form-row">' +
                createFilterRow(emptyExpression, groupIndex, groupExpressions.length) +
                '<button style="height:33px;" class="' + parentTemplateId +'-add-expression  btn btn-success" ' +
                'id="' + parentTemplateId + 'AddExpression_' +  groupIndex + '-' +
                groupExpressions.length + '">' +
                '+' +
                '</button>' +
                '<button style="height:33px;margin-left:1%;" class="' + parentTemplateId + '-remove-expression  btn btn-danger " ' +
                'id="' + buttonRemoveIdd + '">' +
                'x' +
                '</button>' +            
                '</div>' +
                '</div>';
        }
    
        function createFilterRow(expression, groupIndex, expressionIndex)
        {       
            var expressionFormFieldName =
                    parentTemplateId + 'ExpressionAttrCode_' + groupIndex + '-' + expressionIndex,
                expressionFormFieldValue =
                    parentTemplateId + 'ExpressionAttrValue_' + groupIndex + '-' + expressionIndex,
                savedAttribute = attributesCache["Code-" + expression['code']];
               
                if(expression['code'] != null && expression['code'] != "")
                    addedAttributeDropdownsFieldNameValues.push(
                    {
                        'name': expressionFormFieldValue ,
                        'value': expression['code'] ? expression['code'] : ""
                    }
                );         
    
            return '<div class="filterable-dropdown-wrapper input-wrapper col-sm-3 ">' +
                        '<select ' +
                            'class="pricemonitor-filterable-dropdown js-example-basic-single pricemonitor-form-field ' + '" ' +
                            'name="' + expressionFormFieldValue + '" ' +
                            'id="' + expressionFormFieldValue + '" ' +
                            'autocomplete="off" ' +
                            'onchange="loadConditionsAndAttributeValues(this)" ' +
                            'value="' +
                            (expression['code']  ? expression['code'] : "")
                            + '" ' +
                            (savedAttribute ? "readonly disabled" : "") + ' ' +
                            'required' +
                        '/>' +  
                        dropdownInnerHtml +
                        '</select>' +
                        '<input type="hidden" ' +
                            'class="pricemonitor-form-field '  + '" ' +
                            'name="' + expressionFormFieldName + '" ' +
                            'id="' + expressionFormFieldName + '" ' +
                            'data-type="' + expression['type'] + '" ' +
                            'value="' + (expression['code'] ? expression['code'] : "") + '" ' +
                            'required' +
                        '/>' +
                        '<ul class="pricemonitor-filterable-list ' + parentTemplateId + '-all-attributes">' +
                           
                        '</ul>' +
                        '<span class="invalid-feedback"> This field is required! </span>' +
                    '</div>' +
                    '<div class="input-wrapper col-sm-3">' +
                        '<select style="height:65%;padding-top:3px;" class="pricemonitor-form-field ' + ' form-control" ' +
                            'name="' +
                             parentTemplateId + 'ExpressionCondition_' + groupIndex + '-' + expressionIndex + '"' +
                             ' required ' +
                             (savedAttribute ? ' disabled' : '') +
                         '>' +
                            createConditionOptionsForExpressionsAttributeType(expression) +
                        '</select>' +
                    '</div>' +
                    '<div class="input-wrapper col-sm-3">' +
                        createValueFieldForExpressionsAttributeType(expression, groupIndex, expressionIndex) +
                     '</div>'
        }
    
          /**
             * Creates conditions options for expression attribute type as string representing HTML.
             *
             * @param expression
             * @returns {string}
             */
            function createConditionOptionsForExpressionsAttributeType(expression)
            {
    
                var options = getOptionsForExpressionType(expression),
                    optionsHtml = '',
                    conditionTranslationCodes = getConditionCodeTranslationMap();
    
                for (var i = 0; i < options.length; i++) {
                    optionsHtml +=
                        '<option value="' + options[i] + '" ' +
                                (expression.condition === options[i] ? 'selected ' : '' ) +
                                'class="' + parentTemplateId + '-condition-option">' +
                                conditionTranslationCodes[options[i]] +
                        '</option>'
                }
    
                return optionsHtml;
            }
    
        function getOptionsForExpressionType(expression)
        {
            // if (specificSystemAttributes.hasOwnProperty(expression['code'])) {
            //     // If attribute is specific, and has expressions set in configuration then logic for getting
            //     // conditions will not be executed and conditions from configuration will be returned.
            //     return specificSystemAttributes[expression['code']]['conditions'];
            // }
    
            // By default options fo text field are loaded.
            var options = ['equal', 'not_equal', 'contains', 'contains_not'];
    
            if ((expression['type'].indexOf('dropdown') >= 0) || (expression['type'].indexOf('box') >= 0) || (expression['type'].indexOf('Category') >= 0)
            || (expression['type'].indexOf('Manufacturer') >= 0) || (expression['type'].indexOf('Supplier') >= 0) || (expression['type'].indexOf('Channel') >= 0) || (expression['type'].indexOf('other') >= 0) ) {
                options = ['equal', 'not_equal']; 
            } else if (isNumericOrPriceAttribute(expression)) {
                options = [
                    'equal',
                    'not_equal',
                    'greater_than',
                    'less_than',
                    'greater_or_equal',
                    'less_or_equal'
                ];
            } else if(expression['type'].indexOf('image') >= 0) {
                options=[];
            }
    
            return options;
        }
    
        function hasPredefinedValues(expression)
        {
            return expression['type'].indexOf('dropdown') >= 0 || expression['type'].indexOf('box') >= 0;
        }
    
        function getMappedDataTypes()
        {
            return {
                'string': 'string',
                'dropdown': 'string',
                'text' : 'string',
                'int' : 'integer',
                'float': 'double',
                'other': 'string',
                'box' : 'string'
            };
        }
    
        function getConditionCodeTranslationMap()
        {
            return {
                'equal': 'Equal',
                'not_equal': 'Not equal',
                'greater_than': 'Greater than',
                'less_than': 'Less than',
                'greater_or_equal': 'Greater or equal',
                'less_or_equal': 'Less or equal',
                'contains': 'Contains',
                'contains_not': 'Contains not'
            };
        }
    
    /**
         * Creates value field for selected attribute type.
         *
         * @param expression
         * @param groupIndex
         * @param expressionIndex
         *
         * @returns string  Created value field as HTML
         */
        function createValueFieldForExpressionsAttributeType(expression, groupIndex, expressionIndex)
        {
                var valueFieldName =
                        parentTemplateId + 'ExpressionValue_' + groupIndex + '-' + expressionIndex,
                   // possibleFieldValues = getPossibleFieldValues(expression);
                   possibleFieldValues = [];
    
                if (!hasPredefinedValues(expression)) {
                    return createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName);
                }
    
                return createValueFieldForFieldWITHPredefinedValues(valueFieldName, expression, possibleFieldValues);
        }
    
        function createFieldValueForExpressionsAttributeType(expression, groupIndex, expressionIndex,inputWrapperNode,valueFieldName)
        {
            var valueFieldName =
                    parentTemplateId + 'ExpressionValue_' + groupIndex + '-' + expressionIndex
                
            if (!hasPredefinedValues(expression)) { 
                var fieldValue =  createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName);
                setWrapperNodeForFieldIfChangesExist(inputWrapperNode, fieldValue, valueFieldName);
    
                return;
            }
    
             getPossibleFieldValues(expression,function (possibleFieldValues) {                    
                var posibleValueField = null;
    
                if(possibleFieldValues != null)
                    posibleValueField = jQuery.parseJSON(possibleFieldValues);
    
                var inputWrapperValuesInnerHtml = createValueFieldForFieldWITHPredefinedValues(valueFieldName, expression, posibleValueField);
    
                setWrapperNodeForFieldIfChangesExist(inputWrapperNode, inputWrapperValuesInnerHtml, valueFieldName);
               
             });
        }
    
        function getPossibleFieldValues(expression,fCallBack)
        {
              var attributeCode = expression['code'],
                   IdAttribute = expression["IdAttr"];
            
                var dataOption = {
                    'attributeId' : IdAttribute
                };
    
                 $.ajax({
                      type: "GET",
                      url: "/getAttributeValueByAttrId" ,
                      data: dataOption,
                     // async : false,
                      success: function(data)
                      {  
                          fCallBack(data);
                      },
                      error: function(xhr)
                      {
                         
                      }
                  });
        }
    
        function createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName)
        {
            var type = 'text';
    
            if (expression['type'] === 'int' || expression['type'] === 'float' || expression['type'] === 'price' ) {
                type = 'number';
            }
    
            return '<input style="height:65%;" name="' + valueFieldName +'" ' +
                'id="' + valueFieldName + '" '+
                'class=" pricemonitor-form-field' + ' form-control" ' +
                'value="' + (expression.value[0] ? expression.value[0] : '')  +'" ' +
                'type="' + type + '"' +
                ' autocomplete="off" ' +
                (expression.type === "int" ? 'step="0.1" ' : '') +
                (expression.value.length > 0 ? " readonly disabled" : "") + '>' +
                '<span class="invalid-feedback"> This field is required! </span>';
        }
    
        function createDateForView(dateISOString)
        {
            var dateParts = dateISOString.split('-');
    
            if (dateParts.length === 3) {
                var day = dateParts[2],
                    month = dateParts[1],
                    year = dateParts[0];
    
                return day + '/' + month + '/' + year;
            }
    
            return dateISOString;
        }
    
        function createValueFieldForFieldWITHPredefinedValues(valueFieldName, expression, possibleFieldValues)
        {
            var selectInnerHtml = '';
    
            selectInnerHtml += '<select style= "width:100% !important; height:65%;padding-top:3px; " ' +
                'name="' + valueFieldName + '" ' +
                'class="pricemonitor-form-field' + ' form-control " ' +
                (expression.value.length > 0 ? " disabled" : "") + '>';
    
            for (var i = 0; i < possibleFieldValues.length; i++) {
                selectInnerHtml +=
                    '<option value="' + possibleFieldValues[i].backendName + '"' +
                    // (expression.value.indexOf(String(possibleFieldValues[i].value)) >= 0 ? ' selected' : '') +
                    '>' +
                    possibleFieldValues[i].backendName +
                    '</option>';
            }
    
            selectInnerHtml += '</select>' + '<span class="invalid-feedback"> This field is required! </span>';
    
            return selectInnerHtml;
        }
    
        function isNumericOrPriceAttribute(expression)
        {
            return expression['type'].indexOf('int') >= 0 ||
                expression['type'].indexOf('float') >= 0 ||
                expression['type'].indexOf('price') >= 0;
        }
    
        function appendCallbacksOnAddExpressionButtons()
        {
            var allAddButtons = document[formName].getElementsByClassName(
                parentTemplateId + '-add-expression'
            );
    
            for (var i = 0; i < allAddButtons.length; i++) {
                allAddButtons[i].addEventListener('click', addNewExpression);
            }
        }
    
        function addNewExpression(event)
        {
            var groupAndExpressionIndex = getGroupAndExpressionIndex(event.target.id),
                groupIndex = groupAndExpressionIndex['groupIndex'],
                expressionIndex = groupAndExpressionIndex['expressionIndex'],
                expressionAttrCodeFieldName =
                    parentTemplateId + 'ExpressionAttrCode_' + groupIndex + '-' + expressionIndex,
                expressionAttrValueFieldName =
                    parentTemplateId + 'ExpressionAttrValue_' + groupIndex + '-' + expressionIndex,
                expressionValueFieldName =
                    parentTemplateId + 'ExpressionValue_' + groupIndex + '-' + expressionIndex,
                expressionConditionFieldName =
                    parentTemplateId + 'ExpressionCondition_' +groupIndex+ '-' +expressionIndex,
                expressionAttrCode = document[formName][expressionAttrCodeFieldName].value,
                expressionValue = [document[formName][expressionValueFieldName].value],
                expressionCondition = document[formName][expressionConditionFieldName ].value;
    
            if (!isValidForm(expressionAttrCode, expressionValue, expressionCondition,expressionAttrValueFieldName,expressionConditionFieldName,expressionValueFieldName)) {
                event.preventDefault();
                return;
            }
    
            var newExpression = {
                    'code': expressionAttrCode,
                    // 'type': attributesCache[expressionAttrCode]['type'],
                    'type': 'text',
                    'value': expressionValue,
                    'condition': expressionCondition
                },
                filterRow = event.target.parentNode,
                removeButtonId =
                    parentTemplateId + 'RemoveExpression_' + groupIndex + '-' + expressionIndex;
    
            filterRow.innerHTML = createSavedExpressionRowHTML(
                newExpression,
                groupIndex,
                expressionIndex,
                removeButtonId
            );
    
            appendAddNewExpressionRowOnFilterRow(filterRow.parentNode, groupIndex, expressionIndex);
            initializeNewlyCreatedAttrDropdown(groupIndex, expressionIndex);
            document.getElementById(removeButtonId).addEventListener('click', removeExpression);
    
        }
    
        function isValidForm(expressionAttrCode, expressionValue, expressionCondition,expressionAttrValueFieldName,expressionConditionFieldName,expressionValueFieldName)
        {    
            try {

                removeValidationErrors(expressionAttrCode, expressionValue, expressionCondition,expressionAttrValueFieldName,expressionConditionFieldName,expressionValueFieldName);
    
                if (expressionAttrCode === '') {
                    document[formName][expressionAttrValueFieldName].classList.add('pricemonitor-invalid');
                    $('#'+expressionAttrValueFieldName).next('span').addClass("pricemonitor-invalid");
                    $('#'+expressionAttrValueFieldName).parent().find('.invalid-feedback').show();
                    
                    return false;
                }
    
                if (!expressionCondition || expressionCondition === '') {
                    document[formName][expressionConditionFieldName].classList.add('pricemonitor-invalid');
                    return false;
                }
        
                if (expressionValue.length === 0 || expressionValue[0] === "") {
                    document[formName][expressionValueFieldName].classList.add('pricemonitor-invalid');
                    $("#" + expressionValueFieldName).next('span').show();
                    
                    return false;
                }
    
                return true;

            } catch(err) {
            }          
        }
    
            function removeValidationErrors(expressionAttrCode, expressionValue, expressionCondition,expressionAttrValueFieldName,expressionConditionFieldName,expressionValueFieldName)
            {
                document[formName][expressionAttrValueFieldName].classList.remove('pricemonitor-invalid');
                $('#'+expressionAttrValueFieldName).next('span').removeClass("pricemonitor-invalid");
                $('#'+expressionAttrValueFieldName).parent().find('.invalid-feedback').hide();
                document[formName][expressionConditionFieldName].classList.remove('pricemonitor-invalid');
                document[formName][expressionValueFieldName].classList.remove('pricemonitor-invalid');
            }
    
        function getGroupAndExpressionIndex(fieldIdentifier)
        {
            var nameParts = fieldIdentifier.split('_'),
                expressionAndGroupIndex = nameParts[nameParts.length - 1].split('-');
    
            return {
                'groupIndex': parseInt(expressionAndGroupIndex[0]),
                'expressionIndex': expressionAndGroupIndex[1] ? parseInt(expressionAndGroupIndex[1]) : null
            }
        }
    
        function createSavedExpressionRowHTML(expression, groupIndex, expressionIndex, removeButtonId)
        {
            return createFilterRow(expression, groupIndex, expressionIndex) +
                '<button style="height:33px;" class="' + parentTemplateId + '-remove-expression  btn btn-danger " ' +
                'id="' + removeButtonId + '">' +
                'x' +
                '</button>';
        }
    
        function appendAddNewExpressionRowOnFilterRow(filterRowParent, groupIndex, expressionIndex)
        {
            var addNewExpressionRow = document.createElement('div'),
                emptyExpression = {
                    'code': '',
                    'type': 'string',
                    'value': [],
                    'condition': 'equal'
                },
                addNewExpressionBtnId = parentTemplateId + 'AddExpression_' + groupIndex + '-' +
                    (expressionIndex + 1);
                    
            var buttonRemoveId = parentTemplateId + 'RemoveExpression_' + groupIndex + '-' + (expressionIndex + 1);
    
            addNewExpressionRow.classList.add('form-row');
            addNewExpressionRow.innerHTML =
                createFilterRow(emptyExpression, groupIndex, (expressionIndex + 1)) +
                '<button style="height:33px;" class="' + parentTemplateId +'-add-expression btn btn-success " ' +
                'id="' + addNewExpressionBtnId + '">' +
                '+' +
                '</button>' +
                '<button style="height:33px;margin-left:1%;" class="' + parentTemplateId + '-remove-expression  btn btn-danger " ' +
                'id="' + buttonRemoveId + '">' +
                'x' +
                '</button>';
    
            filterRowParent.appendChild(addNewExpressionRow);
            document.getElementById(addNewExpressionBtnId).addEventListener('click', addNewExpression);
            document.getElementById(buttonRemoveId).addEventListener('click', removeExpression);
        }
    
        function initializeNewlyCreatedAttrDropdown(groupIndex, expressionIndex)
        {
            var dropdownFieldName =
                parentTemplateId + 'ExpressionAttrValue_' + groupIndex + '-' + (expressionIndex + 1);
    
                initializeAllFilterableDropdowns();
    
            // Pricemonitor['filterableDropDown']['initDropdown'](
            //     '',
            //     document[formName][dropdownFieldName]
            // );
        }
    
        function appendCallbacksOnRemoveExpressionButtons()
        {
            var allRemoveButtons = document[formName].getElementsByClassName(
                parentTemplateId + '-remove-expression'
            );
    
            for (var i = 0; i < allRemoveButtons.length; i++) {
                var removeButton = allRemoveButtons[i];
                removeButton.addEventListener('click', removeExpression);
            }
        }
    
        function removeExpression(event)
        {
            var filterRowForRemove = event.target.parentNode;
            filterRowForRemove.parentNode.removeChild(filterRowForRemove);
        }
    
        function appendCallbacksOnAddGroupButtons()
        {
            var addGroupButtons =
                document[formName].getElementsByClassName(parentTemplateId + 'add-new-group-button');
    
            for (var i = 0; i < addGroupButtons.length; i++) {
                addGroupButtons[i].addEventListener('click', addNewGroup);
            }
        }
    
        function addNewGroup(event)
        {
            var groupIndex = getGroupAndExpressionIndex(event.target.id)['groupIndex'];
    
            allGroups.push({'groupOperator': 'AND', 'operator': 'OR', 'expressions': []});
            var newGroupWrapper = document.createElement('div');
            newGroupWrapper.classList.add(parentTemplateId + '-single-group-wrapper');
            newGroupWrapper.id = parentTemplateId + '-groupWrapper_' + groupIndex;
            newGroupWrapper.innerHTML = createGroup(groupIndex, [], false);
    
            var addGroupButton = event.target;
            addGroupButton.parentNode.parentNode.removeChild(addGroupButton.parentNode);
    
            template.appendChild(newGroupWrapper);
    
            var formRow = document.createElement('div');
            formRow.classList.add('form-row');
            formRow.innerHTML = '<button class="' + parentTemplateId + 'add-new-group-button btn btn-info" ' +
                                    'id="' + parentTemplateId +'addNewGroup_' + allGroups.length + '">' +
                                    'Add group' +
                                '</button>';
    
            template.appendChild(formRow);
    
            initializeAllFilterableDropdowns();
    
            // Pricemonitor['filterableDropDown']['initDropdown'](
            //     '',
            //     document[formName][parentTemplateId + 'ExpressionAttrValue_' + groupIndex + '-' + 0]
            // );
    
            var addNewExpressionBtnId = parentTemplateId + 'AddExpression_' + groupIndex + '-' + 0,
                addNewGroupBtnId = parentTemplateId +'addNewGroup_' + allGroups.length;
                addNewGroupRemoveBtn = parentTemplateId + 'RemoveExpression_' + groupIndex + '-' + 0;
    
            document.getElementById(addNewExpressionBtnId).addEventListener('click', addNewExpression);
            document.getElementById(addNewGroupBtnId).addEventListener('click', addNewGroup);
            document.getElementById(addNewGroupRemoveBtn).addEventListener('click', removeExpression);
            appendCallbacksOnRemoveGroupButtons();
        }
    
        function appendCallbacksOnRemoveGroupButtons()
        {
            var removeGroupButtons =
                document[formName].getElementsByClassName(parentTemplateId + 'remove-group');
    
            for (var i = 0; i < removeGroupButtons.length; i++) {
                var removeGroupButton = removeGroupButtons[i];
                removeGroupButton.addEventListener('mouseup', removeGroupEventHandler);
            }
        }
    
        function removeGroupEventHandler(event)
        {
            var allGroupWrappers =
                document[formName].getElementsByClassName(parentTemplateId + '-single-group-wrapper');
    
            if (allGroupWrappers.length === 1) { 
                event.preventDefault();
                toastr["warning"]("Filter must have at least one group.!", "Just one group exist on page.");
                return false;
            }
    
            doGroupRemoval(allGroupWrappers);
    
            function isGroupEmpty()
            {
                var groupIndex = getGroupAndExpressionIndex(event.target.id)['groupIndex'],
                    targetGroup = document.getElementById(parentTemplateId + 'Group_' + groupIndex),
                    formRowsInTargetGroup = targetGroup.getElementsByClassName('form-row');
    
                // Each group must have at least form row for group operator and for adding new
                // expression
                return formRowsInTargetGroup.length < 3;
            }
    
            function doGroupRemoval(allGroupWrappers)
            {
                var groupForRemove = event.target.parentNode.parentNode.parentNode;
                groupForRemove.parentNode.removeChild(groupForRemove);
            }
        }
    
        var cancelLoadAttributesValues = false;
    
        function loadConditionsAndAttributeValues(sender,sId)
        {
            if(cancelLoadAttributesValues === true)
            {
                cancelLoadAttributesValues = false;
                return;
            }
                
            var id = $(sender).attr("id");        
            var dataType = $("#" + id + " option:selected").attr("data-type"); 
            
            var IdAttribute;
    
            if(dataType != null && dataType != "" && dataType == "dropdown" || dataType == "box")
                IdAttribute = $("#" + id + " option:selected").attr("value");  
                
             var attrValue = $("#" + id + " option:selected").attr("value"); 
    
            var nameFieldIdentifier = $(sender).attr("name");
    
              /**
                 * Expression that will be used for creating expression fields.
                 *
                 * @type {{code: *, type: string, value: Array}}
                 */
                var expression = {
                    'code': IdAttribute,
                    'type': dataType ? dataType : 'text',
                    'IdAttr' : IdAttribute,
                    'value': []
                },
                expressionAndGroupIndexes = getGroupAndExpressionIndex(nameFieldIdentifier),
                groupIndex = expressionAndGroupIndexes['groupIndex'],
                expressionIndex = expressionAndGroupIndexes['expressionIndex'];
    
                var expressionFormFieldName =
                        parentTemplateId + 'ExpressionAttrCode_' + groupIndex + '-' + expressionIndex;
    
                $("#"+expressionFormFieldName).val(attrValue);
                $("#"+expressionFormFieldName ).attr("data-type",dataType);
    
            loadConditionsForSelectedAttribute(groupIndex, expressionIndex, expression);
            loadAttributeValuesForSelectedAttribute(groupIndex, expressionIndex, expression);
        }
    
        function loadConditionsForSelectedAttribute(groupIndex, expressionIndex, expression)
        {
            var conditionFieldName =
                    parentTemplateId + 'ExpressionCondition_' + groupIndex + '-' + expressionIndex,
                inputWrapperNode = document[formName][conditionFieldName].parentNode,
                inputWrapperConditionsInnerHtml =
                    '<select style="height:65%;padding-top:3px;"  class="form-control"' +
                    'name="' +
                    parentTemplateId + 'ExpressionCondition_' + groupIndex + '-' + expressionIndex + '">' +
                    createConditionOptionsForExpressionsAttributeType(expression) +
                    '</select>';
    
            setWrapperNodeForFieldIfChangesExist(inputWrapperNode, inputWrapperConditionsInnerHtml, conditionFieldName);
        } 
    
        function setWrapperNodeForFieldIfChangesExist(wrapperNode, wrapperInnerHTML, fieldName)
        {
            if (wrapperNode.innerHTML === wrapperInnerHTML) {
                return;
            }
    
            wrapperNode.removeChild(document[formName][fieldName]);
            wrapperNode.innerHTML = wrapperInnerHTML;
        }
    
        function loadAttributeValuesForSelectedAttribute(groupIndex, expressionIndex, expression)
        {
            var valueFieldName = parentTemplateId + 'ExpressionValue_' + groupIndex + '-' + expressionIndex,
                inputWrapperNode = document[formName][valueFieldName].parentNode;
            
            createFieldValueForExpressionsAttributeType(expression, groupIndex, expressionIndex,inputWrapperNode,valueFieldName);
        }
    
        function checkFormIsValid(groupsForSave) {
    
            
            for (var i = 0; i < groupsForSave.length; i++) { 
               var expression = groupsForSave[i]['expressions'];
    
                for(j = 0; j < expression.length; j++) {
                    if(expression[j]['code'] == null || expression[j]['code'] == "")                  
                        return false;               
                        
                    if(expression[j]['condition'] == null || expression[j]['condition'] == "") 
                        return false;
                    if(expression[j]['type'] == null || expression[j]['type'] == "") 
                        return false;
                    if(expression[j]['value'] == null || expression[j]['value'] == "") 
                        return false;
                }
            }
          
            return true;
        }
    
        function saveFilter() {
    
            var filters = createFiltersForRequest();
    
            if(!validateWholeFormOnSave()) {
                return false;
            }          
    
            var transferObject = {
                'pricemonitorId': $("#contractId").val(),
                'type': filterQueryParams.filterType,
                'filters': filters
            };
    
            $.ajax({
                type: "POST",
                url: "/saveFilter",
                data: transferObject,
                success: function(data)
                {
                    toastr["success"]("Data are successfully saved!", "Successfully saved!");
    
                    if(data == null) 
                       return;
                },
                error: function(data)
                {
                    
                }
            });
        }
    
        /**
         * Creates filters in proper format for a request.
         *
         * @returns {Array}
         */
        function createFiltersForRequest()
        {
            var allGroupElements = parentTemplate.getElementsByClassName(
                    parentTemplateId + '-single-group-wrapper'
                ),
                groups = [];
    
            for (var i = 0; i < allGroupElements.length; i++) {
                var groupIndex = getGroupAndExpressionIndex(allGroupElements[i].id)['groupIndex'],
                    group = {
                        'name': 'Group' + ' ' + (i + 1),
                        'groupOperator':
                        document[formName][parentTemplateId + 'GroupOperator_'+ groupIndex].value,
                        'operator':
                        document[formName][parentTemplateId + 'Operator_'+ groupIndex].value,
                        'expressions': []
                    },
                    attributeCodeSelector = '.pricemonitor-form-field[name^=' + parentTemplateId +
                        'ExpressionAttrCode_' + groupIndex,
                    groupFields = allGroupElements[i].querySelectorAll(attributeCodeSelector);
    
                for (var j = 0; j < groupFields.length; j++) {
                    var expressionIndex =
                            getGroupAndExpressionIndex(groupFields[j]['name'])['expressionIndex'],
                        conditionFieldName =
                            parentTemplateId +'ExpressionCondition_' + groupIndex +'-'+ expressionIndex,
                        valueFieldName =
                            parentTemplateId +'ExpressionValue_' + groupIndex + '-' + expressionIndex;
    
                    var value = [document[formName][valueFieldName].value];
    
                    var dataTypeFilter = groupFields[j].dataset.type;
    
                    var typeDatas = getMappedDataTypes();
                    var filterTypeData = typeDatas[dataTypeFilter];
    
                    group['expressions'].push(
                        {
                            'code': groupFields[j].value,
                            'condition': document[formName][conditionFieldName].value,
                            'type': filterTypeData,
                            'value': value
                        }
                    );
                }
    
                groups.push(group);
            }
    
            return groups;
        }
    
    
        function validateWholeFormOnSave() {
            var allGroupElements = parentTemplate.getElementsByClassName(
                parentTemplateId + '-single-group-wrapper'
            ),
            groups = [];
    
        for (var i = 0; i < allGroupElements.length; i++) {
            var groupIndex = getGroupAndExpressionIndex(allGroupElements[i].id)['groupIndex'],
                group = {
                    'name': 'Group' + ' ' + (i + 1),
                    'groupOperator':
                    document[formName][parentTemplateId + 'GroupOperator_'+ groupIndex].value,
                    'operator':
                    document[formName][parentTemplateId + 'Operator_'+ groupIndex].value,
                    'expressions': []
                },
                attributeCodeSelector = '.pricemonitor-form-field[name^=' + parentTemplateId +
                    'ExpressionAttrCode_' + groupIndex,
                groupFields = allGroupElements[i].querySelectorAll(attributeCodeSelector);
    
            for (var j = 0; j < groupFields.length; j++) {
                var expressionIndex =
                        getGroupAndExpressionIndex(groupFields[j]['name'])['expressionIndex'],
                 
                  codeFieldName = 
                        parentTemplateId + 'ExpressionAttrCode_' + groupIndex + '-' + expressionIndex,
                 expressionAttrValueFieldName =
                        parentTemplateId + 'ExpressionAttrValue_' + groupIndex + '-' + expressionIndex,     
                  conditionFieldName =
                        parentTemplateId +'ExpressionCondition_' + groupIndex +'-'+ expressionIndex,
                    valueFieldName =
                        parentTemplateId +'ExpressionValue_' + groupIndex + '-' + expressionIndex;
    
                var value = [document[formName][valueFieldName].value];
    
                var dataTypeFilter = groupFields[j].dataset.type;
    
                var typeDatas = getMappedDataTypes();
                var filterTypeData = typeDatas[dataTypeFilter];
    
                if(!isValidForm(groupFields[j].value,value,document[formName][conditionFieldName].value,expressionAttrValueFieldName,conditionFieldName,valueFieldName))
                    return false;
            }       
        }
    
        return true;
    }
    
    
        function preview() {
            loadProductPreviewData(10,0);
        }
    
        function loadProductPreviewData(limit,offset) {
    
            var filters = createFiltersForRequest();
    
            var data = {
                'pricemonitorId': $("#contractId").val(),
                'type': filterQueryParams.filterType,
                'filters': filters,
                'limit' : limit,
                'offset': offset
            };
    
            $.ajax({
                type: "POST",
                url: "/filterPreview",
                data: data,
                success: function(data)
                {
                    var result = null;
    
                    if(data != null) {
                        result = jQuery.parseJSON(data);    
                        populateProductPreviewTable(result);
                    }
                        
                },
                error: function(data)
                {
                }
            });
        }
    
        function populateProductPreviewTable(data)
        {        
            if(data == null)
              return;
    
           $("#tableModal").DataTable().clear().draw();
    
            for(i = 0; i < data.length; i++) {
                
                var tableData = '<tr><td>' + data[i].id + '</td> <td>' + data[i].name + '</td>  <td>' + data[i].number + '</td><td>' + data[i].itemText + '</td> </tr>';
               
                $("#tableModal").DataTable().row.add([
                    data[i].id, data[i].name, data[i].number, data[i].itemText
                ]).draw();
            
            }

            $('#previewModal').modal('show');  
        }
    
    
        /*********************************** ACCOUNT FORM ***************************************/
    
        function getAccountInformation() {
    
            $.ajax({
                type: "GET",
                url: "/getAccountInfo",
                success: function(data)
                {
                    populateAccountFormWithSavedValues(data);
    
                },
                error: function(xhr)
                {
                   
                }
            });
       }
    
          function populateAccountFormWithSavedValues(response) {
               
            if (response == null)
                 return;
    
            var data = jQuery.parseJSON(response);     
    
            var transactionsRetention = data['transactionsRetentionInterval'],
            transactionDetailsRetention = data['transactionDetailsRetentionInterval'],
            TRANSACTION_DEFAULT_RETENTION_INTERVAL = 30,
            TRANSACTION_DETAILS_DEFAULT_RETENTION_INTERVAL = 7;
    
            document['accountInfo']['email']['value'] = data['userEmail'];
            document['accountInfo']['password']['value'] = data['userPassword'];
            document['accountInfo']['transactionsRetention']['value'] =
                (transactionsRetention !== '') ? transactionsRetention : TRANSACTION_DEFAULT_RETENTION_INTERVAL;
            document['accountInfo']['transactionDetailsRetention']['value'] =
                (transactionDetailsRetention !== '') ?
                    transactionDetailsRetention : TRANSACTION_DETAILS_DEFAULT_RETENTION_INTERVAL;
        }
    
    
        function saveAccountInfo() {
    
            var data = {
                'email' : document['accountInfo']['email']['value'],
                'password' : document['accountInfo']['password']['value'],
                'transactionsRetentionInterval' : document['accountInfo']['transactionsRetention']['value'],
                'transactionDetailsRetentionInterval' : document['accountInfo']['transactionDetailsRetention']['value']
            };
    
            $.ajax({
                type: "POST",
                url: "/saveAccountInfo",
                data: data,
                success: function(data)
                {
                    toastr["success"]("Account info is successfully saved!", "Successfully saved!"); 
                   
                },
                error: function(data)
                {
                }
            });
    
        }


