
$(document).ready(function() {
   
   document.getElementById("tabAttMapping").addEventListener("click", fetchDataAndSetPage);
      
});

var allMappingsAtttribute = [],
    templateIdMappings = 'pricemonitor-attributes-mapping',
    templateMappings = document.getElementById(templateIdMappings),
    pricemonitorAttributes = ['gtin', 'name', 'referencePrice', 'minPriceBoundary', 'maxPriceBoundary'];

function fetchDataAndSetPage()
{
    $.ajax({
        type: "GET",
        url: "/getAttributes",
        success: function(data)
        {
            var dataResult = null;
            if(data != null)
                dataResult = jQuery.parseJSON(data);

                setAttributesMappingForm(dataResult);
        },
        error: function(xhr)
        {
        }
    });  
}

function setAttributesMappingForm(response)
{
    if(response == null)
        return;

    allMappingsAtttribute = response;   

    setSavedMappings();

    setListOptionsForTextAttributes();
}

function setListOptionsForTextAttributes() {

    var textAttrsInnerHtml = '';

    for (var k in allMappingsAtttribute){
        if (allMappingsAtttribute.hasOwnProperty(k)) {

            if(k === "Other")
                continue;

            textAttrsInnerHtml += "<optgroup label= "+k+">";

            for(var n in allMappingsAtttribute[k])
            {
                if (allMappingsAtttribute[k].hasOwnProperty(n)) {
                    var splitedAttr = allMappingsAtttribute[k][n].split('-');
                  
                    if(splitedAttr[1] === "string" || splitedAttr[1] === "text")
                        textAttrsInnerHtml += " <option value="+n+" data-type="+splitedAttr[1]+" >"+ splitedAttr[0]  + "</option>";
                 }
             }
             textAttrsInnerHtml += " </optgroup>";
        }
    }

    appendListOptionsOnSelectBoxes('attributes-mapping-text-attributes', textAttrsInnerHtml);
    deleteEmptyOptGroup();
}

function deleteEmptyOptGroup() {
    $(".attributes-mapping-text-attributes optgroup, .attributes-mapping-custom-attributes optgroup").each(function (el,inx) {
       
        if($(inx).find('option').length == 0)
            $(inx).remove();        
    });   
}

 /**
  * Appends created HTML in dropdowns based on options wrapper class name and options HTML.
 */
function appendListOptionsOnSelectBoxes(optionsWrapperClassName, optionsHTML)
{
    var optionsWrappers = document.getElementsByClassName(optionsWrapperClassName);
    for (var i = 0; i < optionsWrappers.length; i++) {
        optionsWrappers[i].innerHTML = optionsHTML;
    }
}

/**
 * Creates options inner HTML based on source attributes.
 *
 * @param sourceAttributes
 * @returns {string}
 */
function createAttributeOptionsBasedOnSource(sourceAttributes)
{
     var dropdownInnerHtml = '';

    for (var k in sourceAttributes){
        if (sourceAttributes.hasOwnProperty(k)) {

            if(k === "Other")
                continue;

            dropdownInnerHtml += "<optgroup label= "+k+">";

            for(var n in sourceAttributes[k])
            {
                if (sourceAttributes[k].hasOwnProperty(n)) {
                    var splitedAttr = sourceAttributes[k][n].split('-');
                     
                    // if(splitedAttr[1] === "string" || splitedAttr[1] === "text")
                    if(splitedAttr[1] !== "image")
                        dropdownInnerHtml += " <option value="+n+" data-type="+splitedAttr[1]+" >"+ splitedAttr[0]  + "</option>";
                 }
             }
             dropdownInnerHtml += " </optgroup>";
        }
    }

    return dropdownInnerHtml;
}


function setSavedMappings() {

    var dataOption = {
        'priceMonitorContractId' : $("#contractId").val()
    };

    $.ajax({
        type: "GET",
        url: "/getMappedAttributes",
        data: dataOption,
        success: function(data)
        {
            setSavedValuesOnView(data);
        },
        error: function(xhr)
        {
        }
    });
}

