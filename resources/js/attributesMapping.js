
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
            console.log(xhr);
        }
    });  
}

function setAttributesMappingForm(response)
{
    if(response == null)
        return;

    console.log("mapping response");
    console.log(response);

    allMappingsAtttribute = response;   

    setSavedMappings();

    setListOptionsForTextAttributes();
}

function setListOptionsForTextAttributes() {

    var textAttrsInnerHtml = '';

    for (var k in allMappingsAtttribute){
        if (allMappingsAtttribute.hasOwnProperty(k)) {

            textAttrsInnerHtml += "<optgroup label= "+k+">";

            for(var n in allMappingsAtttribute[k])
            {
                if (allMappingsAtttribute[k].hasOwnProperty(n)) {
                    var splitedAttr = allMappingsAtttribute[k][n].split('-');
                     
                    if(splitedAttr[1] === "string")
                        textAttrsInnerHtml += " <option value="+n+" data-type="+splitedAttr[1]+" >"+ splitedAttr[0]  + "</option>";
                 }
             }
             textAttrsInnerHtml += " </optgroup>";
        }
    }

    appendListOptionsOnSelectBoxes('attributes-mapping-text-attributes', textAttrsInnerHtml);
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

            dropdownInnerHtml += "<optgroup label= "+k+">";

            for(var n in sourceAttributes[k])
            {
                if (sourceAttributes[k].hasOwnProperty(n)) {
                    var splitedAttr = sourceAttributes[k][n].split('-');
                     
                    if(splitedAttr[1] === "string")
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
            console.log(xhr);
        }
    });
}

function setSavedValuesOnView(response) {

    // Custom tags container should be emptied each time the page is loaded, so there aren't
    // duplicated rows.
    cleanUpCustomTags();

    var customAttributeIndex = -1,
    attributeMappings = response,
    savedPricemonitorAttributeCodes = [];

    for (var i = 0; i < attributeMappings.length; i++) {
        if (pricemonitorAttributes.indexOf(attributeMappings[i]['pricemonitorCode']) >= 0) {
            setMandatoryAttributesValues(attributeMappings[i]);
        } //else {
        //     customAttributeIndex++;
        //     createCustomField(customAttributeIndex, attributeMappings[i]);
        // }
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
        savedPricemonitorAttributeCodes.push(attributeMapping['pricemonitorCode']);

        var dropdownName = 'attribute-' + attributeMapping['pricemonitorCode'],
            operandFieldName =
                'attribute-' + attributeMapping['pricemonitorCode'] + '-operation',
            valueFieldName =
                'attribute-' + attributeMapping['pricemonitorCode'] + '-offset',
            operandField = document['pricemonitorAttributesMapping'][operandFieldName],
            valueField = document['pricemonitorAttributesMapping'][valueFieldName];

            //set dropDown
        // Pricemonitor['filterableDropDown']['initDropdown'](
        //     attributeMapping['attributeCode'],
        //     document['pricemonitorAttributesMapping'][dropdownName]
        // );

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

                    resetUnsavedFields(dropdownName);

                    // set dropdown

                    // Pricemonitor['filterableDropDown']['initDropdown'](
                    //     null,
                    //     document['pricemonitorAttributesMapping'][dropdownName]
                    // );
                }
            }
        }

        function resetUnsavedFields(dropdownName)
        {
            // document['pricemonitorAttributesMapping'][dropdownName].value = '';
            // document['pricemonitorAttributesMapping'][dropdownName]
            //     .parentNode
            //     .querySelector('input[type=hidden]')
            //     .value = '';
        }

        /**
         * Adds row for adding new custom row by setting innerHTML in form row which will be appended
         * to custom tags container.
         */
        function addRowForAddingNewCustomTag()
        {
            var formRow = document.createElement('div');
            formRow.classList.add('form-row');

            formRow.innerHTML =
                '<input type="text" name="customTagPricemonitorCode" class="pricemonitor-form-field">' +
                    '<div class="filterable-dropdown-wrapper">' +
                        '<select ' +
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
                       '<button class="add-custom-tag" id="add-custom-tag">' +
                        '+' +
                        '</button>';

            document.getElementById('attributes-mapping-custom-tags').appendChild(formRow);

            // Pricemonitor['filterableDropDown']['initDropdown'](
            //     null,
            //     document['pricemonitorAttributesMapping']['customTagAttributeValue']
            // );

            // Appends handler when add new tag is clicked. Displaying and validation is handled here.
            document.getElementById('add-custom-tag').addEventListener('click', addTag);
        }

        function addTag(event)
        {
           // unsetValidationErrors();

            var newMapping =
            {
                'id': null,
                'pricemonitorCode': document['pricemonitorAttributesMapping']
                    ['customTagPricemonitorCode'].value,
                'attributeCode': document['pricemonitorAttributesMapping']
                    ['customTagAttributeCode'].value
            };

            if (!isValidMappingAttributeCode(newMapping['pricemonitorCode']) ||
                pricemonitorAttributes.indexOf(newMapping['pricemonitorCode'].toLowerCase()) >= 0
            ) {
                document['pricemonitorAttributesMapping']
                    ['customTagPricemonitorCode'].classList.add('pricemonitor-invalid');

                return;
            }

            if (!isValidMappingAttributeCode(newMapping['attributeCode'])) {
                document['pricemonitorAttributesMapping']
                    ['customTagAttributeValue'].classList.add('pricemonitor-invalid');

                return;
            }

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

            formRow.innerHTML =
                '<input type="text" name="customTagPricemonitorCode-' + index + '"  ' +
                'disabled value=' + mappedAttribute['pricemonitorCode'] +' required>' +
                '<input type="hidden" value="' + mappedAttribute['id'] + '"' +
                ' name="customTagId-' + index +'" required>' +
                '<div class="filterable-dropdown-wrapper">' +
                '<input type="text" ' +
                'class="pricemonitor-filterable-dropdown pricemonitor-form-field" ' +
                'name="customTagAttributeValue-' + index + '" ' +
                'id="custom-tag-' + index + '" ' +
                'autocomplete="off" ' +
                'value="' + mappedAttribute["attributeCode"] + '"' +
                ' disabled required />' +
                '<input type="hidden" ' +
                'name="customTagAttributeCode-' + index +'" ' +
                'class="pricemonitor-form-field" ' +
                'id="attribute-custom-tag-mapped-code-' + index + '" required/>' +
                '</div>' +
                '<button class="remove-custom-tag" id="remove-custom-tag-' + index + '">-</button>';

            document.getElementById('attributes-mapping-custom-tags').appendChild(formRow);

            var attributeCode =
                mappedAttribute.hasOwnProperty('attributeCode') ? mappedAttribute['attributeCode'] : null;

            // Pricemonitor['filterableDropDown']['initDropdown'](
            //     attributeCode,
            //     document['pricemonitorAttributesMapping']['customTagAttributeValue-' + index]
            // );

            appendEventHandlersOnRemoveButtons();
        }

        /**
         * Appends event handlers for removing custom rows.
         */
        function appendEventHandlersOnRemoveButtons()
        {
            var tagsRemoveButtons = document.getElementsByClassName('remove-custom-tag');

            for (var i = 0; i < tagsRemoveButtons.length; i++) {
               // var removeButton = Pricemonitor['utility']['destroyEventHandlers'](tagsRemoveButtons[i]);
                tagsRemoveButtons[i].addEventListener('mouseup', removeTag);
            }

            function removeTag(event)
            {
                event.stopPropagation();
                event.target.parentNode.parentNode.removeChild(event.target.parentNode);
            }
        }
}
