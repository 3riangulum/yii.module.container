'use strict';

const PopUp = function () {
    let clickClass = null,
        modalClass = null,
        contentId = null,
        baseUrl = null,
        dataMapper = [],
        dataMapperModeTr = false,
        doubleClick = true,
        beforeClick = function () {
            return null;
        },
        afterClick = function () {
            return null;
        },

        init = function (data) {
            clickClass = '.' + data.clickClass;
            modalClass = '.' + data.modalClass;
            contentId = '#' + data.contentId;
            baseUrl = data.baseUrl;
            dataMapper = data.dataMapper;
            dataMapperModeTr = typeof data.dataMapperModeTr !== "undefined" ? data.dataMapperModeTr : 0;
            doubleClick = data.doubleClick;

            if (typeof data.beforeClick !== "undefined") {
                if (typeof data.beforeClick === "function") {
                    beforeClick = data.beforeClick;
                }
            }

            if (typeof data.afterClick !== "undefined") {
                if (typeof data.afterClick === "function") {
                    afterClick = data.afterClick;
                }
            }

            bindClick();
            bindLoader();
        },

        bindClick = function () {
            $('body').on(doubleClick ? 'dblclick' : 'click', clickClass, function (e) {
                e.preventDefault();
                afterClick($(this));
                showModal(
                    _createUrl(
                        dataMapperModeTr ? $(this).parents('tr') : $(this)
                    )
                );

            });
        },

        showModal = function (url) {
            let redefinedUrl = beforeClick(url);
            if (redefinedUrl) {
                url = redefinedUrl;
            }

            CORE.headerAnimateStart();

            $(modalClass)
                .modal('show')
                .find(contentId)
                .empty()
                .load(
                    url,
                    function () {
                        CORE.headerAnimateStop();
                        try {
                            $('.modal-dialog').draggable({
                                handle: ".panel-heading"
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    }
                );
        },

        _createUrl = function (domItem) {
            let url = baseUrl + '?';
            let searchParam = new URLSearchParams();
            dataMapper.forEach(function (item, i, arr) {
                let urlParamVal = domItem.attr('data-' + item.toLowerCase());
                if (urlParamVal) {
                    searchParam.set(item, urlParamVal);
                }
            });

            url += searchParam.toString();
            url = url.replace(/[&?]$/, '');

            return url;
        },

        hideModal = function () {
            $(modalClass).modal('hide');
        },

        hideModalAndRefreshGrid = function (gridId) {
            $(modalClass).modal('hide');
            CORE.refreshGrid('#' + gridId);
        },
        bindLoader = function () {
            $(modalClass)
                .off('shown.bs.modal')
                .on('shown.bs.modal', function () {
                    CORE.headerAnimateStop();
                })
                .off('hiden.bs.modal')
                .on('hide.bs.modal', function (e) {
                    CORE.headerAnimateStop();
                })
                .off('hidden.bs.modal')
                .on('hidden.bs.modal', function (e) {
                    CORE.headerAnimateStop();
                    $(modalClass).find('.popover-content').empty();
                });
        };

    return {
        init: init,
        hideModal: hideModal,
        hideModalAndRefreshGrid: hideModalAndRefreshGrid
    };
};
