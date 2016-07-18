jQuery.fn.exist = function(){
  return jQuery(this).length > 0;
}
$(function() {
    'use strict';
    $('.selectpicker').selectpicker();
    $("#amount").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $("#swapCurrency").on("click", function(){
        // get the values
        var from = $("#from").val();
        var to = $("#to").val();
        // set the values
        $("#from").val(to).change();
        $("#to").val(from).change();
        // refresh the dropdown
        $('.selectpicker').selectpicker('refresh');
        return false;
    });

});