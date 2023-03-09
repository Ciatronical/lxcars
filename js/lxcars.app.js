$(document).ready(function()
{
        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function(ul,items) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul,item);
                });
             }
         });

        $(function() {
            $("#lxc-id-fast-search").catcomplete({
                source: "ajax/lxcars.app.php?action=fastSearch",
                select: function(e,ui) {
                    console.log(ui.item.src + "/" + ui.item.id);
                }
            });
        });
});

