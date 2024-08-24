// jQuery.noConflict();
jQuery(document).ready(function () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    var page;
    var order;
    var order_by;
    var type;
    var query;
    var limit;
    var filter;
    var category;

    //Padrão Carregamento inicial pagina 1 oren update date
    load_data(true, 1, order);
    lnd_update_catalog(order, type);

    // Metodo resposavel pela paginação
    jQuery(document).on('click', '.page-link', function () {
        page = jQuery(this).data('page_number');
        load_data(true, page, order, type, query, order_by, limit, filter, category);
    });

    //Ordenação por tipo plugin ou tema
    jQuery("input[name=lnd-radio-type]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        type = jQuery("input[name=lnd-radio-type]:checked").val();
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    /**
     * Metodo responsavel pela ordenação all, free, installed
     */
    jQuery("input[name=lnd-radio-control]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        filter = jQuery("input[name=lnd-radio-control]:checked").data('order_data');
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    //Ordenação update ou name
    jQuery("input[name=lnd-radio-order]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        order = jQuery("input[name=lnd-radio-order]:checked").val();
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    //Ordenação maior menor
    jQuery("input[name=lnd-radio-order-by]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        order_by = jQuery("input[name=lnd-radio-order-by]:checked").val();
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    /**
     * Metodo resonsavel pela pesquisa
     */
    var typingTimer;
    jQuery('#lnd-search-box').on('input', function (event) {
         //timer identifier
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            query = jQuery('#lnd-search-box').val();
            load_data(true, 1, order, type, query, order_by, limit, filter, category);
        }, 1000)
    });

    /**
     * metod responsavel pela quantidade no grid
     */
    jQuery("#lnd-form-select-grid").on("change", "option", function (e) {
    }).bind("change", "option", function (e) {
        limit = jQuery("#lnd-form-select-grid").val();
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    /**
     * reset filters
     */
    jQuery('#lnd-reset-filters').on('click', function (e) {
        jQuery("#lnd-order-desc").prop('checked', true);
        jQuery("#lnd-radio-type-plugin").prop('checked', false);
        jQuery("#lnd-radio-type-theme").prop('checked', false);
        jQuery("#lnd-radio-control-all").prop('checked', true);
        jQuery("#lnd-order-update").prop('checked', true);
        jQuery('#lnd-search-box').val(null);
        jQuery("#lnd-form-select-grid").val('30').prop('selected', true);
        jQuery("#lnd-form-select-category").val('').prop('selected', true);
        page        = undefined;
        order       = undefined;
        order_by    = undefined;
        type        = undefined;
        query       = undefined;
        limit       = undefined;
        filter      = undefined;
        category    = undefined;
        load_data(true, 1, 'update_date');
    });

    /**
     * Metodo responsavel por chamar categorias
     */
    jQuery("#lnd-form-select-category").on("change", "option", function (e) {
    }).bind("change", "option", function (e) {
        category = jQuery("#lnd-form-select-category").val();
        load_data(true, 1, order, type, query, order_by, limit, filter, category);
    });

    /**
     * Metodo responsavel pela instalação de temas e plugins
     */
    jQuery(document).on('click', '#lnd-install', function () {
        var name = jQuery(this).data('lnd_name');
        var itens = jQuery(this).data('lnd_itens');
        var type = jQuery(this).data('lnd_type');
        var version = jQuery(this).data('lnd_version');
        var footer = jQuery(`.lnd-footer-card-jQuery{name}`);
        var btn = jQuery(this);
        var data = { action: 'lnd_install_itens', _ajax_nonce: ajax_var.nonce, type: type, name: name, version: version, itens: itens };
        jQuery.ajax({
            url: ajax_var.url,
            method: "POST",
            data: data,
            beforeSend: function () {
                jQuery(btn).buttonLoader('start');
                jQuery('.lnd-footer-card').addClass('lnd-alert-card')
            },
            success: function (data) {
                // console.log(data.status)
                const menssage = data['msg'];

                if (data.status === true) {
                    // footer.messageLoader(true)
                    footer.html(`<div class="alerts"><div class="lnd-icon-success"><i class="fa fa-check"></i></div><strong>Success!</strong> jQuery{menssage}</div>`);
                }
                if ( data.status === false) {

                    footer.html(`<div class="alerts"><div class="lnd-icon-danger"><i class="fa fa-times-circle"></i></div><strong>Error!</strong> jQuery{menssage}</div>`);
                }            

            }, complete: function () {
                setTimeout(function () {
                    jQuery(btn).buttonLoader('stop');
                }, 5000);
                load_data(true, page, order, type, query, order_by, limit, filter, category);
            }
        });
    });

    /**
     * Metodo responsavel por ativar tema e plugins
     */
    jQuery(document).on('click', '#lnd-btn-activate', function () {
        var type = jQuery(this).data('lnd_type');
        var itens = jQuery(this).data('lnd_itens');
        var filepath = jQuery(this).data('lnd_filepath');
        var name = jQuery(this).data('lnd_name');
        var btn = jQuery(this);
        var data = { action: 'lnd_activate_itens',_ajax_nonce: ajax_var.nonce, type: type, name: name, filepath: filepath, itens: itens };
        console.log(filepath)
        jQuery.ajax({
            url: ajax_var.url,
            method: "POST",
            data: data,
            beforeSend: function () {
                jQuery(btn).buttonLoader('start');
            },
            success: function (data) {
                const menssage = data['msg'];
                jQuery(`.lnd-footer-card-jQuery{name}`).html(`<div class="alerts"><div class="lnd-icon-success"><i class="fa fa-check"></i></div><strong>Success!</strong> jQuery{menssage}</div>`);
            }, complete: function () {
                setTimeout(function () {
                    jQuery(btn).buttonLoader('stop');
                }, 5000);
                if (type == 'theme') {
                    location.reload()
                }
                load_data(true, page, order, type, query, order_by, limit, filter, category);
            }
        });

    });

    /**
     * Metodo responsavel pela atualizaçãod de plugins e temas
     */
    jQuery(document).on('click', '#lnd-btn-update', function () {
        var page = jQuery("#page-number").data('page_active');
        var order = jQuery("input[name=lnd-radio-order]:checked").data('order_data');
        var type = jQuery(this).data('lnd_type');
        var query = jQuery('#lnd-search-box').val();
        var order_by = jQuery("input[name=lnd-radio-order-by]:checked").val();
        var limit = jQuery("#lnd-form-select-grid").val();
        var filter = jQuery("input[name=lnd-radio-control]:checked").data('order_data');
        var category = jQuery("#lnd-form-select-category").val();
        var itens = jQuery(this).data('lnd_itens');
        var name = jQuery(this).data('lnd_name');
        var version = jQuery(this).data('lnd_version');
        var btn = jQuery(this);
        var data = { action: 'lnd_update_itens',_ajax_nonce: ajax_var.nonce, type: type, lnd_name: name, lnd_version: version, itens: itens };
        jQuery.ajax({
            url: ajax_var.url,
            method: "POST",
            data: data,
            beforeSend: function () {
                jQuery(btn).buttonLoader('start');
            },
            success: function (data) {
                const menssage = data['msg'];
                jQuery(`.lnd-footer-card-jQuery{name}`).html(`<div class="alerts"><div class="lnd-icon-success"><i class="fa fa-check"></i></div><strong>Success!</strong> jQuery{menssage}</div>`);
            }, complete: function () {
                setTimeout(function () {
                    jQuery(btn).buttonLoader('stop');
                }, 5000);
                load_data(true, page, order, type, query, order_by, limit, filter, category);
            }
        });
    });

    //function spinner
    (function (jQuery) {
        jQuery.fn.buttonLoader = function (action) {
            var self = jQuery(this);
            //start loading animation
            if (action == 'start') {
                if (jQuery(self).attr("disabled") == "disabled") {
                    e.preventDefault();
                }
                jQuery(self).attr('data-btn-text', jQuery(self).text());
                jQuery(self).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                jQuery(self).addClass('active');
            }
            //stop loading animation
            if (action == 'stop') {
                jQuery(self).html(jQuery(self).attr('data-btn-text'));
                jQuery(self).removeClass('active');
            }
        }
    })(jQuery);


});

/**
 * 
 * @param {any} loadiv 
 * @param {int} page 
 * @param {string} order 
 * @param {string} type 
 * @param {string} query 
 * @param {string} order_by 
 * @param {int} limit 
 * @param {string} filter 
 * @param {string} category 
 */
function load_data(loadiv, page, order = '', type = '', query = '', order_by = '', limit = '', filter = '', category = '') {
    var load_div = jQuery(".ajax_load");
    var data = { action: 'lnd_get_catalog_itens', _ajax_nonce: ajax_var.nonce, page: page, order: order, type: type, query: query, order_by: order_by, limit: limit, filter: filter, category: category };
    jQuery.ajax({
        url: ajax_var.url,
        method: "POST",
        data: data,
        beforeSend: function () {
            if (loadiv == true) {
                load_div.fadeIn().css("display", "flex");
            }
        },
        success: function (data) {
            jQuery('#lnd-post-grid').html(data);
        }, complete: function () {
            if (loadiv == true) {
                load_div.fadeOut();
            }
        }
    });
}

/**
 * Metodo responsavel por atualizar o catalogo
 * @param {string} order 
 * @param {string} type 
 */
function lnd_update_catalog(order, type) {
    var url = ajax_var.url;
    var data = {  action: 'lnd_update_catalog_ajax', _ajax_nonce: ajax_var.nonce };
    jQuery('#lnd-update-catalogo').on("click", function (e) {
        e.preventDefault();
        var btn = jQuery(this);
        jQuery.ajax({
            url: url,
            method: 'post',
            data: data,
            datatype: 'json',
            beforeSend: function () {
                jQuery(btn).buttonLoader('start');
            },
            success: function (result) {

                action_message(result.status, result.msg);
            },
            complete: function () {
                setTimeout(function () {
                    jQuery(btn).buttonLoader('stop');
                }, 2000);
                load_data(true, 1, order, type);
            }
        });
    });
} 
function action_message(status, msg) {
    const message = jQuery('#alert-menssage');

    if (status == true) {
        message.html('<div class="lnd-alerts lnd-alerts-success lnd-alerts-white lnd-rounded"> <div class="icon"><i class="fa fa-check"></i></div> <strong>Success!</strong> ' + msg + '!</div>' );
    } 

    if (status == false) {
        message.html( '<div class="lnd-alerts lnd-alerts-warning lnd-alerts-white lnd-rounded"><div class="icon"><i class="fa fa-warning"></i></div><strong>Alert!</strong> ' + msg + '!</div>' );
    }
    window.setTimeout(function () {
        jQuery('.lnd-alerts').fadeTo(100, 0).slideUp(1500, function () {
            jQuery('.lnd-alerts').remove();
        });
    }, 7000);
}



