"use strict";

jQuery(function($) {
    class CardComInt extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    form: '.elementor-form'
                }
            };
        }
        getDefaultElements() {
            var selectors = this.getSettings('selectors'),
                elements = {};
            elements.$form = this.$element.find(selectors.form);
            return elements;
        }
        bindEvents() {
            this.elements.$form.on('form_destruct', this.handleSubmit);
        }
        handleSubmit(event, response) {
            if ('undefined' !== typeof response.data.redirect_url) {
                location.href = response.data.redirect_url;
            }
            else if ('undefined' !== typeof response.data.url) {
                $(this).html(`<iframe style="min-height:100vh" src="${response.data.url}"></iframe>`);
            }
        }

    }
    $( window ).on( 'elementor/frontend/init', () => {
       const CardComIntHandler = ( $element ) => {
           elementorFrontend.elementsHandler.addHandler( CardComInt, {
               $element,
           } );
       };
       elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', CardComIntHandler );
    } );
})