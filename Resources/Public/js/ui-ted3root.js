(function ($) {
    $.widget("ted3.ted3root", {
        _create: function () {
            var that = this;

            // Initialize Containers
            Ted3.root = this.element;
            this.element.addClass('ted3-root');

            //Ted3-Panel
            this.panel = $('#ted3-panel');

            if (Ted3.fedata.origDoktype == 4) {
                Ted3.root.addClass('ted3-shortcutpage');
            }

            //  Mainbar
            this.outerMainbar = $('#ted3-mainbarouter');
            this.outerMainbar.draggable({
                start: function (e, ui) {
                    that.outerMainbar.css('right', 'auto');
                },
                stop: function (e, ui) {
                    Ted3.memory.set('ted3-mainbarpos', ui.position);
                }
            });
            //alert(Ted3.fedata.currentlangId);
            Ted3.root.attr('data-currentLangId', Ted3.fedata.currentlangId);
            if (Ted3.fedata.deflang.uid == Ted3.fedata.currentlangId) {
                Ted3.root.attr('data-isDefaultLang', 1);
            } else {
                Ted3.root.attr('data-isDefaultLang', 0);
            }

            if (Ted3.fedata.langFallbackType) {
                Ted3.root.attr('data-langFallbackType', Ted3.fedata.langFallbackType);
            }

            // Init Containers
            this.element.find('[data-container][data-parent="0"]:not([data-container="noteditable"])').container();
            //Other Rootcontainer
            var possibleRestRootcontainers = this.element.find('[data-container]:not([data-container="noteditable"]):not(.ted3-container)');
            possibleRestRootcontainers.each(function () {
                if ($(this).parent().closest('[data-container]').length < 1) {
                    $(this).container();
                }
            });

            // If no conatiners in use
            $('[data-element]').each(function () {
                var element = $(this);
                if (element.closest('[data-container]').length < 1) {
                    console.log("no container");
                    element.element();
                }
            });


            $('.ted3-btn-preview-toggle').on('click', function () {
                if (!that.hasMode('preview')) {
                    that.previewMode();
                    that.moveToView(true);
                } else {
                    that.editMode();
                    that.moveToView();
                }

            });

            //Set Key-UI
            this.keydown = false;
            this._on(this.element, {
                keydown: function (e) {
                    if (e.keyCode && e.keyCode === 17 && !that.element.hasClass('ted3-mode-writing')) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (!that.keydown) {
                            that.keydown = true;
                            this.element.addClass('ted3-mode-noconflict');
                            return;
                        }
                    }
                },
                keyup: function (e) {
                    this.keydown = false;
                    if (e.keyCode === 17 && !that.element.hasClass('ted3-mode-writing')) {
                        e.preventDefault();
                        e.stopPropagation();
                        that.element.removeClass('ted3-mode-noconflict');
                        return false;
                    }
                }
            });


            //Donate
            /*
             if(Ted3.memory.get('ted3-donate-banner-hidex')){
             $('#ted3-donate #ted3-donate-inner').hide();
             $('#ted3-donate #ted3-donate-close').hide();
             }
             */
            $('#ted3-donate').on('click', function () {
                $('#ted3-donate-window').show();
            });

            $('#ted3-donate-close').on('click', function (e) {
                e.stopPropagation();
                $('#ted3-donate').hide();
                Ted3.memory.set("ted3-donate-banner-hidex", true);
            });
            $('#ted3-donate-windowclose').on('click', function (e) {
                e.stopPropagation();
                $('#ted3-donate-window').hide();
            });
            

            $('#ted3-attention-close').on('click', function (e) {
                e.stopPropagation();
                $('#ted3-attention').hide();
                sessionStorage.setItem("ted3-attention-hide", true);
            });

            if (sessionStorage.getItem("ted3-attention-hide")) {
                $('#ted3-attention').hide();
            }




            $('.ted3-btn-page').on('click', function () {

                var link = {
                    url: Ted3.urls.t3edit + 'pages&uid=' + Ted3.fedata.pid + '&returnUrl=' + encodeURIComponent(location.href),
                    title: "Edit page",
                    options: "width=810,height=600,top=50,left=100"
                };
                // console.log(link.url);
                if (Ted3.fedata.currentlangId > 0) {
                    var pageRecordUid = $(this).data('pagerecord');
                    link.url = Ted3.urls.t3edit + 'pages&uid=' + pageRecordUid + '&returnUrl=' + encodeURIComponent(location.href);
                    //  console.log(link);


                }
                //link.url = "http://t3install10.zimmermann2019.web-crossing.com/typo3/index.php?route=%2Frecord%2Fedit&token=63652dc6facbd84723043b1f849102a717a32544&edit%5Bpages%5D%5B27%5D=edit&returnUrl=%2Findex.php%3Fid%3D1%26type%3D4457";


                location.href = link.url;

            });

            $('body > *').on("click", "a[href*='t3:']", function (e) {

                e.preventDefault();
                // alert("dagf");
                // console.log("t3-link click event");
                var link = $(this).attr('href');
                //console.log(link);

                Ted3.ajax({
                    url: Ted3.urls.link,
                    data: {
                        'typolink': link,
                        'lang': Ted3.fedata.currentlangId
                    }
                }).done(function (data) {
                    location.href = data;
                });
            });

            $('#ted3-quickinfo-close').on('click', function () {
                $('#ted3-quickinfo').removeClass('ted3-quickinfo-visble');
            });



            $('.ted3-btn-showhidden').on('click', function () {

                //  Ted3.root.toggleClass('ted3-mode-preview');
                if (!that.hasMode('showhidden')) {
                    that.showHidden();
                } else {
                    that.hideHidden();
                }

            });



            $('.syslang.notTranslatedPage').on('click', function (e) {
                e.preventDefault();
                var langid = $(this).data('syslang');
                // var redirect = $(this).data('redirect');
                Ted3.ajax({
                    url: Ted3.urls.translate,
                    data: {
                        uid: Ted3.fedata.pid,
                        lang: langid,
                        table: 'pages'
                    }
                }).done(function (data) {
                    var data = JSON.parse(data);
                    //console.log(data);
                    location.href = data.translatedPageUrl;
                });
            });


            // init From Memory
            this._initFromMemory();

            // Quickinfo
            if ($('.ted3-quickinfo-msg').length > 0) {
                $('#ted3-quickinfo').addClass('ted3-quickinfo-visble');
            }

            $(window).on('scroll', function () {
                if (that.scrollListener) {
                    clearTimeout(that.elementTo);
                    that.elementTo = setTimeout(function () {
                        that.getVisibleElement();
                    }, 100);
                }
            })

        },
        _init: function () {
            var that = this;
            that._trigger('init');
        },
        _initFromMemory: function () {
            var that = this;
            var pos = Ted3.memory.get("ted3-mainbarpos");
            if (pos != null) {

                if (pos.left + 40 > $(window).width()) {
                    pos.left = $(window).width() - 60;
                }
                if (pos.top > 800 || pos.top < 0) {
                    pos.top = 100;
                }

                this.outerMainbar.css("right", 'auto');
                this.outerMainbar.css(pos);
            }

            if (Ted3.memory.get("ted3-showhidden") || Ted3.root.data('isdefaultlang') == 0) {
                that.showHidden();
            }
            if (Ted3.memory.get("ted3-preview")) {
                this.previewMode();
            } else {
                this.editMode();
            }
            setTimeout(function () {
                if (Ted3.memory.get("ted3-clipboard")) {
                    that.element.addClass('ted3-mode-clipboard');
                }
            }, 400);


            // Show Hidden
        },
        reload: function () {
            var loader = $('<div id="ted3-loader" ><div class="loader"></div></div>');
            this.element.addClass('ted3-mode-loading').append(loader);
            history.go(0);
        },
        hasMode: function (mode) {
            if (this.element.hasClass('ted3-mode-' + mode)) {
                return true;
            }
            return false;
        },
        setToClipboard: function (data) {
            Ted3.memory.set('ted3-clipboard', data);
            this.element.addClass('ted3-mode-clipboard');
        },
        getFromClipboard: function () {
            //this.element.removeClass('ted3-mode-clipboard');
            var data = Ted3.memory.get('ted3-clipboard');
//            Ted3.memory.remove('ted3-clipboard');
            return data;
        },
        previewMode: function () {
            Ted3.memory.set('ted3-preview', true);
            Ted3.root.addClass('ted3-mode-preview');
            Ted3.root.removeClass('ted3-mode-edit');
            this._hotEventsOff();
            this.hideAddzones();
            Ted3.root.find('.ted3-element-selected').element('unselect');
            this._trigger('preview');
        },
        editFirstTime: true,
        editMode: function () {
            var that = this;


            Ted3.root.addClass('ted3-mode-edit');
            Ted3.root.removeClass('ted3-mode-preview');
            Ted3.memory.set('ted3-preview', false);
            this._hotEventsOn();
            this.showAddzones();

            this._trigger('edit');
        },
        viselement: {offset: 0, element: null},
        scrollListener: true,
        elementTo: null,
        getVisibleElement: function () {
            var that = this;
            var elementGot = false;
//            console.log("getVisibleElement");
            $('[data-element]').each(function () {
                //check if visible
                var topOffset = $(this).offset().top;
                if (topOffset > $(window).scrollTop() && topOffset < $(window).scrollTop() + $(window).height()) {
                    that.viselement.element = $(this);
                    that.viselement.offset = topOffset - $(window).scrollTop()
//                    console.log("element got");
                    elementGot = true;
                    return false;
                }
            });
            if (!elementGot) {
                $('*').each(function () {
                    //check if visible
                    var topOffset = $(this).offset().top;
                    if (topOffset > $(window).scrollTop() && topOffset < $(window).scrollTop() + $(window).height()) {
                        that.viselement.element = $(this);
                        that.viselement.offset = topOffset - $(window).scrollTop()
//                        console.log("any element got");
                        elementGot = true;
                        return false;
                    }
                });
            }
        },
        moveToView: function (inv) {
            var that = this;
            if (this.viselement.element != null && this.viselement.element.length > 0) {
                //console.log(this.viselement.element.offset().top);
                setTimeout(function () {
                    var newWindowOffset = that.viselement.element.offset().top - that.viselement.offset;
                    that.scrollListener = false;
                    $(window).scrollTop(newWindowOffset);
                    that.scrollListener = true;
                }, 5);


            }

        },
        showAddzones: function () {
            if (!this.hasMode('preview')) {
                Ted3.memory.set('ted3-addzones', true);
                this.element.addClass('ted3-mode-addzones');
                this._trigger('addzone', {}, {active: true});
                //    this.element.find('[data-element].ui-draggable').element('option', 'move', 1);
            }
        },
        hideAddzones: function () {
            Ted3.memory.set('ted3-addzones', false);
            this.element.removeClass('ted3-mode-addzones');
            this._trigger('addzone', {}, {active: false});
            //  this.element.find('[data-element].ui-draggable').element('option', 'move', 0);
        },
        showHidden: function () {
            Ted3.memory.set('ted3-showhidden', true);
            this.element.addClass('ted3-mode-showhidden');
            this._trigger('showhidden');
        },
        hideHidden: function () {
            Ted3.memory.set('ted3-showhidden', false);
            this.element.removeClass('ted3-mode-showhidden');
            this._trigger('hidehidden');
        },
        change: function () {
            this.element.addClass('ted3-mode-change');
        },

        _hotEventsOn: function () {
            var that = this;
            this.element.on({
                dragenter: function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    that.element.addClass('ted3-mode-dragging');
                    that._trigger('dragenter', e);
                },
                dragover: function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                },
                dragend: function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    that.element.removeClass('ted3-mode-dragging');
                    that._trigger('dragend', e);
                },
                drop: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log("native drop");
                    that.element.removeClass('ted3-mode-dragging');
                    that._trigger('dragend', e);
                }

            });

            this.element.on({
                click: function () {
//                    console.log("adsf");
                    if (that.hasMode('selected') && !that.hasMode('change') && $(this).closest('#ted3-panel').length < 1) {
                        if ($(this).closest('[data-element]').length < 1) {
                            that.element.find('[data-element].ted3-element-selected').element('unselect');
                        }
                    }
                }
            }, '*');
        },
        _hotEventsOff: function () {
            this.element.off('dragenter');
            this.element.off('dragover');
            this.element.off('dragend');
            this.element.off('drop');
            this.element.off('click');

        }
    });


    $(window).on({
        resize: function () {

            if (Ted3.root && !Ted3.root.hasClass('ted3-mode-preview')) {
                $('.ted3-element-selected').element('initBarsPositions');
            }
        },
        load: function () {

            // Cleanup unwanted clones
            $('.ted3-element').each(function (i, item) {
                try {
                    $(this).element('isInit');
                } catch (e) {
                    $(this).find('[data-rte="1"]').removeAttr('id');
                    $(this).removeClass('ted3-element');
                    $(this).removeClass('ted3-element-root');
                    $(this).removeClass('ted3-element-selected');
                    $(this).removeClass('ted3-element-saveable');
                    $(this).removeAttr('data-element');
                    $(this).find('*').removeAttr('data-widget');
                    $(this).find('*').removeAttr('data-element').removeClass('ted3-element').removeClass('ted3-element-root').removeClass('ted3-element-selected');
                }
            });
        },
    });

}(Ted3.jQuery));
