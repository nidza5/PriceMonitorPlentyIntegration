
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
}

function updateDataAttributeContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue)
{
    var $el = $(".secondLevelButton[data-Id=" + idContract + "]");
 
    $el.attr("data-contractId",contractId);
    $el.attr("data-salesPrice",insertSalesPriceValue);
    $el.attr("data-insertPrices",insertPricesValue);

}

$(document).ready(function() {
     
    console.log("document ready");
     $('.js-example-basic-multiple').select2({
        placeholder: "Select sales prices",
        allowClear: true,
        width: '100%'
     });

    // Get the element with id="defaultOpen" and click on it
      document.getElementById("defaultOpen").click(); 

      document.getElementById("tabItemSelection").addEventListener("click", getFilter);
      
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
                console.log("data");
                console.log(data);

                if(data == null) 
                   return;
                
                var data = jQuery.parseJSON( data );
                if(data != null)
                {
                  var insertPricesValue = data.isInsertSalesPrice == "1" ? "true" : "false";

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
                console.log(data);
            }
        });
    }


    function getFilter() {

      var dataOption = {
          'priceMonitorId' : $("#contractId").val()
      };

        $.ajax({
            type: "GET",
            url: "/getFilters" ,
            data: dataOption,
            success: function(data)
            {
                console.log("data");
                console.log(data);

                createFiltersForm(data);

            },
            error: function(xhr)
            {
                console.log(xhr);
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

        // GetAttributes From ajax and in success call function fillFormwithData

        $.ajax({
            type: "GET",
            url: "/getAttributes",
            success: function(data)
            {
                console.log("attributeeees");
                console.log(data);

                var dataResult = null;
                if(data != null)
                    dataResult = jQuery.parseJSON(data);


                fillFormWithData(dataResult);

            },
            error: function(xhr)
            {
                console.log(xhr);
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
        var dropdownInnerHtml = '';

        for (var k in allAttributes){
            if (allAttributes.hasOwnProperty(k)) {
                console.log("Key is " + k);
                console.log("Value is ");
                console.log(allAttributes[k]);

                dropdownInnerHtml += "<optgroup label= "+k+">";

                for(var n in allAttributes[k])
                {
                    if (allAttributes[k].hasOwnProperty(n)) {
                        console.log("Key is " + n);
                        console.log("Value is ");
                        console.log(allAttributes[k][n]);

                        var splitedAttr = allAttributes[k][n].split('-');

                        dropdownInnerHtml += " <option value="+n+" data-type="+splitedAttr[1]+" >"+ splitedAttr[0]  + "</option>";

                        attributesCache[n] = "Code-" + n;


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

        return '<div class="form-row">' +
            createFilterRow(emptyExpression, groupIndex, groupExpressions.length) +
            '<button style="height:33px;" class="' + parentTemplateId +'-add-expression  btn btn-success" ' +
            'id="' + parentTemplateId + 'AddExpression_' +  groupIndex + '-' +
            groupExpressions.length + '">' +
            '+' +
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
            savedAttribute = attributesCache[expression['code']];
     
        if (!savedAttribute) {
            // saved attribute will not exist when adding new row for adding expressions. It is
            // important to add them in array for initialization drop-downs.
            addedAttributeDropdownsFieldNameValues.push(
                {
                    'name': expressionFormFieldValue,
                    'value': ''
                }
            );
        }

        return '<div class="filterable-dropdown-wrapper input-wrapper col-sm-3 ">' +
                    '<select ' +
                        'class="pricemonitor-filterable-dropdown js-example-basic-single pricemonitor-form-field ' + '" ' +
                        'name="' + expressionFormFieldValue + '" ' +
                        'id="' + expressionFormFieldValue + '" ' +
                        'autocomplete="off" ' +
                        'onchange="loadConditionsAndAttributeValues(this)" ' +
                        'value="' +
                        (savedAttribute && savedAttribute.hasOwnProperty('label') ? savedAttribute['label'] : "")
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
                        'value="' + (savedAttribute ? savedAttribute['code'] : "") + '" ' +
                        'required' +
                    '/>' +
                    '<ul class="pricemonitor-filterable-list ' + parentTemplateId + '-all-attributes">' +
                       
                    '</ul>' +
                '</div>' +
                '<div class="input-wrapper col-sm-3">' +
                    '<select class="pricemonitor-form-field ' + ' form-control" ' +
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
        || (expression['type'].indexOf('Manufacturer') >= 0) || (expression['type'].indexOf('Supplier') >= 0) || (expression['type'].indexOf('Channel') >= 0) ) {
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
        return expression['type'].indexOf('dropdown') >= 0;
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

            console.log("has predefined value");
           
            var fieldValue =  createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName);
            setWrapperNodeForFieldIfChangesExist(inputWrapperNode, fieldValue, valueFieldName);

            return;
        }

         getPossibleFieldValues(expression,function (possibleFieldValues) {
            console.log("create value field");
            console.log(possibleFieldValues);

            var posibleValueField = null;

            if(possibleFieldValues != null)
                posibleValueField = jQuery.parseJSON(possibleFieldValues);

            var inputWrapperValuesInnerHtml = createValueFieldForFieldWITHPredefinedValues(valueFieldName, expression, posibleValueField);

            console.log("inputWrapperValuesInnerHtml");
            console.log(inputWrapperValuesInnerHtml);

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
                      console.log("attributesValues");
                      console.log(data);    
                      
                      fCallBack(data);
                  },
                  error: function(xhr)
                  {
                      console.log(xhr);
                  }
              });
    }

    function createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName)
    {
        var type = 'text';

        if (expression['type'] === 'int' || expression['type'] === 'float' || expression['type'] === 'price' ) {
            type = 'number';
        }

        return '<input name="' + valueFieldName +'" ' +
            'id="' + valueFieldName + '" '+
            'class=" pricemonitor-form-field' + ' form-control" ' +
            'value="' + (expression.value[0] ? expression.value[0] : '')  +'" ' +
            'type="' + type + '"' +
            ' autocomplete="off" ' +
            (expression.type === "int" ? 'step="0.1" ' : '') +
            (expression.value.length > 0 ? " readonly disabled" : "") + '>';
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

        selectInnerHtml += '<select style= width:100% !important ' +
            'name="' + valueFieldName + '" ' +
            'class="pricemonitor-form-field' + ' form-control " ' +
            (expression.value.length > 0 ? " disabled" : "") + '>';

        for (var i = 0; i < possibleFieldValues.length; i++) {
            selectInnerHtml +=
                '<option value="' + possibleFieldValues[i].id + '"' +
                // (expression.value.indexOf(String(possibleFieldValues[i].value)) >= 0 ? ' selected' : '') +
                '>' +
                possibleFieldValues[i].backendName +
                '</option>';
        }

        selectInnerHtml += '</select>';

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

        // if (!isValidForm(expressionAttrCode, expressionValue, expressionCondition)) {
        //     return;
        // }

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

        function isValidForm()
        {
            removeValidationErrors();

            if (!expressionAttrCode || expressionAttrCode === '') {
                document[formName][expressionAttrValueFieldName].classList.add('pricemonitor-invalid');
                return false;
            }

            if (!expressionCondition || expressionCondition === '') {
                document[formName][expressionConditionFieldName].classList.add('pricemonitor-invalid');
                return false;
            }

            if (expressionValue.length === 0 ||
                !isValidValueForExpressionAttrType(expressionAttrCode, expressionValue)
            ) {
                document[formName][expressionValueFieldName].classList.add('pricemonitor-invalid');
                return false;
            }

            return true;
        }

        function removeValidationErrors()
        {
            document[formName][expressionAttrValueFieldName].classList.remove('pricemonitor-invalid');
            document[formName][expressionConditionFieldName].classList.remove('pricemonitor-invalid');
            document[formName][expressionValueFieldName].classList.remove('pricemonitor-invalid');
        }
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

        addNewExpressionRow.classList.add('form-row');
        addNewExpressionRow.innerHTML =
            createFilterRow(emptyExpression, groupIndex, (expressionIndex + 1)) +
            '<button style="height:33px;" class="' + parentTemplateId +'-add-expression btn btn-success " ' +
            'id="' + addNewExpressionBtnId + '">' +
            '+' +
            '</button>';

        filterRowParent.appendChild(addNewExpressionRow);
        document.getElementById(addNewExpressionBtnId).addEventListener('click', addNewExpression);
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

        document.getElementById(addNewExpressionBtnId).addEventListener('click', addNewExpression);
        document.getElementById(addNewGroupBtnId).addEventListener('click', addNewGroup);
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
            // var messageModal = new Pricemonitor['modal']['MessageModalConstructor'](
            //     Pricemonitor['utility']['translate'](
            //         'Filter must have at least one group.'
            //     )
            // );

            var messageModal = 'Filter must have at least one group.';

            alert(messageModal);
           // messageModal.open();
            return;
        }

        // if (!isGroupEmpty()) {
        //     var confirmationModal = new Pricemonitor['modal']['ConfirmationModalConstructor'](
        //         Pricemonitor['utility']['translate'](
        //             'There are expressions in this group. ' +
        //             'Are you sure that you want to delete this group?'
        //         ),
        //         doGroupRemoval,
        //         function() {
        //             return false;
        //         }
        //     );

        //     confirmationModal.open();
        //     return;
        // }

        doGroupRemoval();

        function isGroupEmpty()
        {
            var groupIndex = getGroupAndExpressionIndex(event.target.id)['groupIndex'],
                targetGroup = document.getElementById(parentTemplateId + 'Group_' + groupIndex),
                formRowsInTargetGroup = targetGroup.getElementsByClassName('form-row');

            // Each group must have at least form row for group operator and for adding new
            // expression
            return formRowsInTargetGroup.length < 3;
        }

        function doGroupRemoval()
        {
            var groupForRemove = event.target.parentNode.parentNode.parentNode;
            groupForRemove.parentNode.removeChild(groupForRemove);
        }
    }


    function loadConditionsAndAttributeValues(sender,sId)
    {
        var id = $(sender).attr("id");        
        var dataType = $("#" + id + " option:selected").attr("data-type"); 
        
        var IdAttribute;

        if(dataType != null && dataType != "" && dataType == "dropdown")
            IdAttribute = $("#" + id + " option:selected").attr("value");  
            

         console.log("idAttribute");
         console.log(IdAttribute);

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

            $("#"+expressionFormFieldName).val(IdAttribute);

        loadConditionsForSelectedAttribute(groupIndex, expressionIndex, expression);
        loadAttributeValuesForSelectedAttribute(groupIndex, expressionIndex, expression);
    }

    function loadConditionsForSelectedAttribute(groupIndex, expressionIndex, expression)
    {
        var conditionFieldName =
                parentTemplateId + 'ExpressionCondition_' + groupIndex + '-' + expressionIndex,
            inputWrapperNode = document[formName][conditionFieldName].parentNode,
            inputWrapperConditionsInnerHtml =
                '<select  class="form-control"' +
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

    function saveFilter() {

        var filters = createFiltersForRequest();

        console.log("filters");
        console.log(filters);

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
                console.log("data");
                console.log(data);

                toastr["success"]("Data are successfully saved!", "Successfully saved!");

                if(data == null) 
                   return;
            },
            error: function(data)
            {
                console.log(data);
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

                // if (attributesCache.hasOwnProperty(groupFields[j].value) &&
                //     attributesCache[groupFields[j].value].type === 'DateTime'
                // ) {
                //     value = [createDateObject(value[0]).toISOString().split('T')[0]];
                // }

                group['expressions'].push(
                    {
                        'code': groupFields[j].value,
                        'condition': document[formName][conditionFieldName].value,
                        'type': 'string',
                        'value': value
                    }
                );
            }

            groups.push(group);
        }

        return groups;
    }