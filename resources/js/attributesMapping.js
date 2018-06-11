
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

    // Fetch price type attributes because only price types attributes can be mapped on min, max and ref price.
    Pricemonitor['ajax']['get'](
        Pricemonitor['config']['urls']['attributes'],
        {'pricemonitorContractId': Pricemonitor['config']['pricemonitorId'], 'type': 'price'},
        setPriceAndSavedAttributes,
        'json',
        true
    );

    allAttributes = response['data'];

    Pricemonitor['contracts']['utility']['scopeDestroy'](document['pricemonitorAttributesMapping']);
    setListOptionsForTextAttributes();

}