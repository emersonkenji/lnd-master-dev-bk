
jQuery(document).ready(function () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    var page;
    var order;
    var order_by;
    var type ;
    var query;
    var limit;
    var filter;
    var category;
    var plans;

    // plans = (jQuery("input[name=lnd-radio-plans]:checked").val());
    plans = jQuery('.lnd-button-radio.active').data('order_data');
    var additionalTitle = jQuery('.lnd-button-radio.active').data('button_label'); // Substitua pelo título adicional desejado

    jQuery('.lnd-button-radio.active').find(".lnd-span-radio").stop().fadeOut(200, function () {
        jQuery(this).text(additionalTitle).fadeIn(200);
    });
    
    
    //Padrão Carregamento inicial pagina 1 oren update date
    load_data(true, page, order,true, type, query, order_by, limit, filter, category, plans);

    // Metodo resposavel pela paginação
    jQuery(document).on('click', '.page-link', function () {
        page = jQuery(this).data('page_number');
        load_data(true, page, order,false, type, query, order_by, limit, filter, category, plans);
    });

    //Ordenação por tipo plugin ou tema
    jQuery("input[name=lnd-radio-type]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        type = jQuery("input[name=lnd-radio-type]:checked").val();
        load_data(true, 1, order,false, type, query, order_by, limit, filter, category, plans );
    });

    /**
     * Metodo responsavel pela ordenação all, free, installed
     */
    jQuery("input[name=lnd-radio-control]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        filter = jQuery("input[name=lnd-radio-control]:checked").data('order_data');
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
    });

    //Ordenação update ou name
    jQuery("input[name=lnd-radio-order]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        order = jQuery("input[name=lnd-radio-order]:checked").val();
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
    });

    //Ordenação maior menor
    jQuery("input[name=lnd-radio-order-by]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        order_by = jQuery("input[name=lnd-radio-order-by]:checked").val();
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
    });

    // jQuery('.lnd-button-radio').click(function(e) {
    //     var plans = jQuery(this).data('order_data');
    //     console.log(selectedPlan);
    //     load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
        
    //     // Restante do código aqui...
    //     // Você pode usar a variável "selectedPlan" para fazer o que desejar com os dados do botão selecionado.
    //   });

    //Ordenação planos
    jQuery("input[name=lnd-radio-plans]").on("change", "[data-order_data]", function (e) {
    }).bind("change", "[data-order_data]", function (e) {
        plans = jQuery("input[name=lnd-radio-plans]:checked").val();
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
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
            load_data(true, 1, order,false, type, query, order_by, limit, filter, category, plans);
        }, 1000)
    });

    /**
     * metod responsavel pela quantidade no grid
     */
    jQuery("#lnd-form-select-grid").on("change", "option", function (e) {
    }).bind("change", "option", function (e) {
        limit = jQuery("#lnd-form-select-grid").val();
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
    });

    /**
     * reset filters
     */
    jQuery('#lnd-reset-filters').on('click', function (e) {
        jQuery("#lnd-order-desc").prop('checked', true);
        jQuery("#lnd-radio-type-plugin").prop('checked', false);
        jQuery("#lnd-radio-type-theme").prop('checked', false);
        jQuery("input[name=lnd-radio-plans]").prop('checked', false);
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
        // plans       = undefined;
        load_data(true, 1, 'update_date');
    });


    // jQuery("input[id=lnd-reset-filters]").hover(function() {
    //     jQuery(this).attr('title', 'This is a hover text.');
    // });

    jQuery("#lnd-reset-filters").hover(function() {
        jQuery(this).css('cursor','pointer').attr('title', 'Limpar filtros.');
    }, function() {
        jQuery(this).css('cursor','auto');
    });


    /**
     * Metodo responsavel por chamar categorias
     */
    jQuery("#lnd-form-select-category").on("change", "option", function (e) {
    }).bind("change", "option", function (e) {
        category = jQuery("#lnd-form-select-category").val();
        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);
    });

    /**
     * Metodo responsavel pela mudança de planos
     */
    jQuery('.lnd-button-radio').click(function(e) {
        e.preventDefault();
        plans = jQuery(this).data('order_data');

        load_data(true, 1, order, false, type, query, order_by, limit, filter, category, plans);

        jQuery('.lnd-button-radio').removeClass('active').each(function() {
            jQuery(this).find('.lnd-span-radio').stop().fadeOut(200, function() {
                jQuery(this).text('');
            });
        });
        jQuery(this).addClass('active');
        var additionalTitle = jQuery(this).data('button_label'); // Substitua pelo título adicional desejado

        jQuery(this).find('.lnd-span-radio').stop().fadeOut(200, function() {
            jQuery(this).text(additionalTitle).fadeIn(200);
        });
    });


    /**
     * Metodo responsavel por colocar texto a frente do icone dos botoes de planos
     */
    jQuery('.lnd-button-radio').hover(
        function() {
          var buttonId = jQuery(this).attr('id');
          var additionalTitle = jQuery(this).data('button_label');
          
          if (!jQuery('#' + buttonId).hasClass('active')) {
            jQuery(this).find('.lnd-span-radio').stop().fadeOut(200, function() {
                jQuery(this).addClass(buttonId)
              jQuery('.' + buttonId).css('display', 'inline').text(additionalTitle).animate({
                opacity: 1
              }, 1000);
            });
          }
        },
        function() {
          var buttonId = jQuery(this).attr('id');
          if (!jQuery('#' + buttonId).hasClass('active')) {
            jQuery(this).find('.lnd-span-radio').stop().animate({
              opacity: 0
            }, 800, function() {
              jQuery(this).text('').animate({
                opacity: 1
              }, 800);
            });
          }
        }
      );
      

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
function load_data( loadiv, page, order = '', placeholders = true, type = '', query = '', order_by = '', limit = '', filter = '', category = '' , plans ='') {
    var load_div = jQuery(".ajax_load");
    var data = { action: 'lnd_get_catalog_itens_plataforma', _ajax_nonce: ajax_var.nonce, page: page, order: order, type: type, query: query, order_by: order_by, limit: limit, filter: filter, category: category, plans: plans };
    // console.log(data);
    jQuery.ajax({
        url: ajax_var.url,
        method: "POST",
        data: data,
        
        beforeSend: function () {
            if (placeholders == true) {
                placeholder();
            }
            
            if (loadiv == true) {
                load_div.fadeIn().css("display", "flex");
            }
        },
        success: function (data) {
            // console.log(data);
            // placeholder();
            jQuery('#lnd-post-grid').html(data);
        }, complete: function () {
            if (loadiv == true) {
                load_div.fadeOut();
            }
        }
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

function placeholder() {
    var load_div = jQuery('#lnd-post-grid');
    var pagination = `<div class="row"><div class="col-3 align-self-end"><p class="justify-content-start fs-6 fw-bolder">Total: 1 </p></div><div class="col mt-2"><ul class="pagination justify-content-end"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item disabled"><a class="page-link" href="#" id="page-number" data-page_active="1">1 <span class="sr-only"></span></a></li><li class="page-item disabled"><a class="page-link" href="#">Next</a></li></ul></div></div>`;
    var init = `<div class="row justify-content-between my-4 " id="lnd-post-grid">`;
    var placeholder = [];

    
    placeholder.push(`<div class="card h-100 shadow p-1 m-1  lnd-card-grid-skeleton" aria-hidden="true">`);
    placeholder.push(`<img src="https://planos.lojanegociosdigital.com.br/wp-content/uploads/2023/02/fundo-placehouder.jpg" class="card-img-top placeholder" alt="...">`);
    placeholder.push(`<div class="card-body">`);
    placeholder.push(`<h5 class="card-title placeholder-glow"><span class="placeholder col-6"></span></h5>`);
    placeholder.push(`<p class="card-text placeholder-glow">`);
    placeholder.push(`<span class="placeholder col-6"></span>`);
    placeholder.push(`<span class="placeholder col-9"></span>`);
    placeholder.push(`<span class="placeholder col-8"></span>`);
    placeholder.push(`<span class="placeholder col-12"></span>`);
    placeholder.push(`<span class="placeholder col-6"></span>`);
    placeholder.push(`</p>`);
    placeholder.push(`<a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-12"></a>`);
    placeholder.push(`</div>`);
    placeholder.push(`</div>`);

    var end = `</div>`;
    var place = "";
    var i = 0;
    while (i < 15) {
        place += placeholder.join('');
        i++;
    }                       
    load_div.html(pagination + init + place + end);           
          
}



