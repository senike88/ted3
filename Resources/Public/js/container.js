(function ($) {
    $.widget("ted3.container", {
        // Default options.
        options: {
            addzone: false,
            allowOnlyEmptyAddzone: 0
        },
        _create: function () {
            var that = this;
            this.buttons = {};
            //var firstElement = this.element.find('.ted-element').eq(0);
            this._setOption('addzone', $.extend(false, this.options.addzone, this.element.data('settings').addzone));
            if (this.element.data('settings').allowOnlyEmptyAddzone == 1) {
                this._setOption('allowOnlyEmptyAddzone', 1);
            }

            this.element.addClass('ted3-container');
            this.type = this.element.data('container');
            this.api = Ted3.api[this.type];

            if (this.options.addzone != false) {
                this._createAddzone();
            }

        },
        _init: function () {
            var that = this;
            this.elements = this.getElements();
            if (this.elements) {
                // Init Elements
                this.elements.element();
            }
            this._initAddzone();
        },
        _initAddzone: function () {
            var that = this;
            this.addzonediv.detach();

            if (that.elements) {
                if (!this.options.allowOnlyEmptyAddzone) {
                    this.addzonediv.prependTo(this.element);
                }
            } else { // Empty-Container
                this.element.addClass('ted3-container-empty');
                this.addzonediv.prependTo(this.element);
            }

        },
        reinitAddzone: function () {
            var that = this;
            // console.log("reinit addzone container");
            this.elements = this.getElements();
            this.addzonediv.detach();
            if (that.elements) {
                if (!this.options.allowOnlyEmptyAddzone) {
                    this.addzonediv.prependTo(this.element);
                }
            } else { // Empty-Container
                this.element.addClass('ted3-container-empty');
                this.addzonediv.prependTo(this.element);
            }
        },
        _createAddzone: function () {
            var that = this;
            this.addzonediv = $('<div />', {
                class: 'ted3-addzone'
            }).addzone({
                settings: that.options.addzone,
                createRecord: function (e, newdata) {
                    that.api.new(that.element).done(function (data) {
                        Ted3.reload();
                    });
                },
                receiveNew: function (e, newdata) {
                    var addzone = $(this);
//                    alert("container");
                    that.api.new(that.element, newdata).done(function (data) {
                        if (that.element.closest('[data-forcereload="1"]').length > 0 || that.element.hasClass('ted3-element-slidecontainer')) {
                            Ted3.reload();
                            return false;
                        }
                        var data = JSON.parse(data);
                        Ted3.load('div[data-element="' + that.type + '"][data-uid="' + data.newuid + '"]', function (newelement) {
                            that.addzonediv.after(newelement);
                            newelement.element();
                            setTimeout(function(){
                                newelement.element('select');
                            },300);
                            
                            addzone.addzone('endLoading');
                            that.reinitAddzone();
                        });
                    });
                },
                receiveElement: function (e, element) {
                    var addzone = $(this);

                    that.addzonediv.after(element);
                    element.element(); //reinit
                    that.api.move(that.element, element).done(function (data) {
                        //Ted3.log("Element moved to dropzone");
                        addzone.addzone('endLoading');
                        if (that.element.closest('[data-forcereload="1"]').length > 0 || that.element.hasClass('ted3-element-slidecontainer')) {
                            Ted3.reload();
                            return false;
                        } else {
                            that.reinitAddzone();
                        }
                    });
                },
                receiveFromClipboard: function () {
                    var addzone = $(this);
                    var elementData = Ted3.root.ted3root('getFromClipboard');
                   
                    var tmpElement = $('<div />').data(elementData);
                    that.api[elementData.mode](that.element, tmpElement).done(function (data) {
                        var data = JSON.parse(data);
                        if (elementData.mode == "move") {
                            data.newuid = elementData.uid;
                            $('[data-element][data-uid="' + data.newuid + '"]').remove();
                        }
                        if (that.element.closest('[data-forcereload="1"]').length > 0 || that.element.hasClass('ted3-element-slidecontainer')) {
                            Ted3.reload();
                        } else {
                            Ted3.load('div[data-uid="' + data.newuid + '"]', function (newelement) {
                                if (elementData.mode == "move") {
                                    Ted3.root.find('[data-element][data-uid="' + data.newuid + '"]');
                                }

                                that.addzonediv.after(newelement);
                                newelement.element();
                                addzone.addzone('endLoading');
                            });
                        }
                    });

                    tmpElement = null;
                }
            });
            if (this.element.data('name')) {
                this.addzonediv.prepend('<div class="ted3-addzone-name">' + this.element.data('name') + '</div>');
            }


            Ted3.root.on('ted3rootaddzone', function (e, active) {
                if (active) {
                    that._initAddzone();
                } else {
                    that.addzonediv.detach();

                }
            });
        },

        getElements: function (sel) {
            var selector = sel || '[data-element="' + this.type + '"]';
            if (this.element.find(selector).length > 0) {
                if (this.element.find(selector).eq(0).closest('[data-container]')[0] == this.element[0]) {
                    return this.element.find(selector).eq(0).parent().children(selector);
                }
            }
            return false;

        }

    });
}(Ted3.jQuery));