function setSavedValuesOnView(response) {

    // Custom tags container should be emptied each time the page is loaded, so there aren't
    // duplicated rows.
    cleanUpCustomTags();

    var customAttributeIndex = -1,
    attributeMappings = response != null ? jQuery.parseJSON(response) : null,
    savedPricemonitorAttributeCodes = [];

    for (var i = 0; i < attributeMappings.length; i++) {
        if (pricemonitorAttributes.indexOf(attributeMappings[i]['priceMonitorCode']) >= 0) {
            setMandatoryAttributesValues(attributeMappings[i]);
        } else {
            customAttributeIndex++;
            createCustomField(customAttributeIndex, attributeMappings[i]);
        }
    }

    initializeNotSavedAttributeDropdowns();
    addRowForAddingNewCustomTag();

    /**
     * Cleans custom tags from DOM.
     */
    function cleanUpCustomTags()
    {
        var allFormRows = document
                .getElementById('attributes-mapping-custom-tags')
                .getElementsByClassName('form-row'),
            allRowsLength = allFormRows.length;

        for (var i = 0; i < allRowsLength; i++) {
            // when child is removed from dom indexes of elements are refreshed, that is why
            // always first element is deleted.
            allFormRows[0].parentNode.removeChild(allFormRows[0]);
        }
    }

    /**
     * Sets values in mandatory attributes.
     *
     * @param attributeMapping
     */
     function setMandatoryAttributesValues(attributeMapping)
     {
        attributeMapping = attributeMapping || attributeMappings[i];
        savedPricemonitorAttributeCodes.push(attributeMapping['priceMonitorCode']);

        var dropdownName = 'attribute-' + attributeMapping['priceMonitorCode'],
            operandFieldName =
                'attribute-' + attributeMapping['priceMonitorCode'] + '-operation',
            valueFieldName =
                'attribute-' + attributeMapping['priceMonitorCode'] + '-offset',
            operandField = document['pricemonitorAttributesMapping'][operandFieldName],
            valueField = document['pricemonitorAttributesMapping'][valueFieldName];

            //set dropDown

            $('select[name='+dropdownName+']').val(attributeMapping['attributeCode']);
            $('#' + dropdownName + "-code").val(attributeMapping['attributeCode']);

        if (operandField) {
            operandField.value = attributeMapping['operand'];
        }

        if (valueField) {
            valueField.value = attributeMapping['value'];
        }
     }

        /**
         * Initialize not saved attributes dropdowns with empty values.
         */
        function initializeNotSavedAttributeDropdowns()
        {
            for (var i = 0; i < pricemonitorAttributes.length; i++) {
                if (savedPricemonitorAttributeCodes.indexOf(pricemonitorAttributes[i]) < 0) {
                    var dropdownName = 'attribute-' + pricemonitorAttributes[i];

                 $("[name="+dropdownName+"]").change();
                  
                }
            }
        }

        /**
         * Adds row for adding new custom row by setting innerHTML in form row which will be appended
         * to custom tags container.
         */
        function addRowForAddingNewCustomTag()
        {
            var formRow = document.createElement('div');
            formRow.classList.add('form-row');
            formRow.classList.add('rows-grid');

            formRow.innerHTML =
                '<input type="text" name="customTagPricemonitorCode" class="pricemonitor-form-field">' +
                    '<div class="filterable-dropdown-wrapper" style="width:20%;">' +
                        '<select ' +
                        'onchange="updateCustomTagValuesOnInput(this)" ' +
                        'class="pricemonitor-filterable-dropdown form-control pricemonitor-form-field pricemonitor-filterable-list attributes-mapping-custom-attributes" ' +
                        'name="customTagAttributeValue" ' +
                        'id="custom-tag-add" ' +
                        '/>' +
                            createAttributeOptionsBasedOnSource(allMappingsAtttribute) +
                        '</select>' +
                        '<input type="hidden" ' +
                             'name="customTagAttributeCode" ' +
                             'class="pricemonitor-form-field" ' +
                            'id="attribute-custom-tag-add"/>' +
                       '</div>' +
                       '<button class="add-custom-tag btn btn-success" id="add-custom-tag">' +
                        '+' +
                        '</button>';

            document.getElementById('attributes-mapping-custom-tags').appendChild(formRow);

            // Appends handler when add new tag is clicked. Displaying and validation is handled here.
            document.getElementById('add-custom-tag').addEventListener('click', addTag);
            deleteEmptyOptGroup();
        }

        function addTag(event)
        {
            var newMapping =
            {
                'id': null,
                'priceMonitorCode': document['pricemonitorAttributesMapping']
                    ['customTagPricemonitorCode'].value,
                'attributeCode': document['pricemonitorAttributesMapping']
                    ['customTagAttributeCode'].value
            };

            var customTagsWrapper = document.getElementById('attributes-mapping-custom-tags'),
                allCustomTagsRows = customTagsWrapper.getElementsByClassName('form-row'),
                addNewTagRowElement = allCustomTagsRows[allCustomTagsRows.length - 1],
                lastAddedTagRowElement = allCustomTagsRows[allCustomTagsRows.length - 2],
                lastAddedTagInput = lastAddedTagRowElement ?
                    lastAddedTagRowElement.getElementsByTagName('input')[0] : null,
                lastAddedTagRowIndex =
                    lastAddedTagInput ? lastAddedTagInput.name.split('-')[1] : 0;

            // Removes row for adding new tag
            event.target.parentNode.parentNode.removeChild(addNewTagRowElement);

            // Creates new custom field and appends it as the last row.
            createCustomField(parseInt(lastAddedTagRowIndex) + 1, newMapping);

            // Adds row for adding new custom tag.
            addRowForAddingNewCustomTag();
        }

        /**
         * Validates if attribute code is valid.
         *
         * @param attributeCode
         * @returns {boolean}
         */
        function isValidMappingAttributeCode(attributeCode)
        {
            return attributeCode !== null && attributeCode.trim() !== '';
        }

       /**
         * Creates new custom field by using standard mechanism for creating form rows with form fields.
         *
         * @param index
         * @param mappedAttribute
         */
        function createCustomField(index, mappedAttribute)
        {
            var formRow = document.createElement('div');
            formRow.classList.add('form-row');
            formRow.classList.add("adjustRows");

            formRow.innerHTML =
                '<input type="text" style="width:21%" class="form-control" name="customTagPricemonitorCode-' + index + '"  ' +
                'disabled value=' + mappedAttribute['priceMonitorCode'] +' required>' +
                '<input type="hidden" value="' + mappedAttribute['id'] + '"' +
                ' name="customTagId-' + index +'" required>' +
                '<div class="filterable-dropdown-wrapper" style="width:20%">' +
                '<input type="text" ' +
                'class="pricemonitor-filterable-dropdown pricemonitor-form-field form-control" ' +
                'name="customTagAttributeValue-' + index + '" ' +
                'id="custom-tag-' + index + '" ' +
                'autocomplete="off" ' +
                'value="' + mappedAttribute["attributeCode"] + '"' +
                ' disabled required />' +
                '<input type="hidden" ' +
                'name="customTagAttributeCode-' + index +'" ' +
                'value="' + mappedAttribute["attributeCode"] + '"' +
                'class="pricemonitor-form-field" ' +
                'id="attribute-custom-tag-mapped-code-' + index + '" required/>' +
                '</div>' +
                '<button class="remove-custom-tag btn btn-danger" id="remove-custom-tag-' + index + '">-</button>';

            document.getElementById('attributes-mapping-custom-tags').appendChild(formRow);

            var attributeCode =
                mappedAttribute.hasOwnProperty('attributeCode') ? mappedAttribute['attributeCode'] : null;

            appendEventHandlersOnRemoveButtons();
        }

        /**
         * Appends event handlers for removing custom rows.
         */
        function appendEventHandlersOnRemoveButtons()
        {
            var tagsRemoveButtons = document.getElementsByClassName('remove-custom-tag');

            for (var i = 0; i < tagsRemoveButtons.length; i++) {
                tagsRemoveButtons[i].addEventListener('mouseup', removeTag);
            }

            function removeTag(event)
            {
                event.stopPropagation();
                event.target.parentNode.parentNode.removeChild(event.target.parentNode);
            }
        }
}

