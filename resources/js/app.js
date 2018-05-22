function showTabContentContent(evt, nameTab) {
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

    if(nameTab == "Contracts")
        showTabContent(evt, 'ContractInfo');
}


$(document).ready(function() {
      
    console.log("document ready");

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
    evt.currentTarget.className += " active";

}