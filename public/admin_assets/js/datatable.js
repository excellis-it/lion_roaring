/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";







$(document).ready(function () {
  $("#example").DataTable({
    dom: "Bfrtip",
    buttons: [
    ]
  });
});
$(document).ready(function () {
  $("#data_table").DataTable({
    dom: "Bfrtip",
    buttons: [
      "copy",
      "excelFlash",
      "excel",
      "pdf",
      "print",
      {
        text: "Reload",
        action: function (e, dt, node, config) {
          dt.ajax.reload();
        }
      }
    ]
  });
});
$(document).ready(function () {
  $("#data_table_b2b_new_re").DataTable({
    dom: "Bfrtip",
    buttons: [
      "copy",
      "excelFlash",
      "excel",
      "pdf",
      "print",
      {
        text: "Reload",
        action: function (e, dt, node, config) {
          dt.ajax.reload();
        }
      }
    ]
  });
});
