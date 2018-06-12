
$(document).ready(function() {
   
   document.getElementById("tabAttMapping").addEventListener("click", fetchDataAndSetPage);
      
});

var allMappingsAtttribute = [];
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

function setSavedMappings() {


}