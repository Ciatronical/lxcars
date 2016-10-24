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
//console.log(rsp);

        $('.newOrderPos').children('.itemNr').val(rsp.partnumber);
        $('.newOrderPos').children().children('.description').val(rsp.description);
        $('.newOrderPos').children('.unity').val(rsp.unit);
        $('.newOrderPos').children('.price').val(rsp.sellprice);
        $('.newOrderPos').children('.discount').val(rsp.not_discountable);
        $('.newOrderPos').children('.partID').text(rsp.id);

$('.orderPos').removeClass('oP');
$('.newOrderPos').clone().insertBefore('.newOrderPos').removeClass('newOrderPos').addClass('orderPos oP');

$('<input name="pos_description" type="text" class="ui-widget-content ui-corner-all description oPdescription elem">').insertAfter($('.oP').children('.itemNr'));
$('<input name="pos_unit" type="text" class="ui-widget-content ui-corner-all unity oPunity elem" autocomplete="off">').insertAfter($('.oP').children('.description'));
//oPunity

$('.oP').children('.oPdescription').val(rsp.description);
$('.oP').children('.oPunity').val(rsp.unit);

//$('.op').children('.posID').text($('.newOrderPos').children('.posID').text());

$('.orderPos').children('.part_picker').remove();
$('.orderPos').children('.nPunity').remove();

        $('.newOrderPos').children('.itemNr').val('');
        $('.newOrderPos').children().children('#add_item_parts_id_name').val('');
        $('.newOrderPos').children('.unity').val('');
        $('.newOrderPos').children('.price').val('');
        $('.newOrderPos').children('.discount').val('');
        $('.newOrderPos').children('.partID').text('');
        $('.newOrderPos').children('.posID').text('');

        zaehler();

        $('.orderPos').children('.description').addClass('description2');
        $('.orderPos').children('.unity').addClass('unity2');
        $('.orderPos').children('.number').addClass('number2');
        $('.orderPos').children('.price').addClass('price2');
        $('.orderPos').children('.discount').addClass('discount2');
        $('.orderPos').children('.total').addClass('total2');
        $('.orderPos').children('.mechanics').addClass('mechanics2');
        $('.orderPos').children('.status').addClass('status2');
        $('.orderPos').children('.posID').addClass('posID2');
        $('.orderPos').children('.partID').addClass('partID2');

        updateDatabase();

        $('.elem').change(function (e) {
            updateDatabase();
        }).on( 'keyup', function(){
            //Wird benötigt um der ".elem" für das INSERT das Value zu zuweisen
            var y = $(this).val();
            $(this).attr('value', y);
            updateDatabase();
        } )

        $( '.number' ).on( 'keyup', function () {
            var x = $( this ).val();
            var y = $( this ).parent( '.orderPos' ).children( '.price' ).val();
            //var z = ( x * y );
            $(this ).parent( '.orderPos' ).children( '.total' ).val( x * y );
            //console.log( z );
        } )

        $( '.discount' ).on( 'keyup', function () {
            var number = $( this ).parent( '.orderPos' ).children( '.number' ).val();
            var price = $( this ).parent( '.orderPos' ).children( '.price' ).val();
            var discount = ( 1 - ($( this ).val() / 100) );
            //var z = ( (number * price) * (1 - discount) )
            $( this ).parent( '.orderPos' ).children( '.total' ).val( ( number * price ) * discount );
            //console.log( z );
        } )

        $( '.rmv' ).click( function (){
            //var posToDel = {};
            var posToDel = $(this).parent().children('.posID').text();
            //console.log(posToDel);
            //console.log('remove');

                $.ajax({
                    url: 'ajax/order.php',
                    data: { action: "delPosition", data: posToDel },
                    type: "POST",
                    success: function(){
                        //alert('Gesendet');
                    },
                    error:   function(){
                        alert('löschen der Daten fehlgeschlagen!');
                    }
                });

            $( this ).parent().remove();
            zaehler();
            updateDatabase();
        });

/***************************************************/

          },
        });
      } else {
        $real.trigger('set_item:PartPicker', item);
      }
      annotate_state();
    }

/***************************************************/

    function zaehler() {
        $( '.positions' ).each( function( cnt, list ){
            $( list ).attr( 'id', 'pos__' + ( cnt + 1 ) ).children( '.pos' ).val( ( cnt + 1 ) ).attr('value', (cnt + 1));
            //For-Schleife um in den einzelnen ListenElementen die Felder
            //durch zu zählen und zu nummerieren
            var elements = $( this ).children( '.elem' );
            for( var i = 0; i < elements.length; i++ ){
                elements[i].id = $( list ).attr( 'id' ) + '__elem__' + ( i + 1 );
            }
        })
    }

    function updateDatabase() {
                clearTimeout( timer );
                timer = setTimeout( function(){   //calls click event after a certain time
                    var updateDataJSON = new Array;
                    $( 'ul#sortable > li' ).each( function(){
                        updateDataJSON.push({
                            //"Bezeichnung des Arrays": Inhalt der zu Speichern ist
                            "order_nr": $( this ).children( '.pos' ).val(),
                            "item_nr": $( this ).children( '.itemNr' ).val(),
                            "pos_description": $( this ).children( '.description' ).val(),
                            "pos_unit": $( this ).children( '.unity' ).val(),
                            "pos_qty": $( this ).children( '.number' ).val(),
                            "pos_price": $( this ).children( '.price' ).val(),
                            "pos_discount": $( this ).children( '.discount' ).val(),
                            "pos_total": $( this ).children( '.total' ).val(),
                            "pos_emp": $( this ).children( '.mechanics' ).val(),
                            "pos_status": $( this ).children( '.status' ).val(),
                            "pos_id": $( this ).children( '.posID' ).text(),
                            "partID": $( this ).children( '.partID' ).text()
                        });
                    })

                    updateDataJSON.pop();
                    //console.log(updateDataJSON);

                    $.ajax({
                        url: 'ajax/order.php',
                        //data: { action: "updatePositions", data: JSON.stringify(updateDataJSON)},
                        data: { action: "updatePositions", data: updateDataJSON },
                        type: "POST",
                            success: function(){
                                //alert( 'send all posdata' );
                            },
                            error:  function(){
                                alert( 'error sending posdata' );
                            }
                    });


                }, 800 );
    }

/***************************************************/

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
