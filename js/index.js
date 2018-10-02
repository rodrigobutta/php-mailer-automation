
$(document).ready(function() {
    $('#example').DataTable();
} );

// Get value from data table
 $(document).on("click", "#btnMyTest001", function (e) {
     $('#my_modal #age').attr("value", $(this).attr("data-age"));
 })