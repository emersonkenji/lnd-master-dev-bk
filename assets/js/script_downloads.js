jQuery(document).ready(function () {
    
    jQuery(document).ready(function() {
        var selectedIds = []; // lista de ids selecionados individualmente
        var allIds = []; // lista de todos os ids das caixas de seleção

       jQuery('input[name="element[]"]').on("change", function() {
            if (jQuery(this).is(":checked")) {
                selectedIds.push(jQuery(this).val());
            } else {
                var index = selectedIds.indexOf(jQuery(this).val());
                if (index > -1) {
                    selectedIds.splice(index, 1);
                }
            }
           jQuery('input[name="elements-ids[]"]').val(selectedIds);
           jQuery('p[id="p_create_product_multiple"]').val(selectedIds.join(','));
            console.log(selectedIds);
        });

       jQuery('input[id="cb-select-all-1"]').on("change", function() {
            allIds =jQuery('input[name="element[]"]').map(function() {
                return jQuery(this).val();
            }).get();

            if (jQuery(this).is(":checked")) {
                selectedIds = allIds;
            } else {
                selectedIds = [];
            }
           jQuery('input[name="elements-ids[]"]').val(selectedIds);
        });
    });
});
