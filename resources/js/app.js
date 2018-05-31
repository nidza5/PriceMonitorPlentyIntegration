
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
    var parentTemplateId = document.getElementById("pricemonitor-product-selection");
    var attributesCache = {};
    var specificSystemAttributes = {};
    var  addedDateFieldID = null;


    function createFiltersForm(responseData) {

        var dataResponse = "";

        if(responseData != null)
            dataResponse = jQuery.parseJSON(responseData);

        if(dataResponse != null)
             allGroups = dataResponse.filters;

        // GetAttributes From ajax and in success call function fillFormwithData

        fillFormWithData(null);

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

        for (var prop in allAttributes) {
            if (allAttributes.hasOwnProperty(prop)) {
                dropdownInnerHtml += '<li class="pricemonitor-list-group">' + prop + '</li>';

                for (var i = 0; i < allAttributes[prop].length; i++) {
                    dropdownInnerHtml += '<li class="pricemonitor-filterable-list-item">' +
                        '<a id="' + allAttributes[prop][i]['code'] + '">' +
                        allAttributes[prop][i]['label'] +
                        '</a>' +
                        '</li>';

                    attributesCache[allAttributes[prop][i]['code']] = allAttributes[prop][i];
                }
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
        // appendCallbacksOnAddExpressionButtons();
        // appendCallbacksOnRemoveExpressionButtons();
        // appendCallbacksOnAddGroupButtons();
        // appendCallbacksOnRemoveGroupButtons();

        // var submitButton = document.getElementById(parentTemplateId + '-submit'),
        //     previewButton = document.getElementById(parentTemplateId + '-preview');

        // submitButton.addEventListener('click', saveFilter);
        // previewButton.addEventListener('click', preview);
    }

    function initializeAllFilterableDropdowns()
    {
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
            '<button class="' + parentTemplateId + 'add-new-group-button" ' +
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
                '<select class="' + (groupOperatorDisabled ? "pricemonitor-form-field" : "") + '" ' +
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
                '<label for="' + parentTemplateId + 'Operator_' + groupIndex + '">' +
                'Group type' + '</label>' +
                '<select class="pricemonitor-form-field" ' +
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
            '<button class="' + parentTemplateId +'-add-expression" ' +
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

        return '<div class="filterable-dropdown-wrapper input-wrapper">' +
            '<input type="text" ' +
            'class="pricemonitor-filterable-dropdown ' +
            (savedAttribute && savedAttribute.hasOwnProperty('label') ?
                "pricemonitor-form-field" : "") + '" ' +
            'name="' + expressionFormFieldValue + '" ' +
            'id="' + expressionFormFieldValue + '" ' +
            'autocomplete="off" ' +
            'value="' +
            (savedAttribute && savedAttribute.hasOwnProperty('label') ? savedAttribute['label'] : "")
            + '" ' +
            (savedAttribute ? "readonly disabled" : "") + ' ' +
            'required' +
            '/>' +
            '<input type="hidden" ' +
            'class="' + (savedAttribute && savedAttribute.hasOwnProperty('code') ?
                "pricemonitor-form-field" : "") + '" ' +
            'name="' + expressionFormFieldName + '" ' +
            'id="' + expressionFormFieldName + '" ' +
            'value="' + (savedAttribute ? savedAttribute['code'] : "") + '" ' +
            'required' +
            '/>' +
            '<ul class="pricemonitor-filterable-list ' + parentTemplateId + '-all-attributes">' +
            dropdownInnerHtml +
            '</ul>' +
            '</div>' +
            '<div class="input-wrapper">' +
            '<select class="' +  (savedAttribute ? "pricemonitor-form-field" : "" ) + '" ' +
            'name="' +
            parentTemplateId + 'ExpressionCondition_' + groupIndex + '-' + expressionIndex + '"' +
            ' required ' +
            (savedAttribute ? ' disabled' : '') +
            '>' +
            createConditionOptionsForExpressionsAttributeType(expression) +
            '</select>' +
            '</div>' +
            '<div class="input-wrapper">' +
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
        if (specificSystemAttributes.hasOwnProperty(expression['code'])) {
            // If attribute is specific, and has expressions set in configuration then logic for getting
            // conditions will not be executed and conditions from configuration will be returned.
            return specificSystemAttributes[expression['code']]['conditions'];
        }

        // By default options fo text field are loaded.
        var options = ['equal', 'not_equal', 'contains', 'contains_not'];

        if (hasPredefinedValues(expression)) {
            options = ['equal', 'not_equal'];
        } else if (isNumericOrDateAttribute(expression)) {
            options = [
                'equal',
                'not_equal',
                'greater_than',
                'less_than',
                'greater_or_equal',
                'less_or_equal'
            ];
        } else if (expression['type'].indexOf('boolean') >= 0) {
            options = ['equal', 'not_equal'];
        }

        return options;
    }

    function hasPredefinedValues(expression)
    {
        return (attributesCache.hasOwnProperty(expression['code']) &&
            attributesCache[expression['code']].hasOwnProperty('options') &&
            attributesCache[expression['code']]['options'].length > 0) ||
            expression['type'].indexOf('[]') >= 0;
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
        // Added date field should be reset when creating values field.
        addedDateFieldID = null;

        var valueFieldName =
                parentTemplateId + 'ExpressionValue_' + groupIndex + '-' + expressionIndex,
            possibleFieldValues = getPossibleFieldValues(expression);

        if (!hasPredefinedValues(expression)) {
            return createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName);
        }

        return createValueFieldForFieldWITHPredefinedValues(valueFieldName, expression, possibleFieldValues);
    }

    function getPossibleFieldValues(expression)
    {
        var possibleFieldValues = [],
            attributeCode = expression['code'];

        if (expression['type'] === 'boolean') {
            possibleFieldValues = [
                {'label': 'Yes', 'value': 1},
                {'label': 'No', 'value': 0}
            ];
        } else if (attributesCache.hasOwnProperty(attributeCode) &&
            attributesCache[attributeCode].hasOwnProperty('options')) {
            possibleFieldValues = attributesCache[attributeCode]['options'];
        }

        return possibleFieldValues;
    }

    function createValueFieldForFieldWithoutPredefinedValues(expression, valueFieldName)
    {
        var type = 'text';

        if (expression['type'] === 'integer' || expression['type'] === 'double') {
            type = 'number';
        } else if (expression['type'] === 'DateTime') {
            if (expression.value.length === 0) {
                // Only for new filter rows date control should be loaded. When filter is already saved
                // input with date is all that is needed because date is not editable.
                addedDateFieldID = valueFieldName;
            } else {
                expression.value = [createDateForView(expression.value[0])];
            }
        }

        return '<input name="' + valueFieldName +'" ' +
            'id="' + valueFieldName + '" '+
            'class="' + (expression.value.length > 0 ? "pricemonitor-form-field" : "") + '" ' +
            'value="' + (expression.value[0] ? expression.value[0] : '')  +'" ' +
            'type="' + type + '"' +
            ' autocomplete="off" ' +
            (expression.type === "double" ? 'step="0.1" ' : '') +
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

        selectInnerHtml += '<select ' +
            'name="' + valueFieldName + '" ' +
            'class="' + (expression.value.length > 0 ? "pricemonitor-form-field" : "") + '" ' +
            (expression.value.length > 0 ? " disabled" : "") + '>';

        for (var i = 0; i < possibleFieldValues.length; i++) {
            selectInnerHtml +=
                '<option value="' + possibleFieldValues[i].value + '"' +
                (expression.value.indexOf(String(possibleFieldValues[i].value)) >= 0 ? ' selected' : '') +
                '>' +
                possibleFieldValues[i].label +
                '</option>';
        }

        selectInnerHtml += '</select>';

        return selectInnerHtml;
    }