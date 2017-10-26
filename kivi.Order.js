namespace('kivi.Order', function(ns) {
  ns.check_cv = function() {
    if ($('#type').val() == 'sales_order') {
      if ($('#order_customer_id').val() === '') {
        alert(kivi.t8('Please select a customer.'));
        return false;
      }
    } else  {
      if ($('#order_vendor_id').val() === '') {
        alert(kivi.t8('Please select a vendor.'));
        return false;
      }
    }
    return true;
  };

  ns.check_save_duplicate_parts = function() {
    var id_arr = $('[name="order.orderitems[].parts_id"]').map(function() {return this.value;}).get();

    var i, obj = {}, pos = [];

    for (i = 0; i < id_arr.length; i++) {
      var id = id_arr[i];
      if (obj.hasOwnProperty(id)) {
        pos.push(i + 1);
      }
      obj[id] = 0;
    }

    if (pos.length > 0) {
      return confirm(kivi.t8("There are duplicate parts at positions") + "\n"
                     + pos.join(', ') + "\n"
                     + kivi.t8("Do you really want to save?"));
    }
    return true;
  };

  ns.save = function(warn_on_duplicates) {
    if (!ns.check_cv()) return;
    if (warn_on_duplicates && !ns.check_save_duplicate_parts()) return;

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/save' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.save_and_delivery_order = function(warn_on_duplicates) {
    if (!ns.check_cv()) return;
    if (warn_on_duplicates && !ns.check_save_duplicate_parts()) return;

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/save_and_delivery_order' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.delete_order = function() {
    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/delete' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.show_print_options = function() {
    if (!ns.check_cv()) return;

    kivi.popup_dialog({
      id: 'print_options',
      dialog: {
        title: kivi.t8('Print options'),
        width:  800,
        height: 300
      }
    });
  };

  ns.print = function() {
    $('#print_options').dialog('close');

    var data = $('#order_form').serializeArray();
    data = data.concat($('#print_options_form').serializeArray());
    data.push({ name: 'action', value: 'Order/print' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.download_pdf = function(pdf_filename, key) {
    var data = [];
    data.push({ name: 'action', value: 'Order/download_pdf' });
    data.push({ name: 'type', value: $('#type').val() });
    data.push({ name: 'pdf_filename', value: pdf_filename });
    data.push({ name: 'key', value: key });
    $.download("../../controller.pl", data);
  };

  ns.email = function() {
    if (!ns.check_cv()) return;
    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/show_email_dialog' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  var email_dialog;

  ns.show_email_dialog = function(html) {
    var id            = 'jqueryui_popup_dialog';
    var dialog_params = {
      id:     id,
      width:  800,
      height: 500,
      modal:  true,
      close: function(event, ui) {
        email_dialog.remove();
      },
    };

    $('#' + id).remove();

    email_dialog = $('<div style="display:none" id="' + id + '"></div>').appendTo('body');
    email_dialog.html(html);
    email_dialog.dialog(dialog_params);

    $('.cancel').click(ns.close_email_dialog);

    return true;
  };

  ns.send_email = function() {
    var data = $('#order_form').serializeArray();
    data = data.concat($('#email_form').serializeArray());
    data.push({ name: 'action', value: 'Order/send_email' });
    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.close_email_dialog = function() {
    email_dialog.dialog("close");
  };

  ns.reload_cv_dependant_selections = function() {
    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/customer_vendor_changed' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.reformat_number = function(event) {
    $(event.target).val(kivi.format_amount(kivi.parse_amount($(event.target).val()), -2));
  };

  ns.recalc_amounts_and_taxes = function() {
    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/recalc_amounts_and_taxes' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.unit_change = function(event) {
    var row = $(event.target).parents("tbody").first();
    var item_id_dom = $(row).find('[name="orderitem_ids[+]"]');
    var sellprice_dom = $(row).find('[name="order.orderitems[].sellprice_as_number"]');
    var select_elt = $(row).find('[name="order.orderitems[].unit"]');

    var oldval = $(select_elt).data('oldval');
    $(select_elt).data('oldval', $(select_elt).val());

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/unit_changed' });
    data.push({ name: 'item_id', value: item_id_dom.val() });
    data.push({ name: 'old_unit', value: oldval });
    data.push({ name: 'sellprice_dom_id', value: sellprice_dom.attr('id') });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.update_sellprice = function(item_id, price_str) {
    var row = $('#item_' + item_id).parents("tbody").first();
    var price_elt = $(row).find('[name="order.orderitems[].sellprice_as_number"]');
    var html_elt  = $(row).find('[name="sellprice_text"]');
    price_elt.val(price_str);
    html_elt.html(price_str);
  };

  ns.init_row_handlers = function() {
    kivi.run_once_for('.recalc', 'on_change_recalc', function(elt) {
      $(elt).change(ns.recalc_amounts_and_taxes);
    });

    kivi.run_once_for('.reformat_number', 'on_change_reformat', function(elt) {
      $(elt).change(ns.reformat_number);
    });

    kivi.run_once_for('.unitselect', 'on_change_unit_with_oldval', function(elt) {
      $(elt).data('oldval', $(elt).val());
      $(elt).change(ns.unit_change);
    });

    kivi.run_once_for('.row_entry', 'on_kbd_click_show_hide', function(elt) {
      $(elt).keydown(function(event) {
        var row;
        if(event.keyCode == 40 && event.shiftKey === true) {
          // shift arrow down
          event.preventDefault();
          row = $(event.target).parents(".row_entry").first();
          $(row).children().not(':first').show();
          return false;
        }
        if(event.keyCode == 38 && event.shiftKey === true) {
          // shift arrow up
          event.preventDefault();
          row = $(event.target).parents(".row_entry").first();
          $(row).children().not(':first').hide();
          return false;
        }
      });
      $(elt).dblclick(function(event) {
        event.preventDefault();
        var row = $(event.target).parents(".row_entry").first();
        $(row).children().not(':first').toggle();
        return false;
      });
    });
  };

  ns.redisplay_linetotals = function(data) {
    $('.row_entry [name="linetotal"]').each(function(idx, elt) {
      $(elt).html(data[idx]);
    });
  };

  ns.renumber_positions = function() {
    $('.row_entry [name="position"]').each(function(idx, elt) {
      $(elt).html(idx+1);
    });
  };

  ns.reorder_items = function(order_by) {
    var dir = $('#' + order_by + '_header_id a img').attr("data-sort-dir");
    $('#row_table_id thead a img').remove();

    var src;
    if (dir == "1") {
      dir = "0";
      src = "image/up.png";
    } else {
      dir = "1";
      src = "image/down.png";
    }

    $('#' + order_by + '_header_id a').append('<img border=0 data-sort-dir=' + dir + ' src=' + src + ' alt="' + kivi.t8('sort items') + '">');

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/reorder_items' });
    data.push({ name: 'order_by', value: order_by });
    data.push({ name: 'sort_dir', value: dir });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.redisplay_items = function(data) {
    var old_rows = $('.row_entry').detach();
    var new_rows = [];
    $(data).each(function(idx, elt) {
      new_rows.push(old_rows[elt.old_pos - 1]);
    });
    $(new_rows).appendTo($('#row_table_id'));
    ns.renumber_positions();
  };

  ns.add_item = function() {
    if ($('#add_item_parts_id').val() === '') return;
    if (!ns.check_cv()) return;

    $('#row_table_id thead a img').remove();

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/add_item' });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.show_multi_items_dialog = function() {
    if (!ns.check_cv()) return;

    $('#row_table_id thead a img').remove();

    kivi.popup_dialog({
      url: '../../controller.pl?action=Order/show_multi_items_dialog',
      data: { type: $('#type').val(),
              callback: 'Order/add_multi_items',
              callback_data_id: 'order_form' },
      id: 'jq_multi_items_dialog',
      dialog: {
        title: kivi.t8('Add multiple items'),
        width:  800,
        height: 500
      }
    });
    return true;
  };

  ns.close_multi_items_dialog = function() {
    $('#jq_multi_items_dialog').dialog('close');
  };

  ns.delete_order_item_row = function(clicked) {
    var row = $(clicked).parents("tbody").first();
    $(row).remove();

    ns.renumber_positions();
    ns.recalc_amounts_and_taxes();
  };

  ns.row_table_scroll_down = function() {
    $('#row_table_scroll_id').scrollTop($('#row_table_scroll_id')[0].scrollHeight);
  };

  ns.show_longdescription_dialog = function(clicked) {
    var row = $(clicked).parents("tbody").first();
    var position = $(row).find('[name="position"]').html();
    var partnumber = $(row).find('[name="partnumber"]').html();
    var description_elt = $(row).find('[name="description"]');
    var description = description_elt.val();
    var longdescription_elt = $(row).find('[name="order.orderitems[].longdescription"]');
    var longdescription;

    if (!longdescription_elt.length) {
      var data = [];
      data.push({ name: 'action', value: 'Order/get_item_longdescription' });
      data.push({ name: 'type', value: $('#type').val() });
      data.push({ name: 'item_id', value: $(row).find('[name="order.orderitems[+].id"]').val() });
      data.push({ name: 'parts_id', value: $(row).find('[name="order.orderitems[].parts_id"]').val() });
      $.ajax({
        url: '../../controller.pl',
        data: data,
        method: "GET",
        async: false,
        dataType: 'text',
        success: function(val){
          longdescription = val;
        }
      });
    } else {
      longdescription = longdescription_elt.val();
    }

    var params = { runningnumber: position,
                   partnumber: partnumber,
                   description: description,
                   default_longdescription: longdescription,
                   set_function: function(val){
                     longdescription_elt.remove();
                     $('<input type="hidden" name="order.orderitems[].longdescription">').insertAfter(description_elt).val(val);
                   }
                 };
	console.log(params);
	
    ns.edit_longdescription_with_params(params);
  };

  ns.price_chooser_item_row = function(clicked) {
    var row = $(clicked).parents("tbody").first();
    var item_id_dom = $(row).find('[name="orderitem_ids[+]"]');

    var data = $('#order_form').serializeArray();
    data.push({ name: 'action', value: 'Order/price_popup' });
    data.push({ name: 'item_id', value: item_id_dom.val() });

    $.post("../../controller.pl", data, kivi.eval_json_result);
  };

  ns.update_price_source = function(item_id, source, descr, price_str, price_editable) {
    var row = $('#item_' + item_id).parents("tbody").first();
    var source_elt = $(row).find('[name="order.orderitems[].active_price_source"]');
    var button_elt = $(row).find('[name="price_chooser_button"]');

    button_elt.val(button_elt.val().replace(/.*\|/, descr + " |"));
    source_elt.val(source);

    var editable_div_elt = $(row).find('[name="editable_price"]');
    var not_editable_div_elt = $(row).find('[name="not_editable_price"]');
    if (price_editable == 1 && source === '') {
      // editable
      $(editable_div_elt).show();
      $(not_editable_div_elt).hide();
      $(editable_div_elt).find(':input').prop("disabled", false);
      $(not_editable_div_elt).find(':input').prop("disabled", true);
    } else {
      // not editable
      $(editable_div_elt).hide();
      $(not_editable_div_elt).show();
      $(editable_div_elt).find(':input').prop("disabled", true);
      $(not_editable_div_elt).find(':input').prop("disabled", false);
    }

    if (price_str) {
      var price_elt = $(row).find('[name="order.orderitems[].sellprice_as_number"]');
      var html_elt  = $(row).find('[name="sellprice_text"]');
      price_elt.val(price_str);
      html_elt.html(price_str);
      ns.recalc_amounts_and_taxes();
    }

    kivi.io.close_dialog();
  };
	ns.edit_longdescription_with_params = function(params) {
    var $container = $('#popup_edit_longdescription_input_container');
    var $edit      = $('<textarea id="popup_edit_longdescription_input" class="texteditor-in-dialog" wrap="soft" style="width: 750px; height: 220px;"></textarea>');

    $container.children().remove();
    $container.append($edit);

    if (params.element) {
      $container.data('element', params.element);
    }
    if (params.set_function) {
      $container.data('setFunction', params.set_function);
    }

    $edit.val(params.default_longdescription);

    $('#popup_edit_longdescription_runningnumber').html(params.runningnumber);
    $('#popup_edit_longdescription_partnumber').html(params.partnumber);

    var description = params.description.replace(/[\n\r]+/, '');
    if (description.length >= 50)
      description = description.substring(0, 50) + "…";
    $('#popup_edit_longdescription_description').html(description);

    kivi.popup_dialog({
      id:    'edit_longdescription_dialog',
      dialog: {
        title: kivi.t8('Enter longdescription'),
        open:  function() { kivi.focus_ckeditor_when_ready('#popup_edit_longdescription_input'); },
        close: function() { $('#popup_edit_longdescription_input_container').children().remove(); }
      }
    });
  };
  ns.update_discount_source = function(item_id, source, descr, discount_str, price_editable) {
    var row = $('#item_' + item_id).parents("tbody").first();
    var source_elt = $(row).find('[name="order.orderitems[].active_discount_source"]');
    var button_elt = $(row).find('[name="price_chooser_button"]');

    button_elt.val(button_elt.val().replace(/\|.*/, "| " + descr));
    source_elt.val(source);

    var editable_div_elt = $(row).find('[name="editable_discount"]');
    var not_editable_div_elt = $(row).find('[name="not_editable_discount"]');
    if (price_editable == 1 && source === '') {
      // editable
      $(editable_div_elt).show();
      $(not_editable_div_elt).hide();
      $(editable_div_elt).find(':input').prop("disabled", false);
      $(not_editable_div_elt).find(':input').prop("disabled", true);
    } else {
      // not editable
      $(editable_div_elt).hide();
      $(not_editable_div_elt).show();
      $(editable_div_elt).find(':input').prop("disabled", true);
      $(not_editable_div_elt).find(':input').prop("disabled", false);
    }

    if (discount_str) {
      var discount_elt = $(row).find('[name="order.orderitems[].discount_as_percent"]');
      var html_elt     = $(row).find('[name="discount_text"]');
      discount_elt.val(discount_str);
      html_elt.html(discount_str);
      ns.recalc_amounts_and_taxes();
    }

    kivi.io.close_dialog();
  };

});

$(function(){
  if ($('#type').val() == 'sales_order') {
    $('#order_customer_id').change(kivi.Order.reload_cv_dependant_selections);
  } else {
    $('#order_vendor_id').change(kivi.Order.reload_cv_dependant_selections);
  }

  if ($('#type').val() == 'sales_order') {
    $('#add_item_parts_id').on('set_item:PartPicker', function(e,o) { $('#add_item_sellprice_as_number').val(kivi.format_amount(o.sellprice, -2)) });
  } else {
    $('#add_item_parts_id').on('set_item:PartPicker', function(e,o) { $('#add_item_sellprice_as_number').val(kivi.format_amount(o.lastcost, -2)) });
  }
  $('#add_item_parts_id').on('set_item:PartPicker', function(e,o) { $('#add_item_description').val(o.description) });
  $('#add_item_parts_id').on('set_item:PartPicker', function(e,o) { $('#add_item_unit').val(o.unit) });

  $('.add_item_input').keydown(function(event) {
    if(event.keyCode == 13) {
      event.preventDefault();
      kivi.Order.add_item();
      return false;
    }
  });

  kivi.Order.init_row_handlers();

  $('#row_table_id').on('sortstop', function(event, ui) {
    $('#row_table_id thead a img').remove();
    kivi.Order.renumber_positions();
  });
});

  