function updateCustomTagValuesOnInput(sender) {

    var id = $(sender).attr("id");
    var attrValue = $("#" + id + " option:selected").attr("value");

    $("#attribute-" + id).val(attrValue);
}

function updateCodeValuesOnInput(sender) {

    var id = $(sender).attr("id"); 
    var name = $(sender).attr("name");

    var attrValue = $("#" + id + " option:selected").attr("value"); 

    $("#" + name + "-code").val(attrValue);

}

function saveAttributesMapping()
{
    var requestMapping = createAttributesMappingForRequest();

    var transferObject = {
        'pricemonitorId': $("#contractId").val(),
        'mappings': requestMapping
    };

    $.ajax({
        type: "POST",
        url: "/saveAttributesMapping",
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
     * Creates attributes mapping transfer object for request.
     *
     * @returns {Array}
     */
    function createAttributesMappingForRequest()
    {
        var mappings = [];

        setMandatoryAttributesMappings();
        setCustomAttributesMappings();

        return mappings;

    function setMandatoryAttributesMappings()
    {
        for (var i = 0; i < pricemonitorAttributes.length; i++) {
            var prop = pricemonitorAttributes[i],
                formFieldName = 'attribute-' + prop + '-code',
                operand = '',
                value = '';

            if (prop === 'minPriceBoundary') {
                operand =document['pricemonitorAttributesMapping']
                    ['attribute-minPriceBoundary-operation'].value;
                value = document['pricemonitorAttributesMapping']['attribute-minPriceBoundary-offset']
                    .value;
            } else if (prop === 'maxPriceBoundary') {
                operand = document['pricemonitorAttributesMapping']
                    ['attribute-maxPriceBoundary-operation'].value;
                value = document['pricemonitorAttributesMapping']['attribute-maxPriceBoundary-offset']
                    .value;
            }

            mappings.push(
                {
                    'pricemonitorCode': prop,
                    'attributeCode': document['pricemonitorAttributesMapping'][formFieldName].value,
                    'operand': operand,
                    'value': value
                }
            );
        }
    }

    function setCustomAttributesMappings()
    {
        var customTagsWrapper = document.getElementById('attributes-mapping-custom-tags'),
            allRemoveButtons = customTagsWrapper.getElementsByClassName('remove-custom-tag');

        for (var i = 0; i < allRemoveButtons.length; i++) {
            var customTagIndexParts = allRemoveButtons[i].id.split('-'),
                customTagIndex = customTagIndexParts[customTagIndexParts.length - 1],
                customTagIdInputName = 'customTagId-' + customTagIndex,
                customAttributeInputName = 'customTagAttributeCode-' + customTagIndex,
                customPMAttributeInputName = 'customTagPricemonitorCode-' + customTagIndex;

            mappings.push(
                {
                    'id': document['pricemonitorAttributesMapping'][customTagIdInputName].value,
                    'pricemonitorCode':
                    document['pricemonitorAttributesMapping'][customPMAttributeInputName].value,
                    'attributeCode':
                    document['pricemonitorAttributesMapping'][customAttributeInputName].value,
                    'operand': '',
                    'value': ''
                }
            );
        }
    }
}
