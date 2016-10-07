namespace('kivi', function(k){
  k.PartPicker = function($real, options) {
    // short circuit in case someone double inits us
    if ($real.data("part_picker"))
      return $real.data("part_picker");

    var KEY = {
      ESCAPE: 27,
      ENTER:  13,
      TAB:    9,
      LEFT:   37,
      RIGHT:  39,
      PAGE_UP: 33,
      PAGE_DOWN: 34,
    };
    var CLASSES = {
      PICKED:       'partpicker-picked',
      UNDEFINED:    'partpicker-undefined',
      FAT_SET_ITEM: 'partpicker_fat_set_item',
    }
    var o = $.extend({
      limit: 20,
      delay: 50,
      fat_set_item: $real.hasClass(CLASSES.FAT_SET_ITEM),
    }, options);
    var STATES = {
      PICKED:    CLASSES.PICKED,
      UNDEFINED: CLASSES.UNDEFINED
    }
    var real_id = $real.attr('id');
    var $dummy  = $('#' + real_id + '_name');
    var $type   = $('#' + real_id + '_type');
    var $unit   = $('#' + real_id + '_unit');
    var $convertible_unit = $('#' + real_id + '_convertible_unit');
    var state   = STATES.PICKED;
    var last_real = $real.val();
    var last_dummy = $dummy.val();
    var timer;

    function open_dialog () {
      k.popup_dialog({
        url: '../../controller.pl?action=Part/part_picker_search',
        data: $.extend({
          real_id: real_id,
        }, ajax_data($dummy.val())),
        id: 'part_selection',
        dialog: {
          title: k.t8('Part picker'),
          width: 800,
          height: 800,
        }
      });
      window.clearTimeout(timer);
      return true;
    }

    function ajax_data(term) {
      var data = {
        'filter.all:substr:multi::ilike': term,
        'filter.obsolete': 0,
        'filter.unit_obj.convertible_to': $convertible_unit && $convertible_unit.val() ? $convertible_unit.val() : '',
        no_paginate:  $('#no_paginate').prop('checked') ? 1 : 0,
        current:  $real.val(),
      };

      if ($type && $type.val())
        data['filter.type'] = $type.val().split(',');

      if ($unit && $unit.val())
        data['filter.unit'] = $unit.val().split(',');

      return data;
    }

    function set_item (item) {
      if (item.id) {
        $real.val(item.id);
        // autocomplete ui has name, use the value for ajax items, which contains displayable_name
        $dummy.val(item.name ? item.name : item.value);
      } else {
        $real.val('');
        $dummy.val('');
      }
      state = STATES.PICKED;
      last_real = $real.val();
      last_dummy = $dummy.val();
      last_unverified_dummy = $dummy.val();
      $real.trigger('change');

      if (o.fat_set_item && item.id) {
        $.ajax({
          url: '../../controller.pl?action=Part/show.json',
          data: { id: item.id },
          success: function(rsp) {
            $real.trigger('set_item:PartPicker', rsp);

/***************************************************/
console.log(rsp);
    $('<li class="inserted new">' +
        '<img src="../../image/updown.png" class="mv">' +
        '<img src="../../image/close.png" class="rmv">' +
        '<input name="order_nr" type="text" class="ui-widget-content ui-corner-all pos elem">' +
        '<input type="text" id="test_text_input_01" class="test_text_input_01 ui-widget-content ui-corner-all itemNr elem">' +
        '<input type="text" id="test_text_input_02" class="test_text_input_02 ui-widget-content ui-corner-all description elem">' +
        '<input type="hidden" class="add_item_input part_autocomplete partpicker_fat_set_item" id="add_item_parts_id" name="add_item.parts_id" value="">'+
        '<select id="test_text_input_03" name="pos_unit" type="text" class="test_text_input_03 ui-widget-content ui-corner-all unity elem" autocomplete="off">' +
            '<option selected="selected"></option>' + //!get via AJAX
            '<option>Stck</option>' +
            '<option>kg</option>' +
            '<option>Std</option>' +
        '</select>' +
        '<input type="text" id="test_text_input_04" class="test_text_input_04 ui-widget-content ui-corner-all price elem">' +
        '<input type="text" id="test_text_input_05" class="test_text_input_05 ui-widget-content ui-corner-all discount elem">' +
        '<input name="pos_total" value="0" type="text" class="ui-widget-content ui-corner-all total elem" autocomplete="off">' +
        '<select name="pos_emp" type="text" class="ui-widget-content ui-corner-all mechanics elem" autocomplete="off">' +
            '<option value="0" selected="selected"></option>' +
            '<option value="1">Mechaniker</option>' +
            '<option value="2">Monteur</option>' +
        '</select>' +
        '<select name="pos_status" type="text" class="ui-widget-content ui-corner-all status elem" autocomplete="off">' +
            '<option value="0" selected="true"></option>' +
            '<option value="1">gelesen</option>' +
            '<option value="2">Bearbeitung</option>' +
            '<option value="3">erledigt</option>' +
        '</select>' +
        '<label name="pos_id" class="posID elem">test</label>' +
    '</li>').insertBefore('.insertInput');
        $('.new').children('.test_text_input_01').val(rsp.partnumber);
        $('.new').children('.test_text_input_02').val(rsp.description);
        $('.new').children('.test_text_input_03').val(rsp.unit);
        $('.new').children('.test_text_input_04').val(rsp.sellprice);
        $('.new').children('.test_text_input_05').val(rsp.not_discountable);
        $('.inserted').removeClass('new');
        $('#add_item_parts_id_name').val('');
        $('.inserted').children('.description').addClass('description2');
        $('.inserted').children('.unity').addClass('unity2');
        $('.inserted').children('.price').addClass('price2');
        $('.inserted').children('.discount').addClass('discount2');
        $('.inserted').children('.total').addClass('total2');
        $('.inserted').children('.mechanics').addClass('mechanics2');
        $('.inserted').children('.status').addClass('status2');
        $('.inserted').children('.posID').addClass('posID2');
/***************************************************/

          },
        });
      } else {
        $real.trigger('set_item:PartPicker', item);
      }
      annotate_state();
    }

    function make_defined_state () {
      if (state == STATES.PICKED) {
        annotate_state();
        return true
      } else if (state == STATES.UNDEFINED && $dummy.val() == '')
        set_item({})
      else {
        last_unverified_dummy = $dummy.val();
        set_item({ id: last_real, name: last_dummy })
      }
      annotate_state();
    }

    function annotate_state () {
      if (state == STATES.PICKED)
        $dummy.removeClass(STATES.UNDEFINED).addClass(STATES.PICKED);
      else if (state == STATES.UNDEFINED && $dummy.val() == '')
        $dummy.removeClass(STATES.UNDEFINED).addClass(STATES.PICKED);
      else {
        last_unverified_dummy = $dummy.val();
        $dummy.addClass(STATES.UNDEFINED).removeClass(STATES.PICKED);
      }
    }

    function update_results () {
      $.ajax({
        url: '../../controller.pl?action=Part/part_picker_result',
        data: $.extend({
            'real_id': $real.val(),
        }, ajax_data(function(){ var val = $('#part_picker_filter').val(); return val === undefined ? '' : val })),
        success: function(data){ $('#part_picker_result').html(data) }
      });
    };

    function result_timer (event) {
      if (!$('no_paginate').prop('checked')) {
        if (event.keyCode == KEY.PAGE_UP) {
          $('#part_picker_result a.paginate-prev').click();
          return;
        }
        if (event.keyCode == KEY.PAGE_DOWN) {
          $('#part_picker_result a.paginate-next').click();
          return;
        }
      }
      window.clearTimeout(timer);
      timer = window.setTimeout(update_results, 100);
    }

    function close_popup() {
      $('#part_selection').dialog('close');
    };

    function handle_changed_text(callbacks) {
      $.ajax({
        url: '../../controller.pl?action=Part/ajax_autocomplete',
        dataType: "json",
        data: $.extend( ajax_data($dummy.val()), { prefer_exact: 1 } ),
        success: function (data) {
          if (data.length == 1) {
            set_item(data[0]);
            if (callbacks && callbacks.match_one) callbacks.match_one(data[0]);
          } else if (data.length > 1) {
            state = STATES.UNDEFINED;
            if (callbacks && callbacks.match_many) callbacks.match_many(data);
          } else {
            state = STATES.UNDEFINED;
            if (callbacks &&callbacks.match_none) callbacks.match_none();
          }
          annotate_state();
        }
      });
    };

    $dummy.autocomplete({
      source: function(req, rsp) {
        $.ajax($.extend(o, {
          url:      '../../controller.pl?action=Part/ajax_autocomplete',
          dataType: "json",
          data:     ajax_data(req.term),
          success:  function (data){ rsp(data) }
        }));
      },
      select: function(event, ui) {
        set_item(ui.item);
      },
    });
    /*  In case users are impatient and want to skip ahead:
     *  Capture <enter> key events and check if it's a unique hit.
     *  If it is, go ahead and assume it was selected. If it wasn't don't do
     *  anything so that autocompletion kicks in.  For <tab> don't prevent
     *  propagation. It would be nice to catch it, but javascript is too stupid
     *  to fire a tab event later on, so we'd have to reimplement the "find
     *  next active element in tabindex order and focus it".
     */
    /* note:
     *  event.which does not contain tab events in keypressed in firefox but will report 0
     *  chrome does not fire keypressed at all on tab or escape
     */
    $dummy.keydown(function(event){
      if (event.which == KEY.ENTER || event.which == KEY.TAB) {
        // if string is empty assume they want to delete
        if ($dummy.val() == '') {
          set_item({});
          return true;
        } else if (state == STATES.PICKED) {
          return true;
        }
        if (event.which == KEY.TAB) {
          event.preventDefault();
          handle_changed_text();
        }
        if (event.which == KEY.ENTER) {
          handle_changed_text({
            match_one:  function(){$('#update_button').click();},
            match_many: function(){open_dialog();}
          });
          return false;
        }
      } else {
        state = STATES.UNDEFINED;
      }
    });

    $dummy.on('paste', function(){
      setTimeout(function() {
        handle_changed_text();
      }, 1);
    });

    $dummy.blur(function(){
      window.clearTimeout(timer);
      timer = window.setTimeout(annotate_state, 100);
    });

    // now add a picker div after the original input
    var popup_button = $('<span>').addClass('ppp_popup_button');
    $dummy.after(popup_button);
    popup_button.click(open_dialog);

    var pp = {
      real:           function() { return $real },
      dummy:          function() { return $dummy },
      type:           function() { return $type },
      unit:           function() { return $unit },
      convertible_unit: function() { return $convertible_unit },
      update_results: update_results,
      result_timer:   result_timer,
      set_item:       set_item,
      reset:          make_defined_state,
      is_defined_state: function() { return state == STATES.PICKED },
      init_results:    function () {
        $('div.part_picker_part').each(function(){
          $(this).click(function(){
            set_item({
              id:   $(this).children('input.part_picker_id').val(),
              name: $(this).children('input.part_picker_description').val(),
              unit: $(this).children('input.part_picker_unit').val(),
              partnumber:  $(this).children('input.part_picker_partnumber').val(),
              description: $(this).children('input.part_picker_description').val(),
            });
            close_popup();
            $dummy.focus();
            return true;
          });
        });
        $('#part_selection').keydown(function(e){
           if (e.which == KEY.ESCAPE) {
             close_popup();
             $dummy.focus();
           }
        });
      }
    }
    $real.data('part_picker', pp);
    return pp;
  }
});

$(function(){
  $('input.part_autocomplete').each(function(i,real){
    kivi.PartPicker($(real));
  })
});
