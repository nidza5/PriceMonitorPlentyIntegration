function showTabContentContent(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontentprestaprice");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinksprestaprice");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}


$(document).ready(function() {
      
    console.log("document ready");

    // Get the element with id="defaultOpen" and click on it
      document.getElementById("defaultOpen").click(); 

    //   var acc = document.getElementsByClassName("accordion");
    //   var i;
      
    //   for (i = 0; i < acc.length; i++) {
    //     acc[i].addEventListener("click", function() {
    //       this.classList.toggle("active");
    //       var panel = this.nextElementSibling;
    //       if (panel.style.maxHeight){
    //         panel.style.maxHeight = null;
    //       } else {
    //         panel.style.maxHeight = panel.scrollHeight + "px";
    //       } 
    //     });
    //   }
  });

function expandCollapseMenu(el) {

 var $el = $(el);
 var liExpand = $el.find(".ulAccordation");

 liExpand.toggle();

}