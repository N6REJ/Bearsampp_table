/**
 * @package         Tabs & Accordions
 * @version         2.1.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

'use strict';

import {Helper} from './modules/helper.js?2.1.2';
import {Set} from './modules/set.js?2.1.2';
import {PageScroller} from './modules/page_scroller.js?2.1.2';

(function() {
    window.RegularLabs = window.RegularLabs || {};

    RegularLabs.TabsAccordions = {
        init: function(settings) {
            this.createSets(settings);
            this.handleAnchorLinks();
            this.scrollToNestedAnchor();
        },

        createSets: function(settings) {
            document.querySelectorAll('[data-rlta-element="container"]').forEach(container => {
                container.rlta = new Set(container, settings);
            });
        },

        handleAnchorLinks: function() {
            const urlWithoutHash = window.location.href.split('#')[0];

            const selectors = [
                'a[href^="#"]',
                'a[href^="/#"]',
                'a[href^="' + urlWithoutHash + '#"]',
                'a[href^="' + urlWithoutHash + '/#"]',
            ];


            document.querySelectorAll(selectors.join(',')).forEach(link => {
                this.handleAnchorLink(link);
            });
        },

        handleAnchorLink: function(link) {
            if (link.target) {
                return;
            }

            const hash_id = decodeURIComponent(link.hash.replace('#', '')).trim();

            const item_anchor = this.getItemAndAnchorById(hash_id);
            const item        = item_anchor[0];
            const anchor      = item_anchor[1];

            if (item === null) {
                return;
            }

            if ( ! item) {
                setTimeout(() => {
                    this.handleAnchorLink(link);
                }, 100);
                return;
            }

            link.addEventListener('click', (event) => {
                event.preventDefault();

                this.scrollToItem(item, anchor);
            });
        },

        open: async function(button, scroll, set_hash = true) {
            button = Helper.getButtonByMixed(button);

            return new Promise(async (resolve) => {
                if ( ! button) {
                    resolve();
                    return;
                }

                if (scroll === undefined && window.rltaSettings !== undefined && window.rltaSettings.constructor === Object) {
                    scroll = window.rltaSettings.scrollOnLinks;
                }

                await button.rlta.item.open({
                    scroll: scroll,
                    hash  : set_hash
                });
                resolve();
            });
        },

        toggle: function(button, scroll) {
            button = Helper.getButtonByMixed(button);

            if ( ! button) {
                return;
            }

            if (scroll === undefined && window.rltaSettings !== undefined && window.rltaSettings.constructor === Object) {
                scroll = window.rltaSettings.scrollOnLinks;
            }

            button.rlta.item.toggle(scroll);
        },

        closeAll: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const buttons = parent.querySelectorAll('[data-rlta-element="button"]');

            buttons.forEach(button => {
                if (button && button.rlta !== undefined) {
                    button.rlta.item.close();
                }
            });
        },

        openAll: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const sets = parent.querySelectorAll('[data-rlta-type="accordions"],[data-rlta-element="button-list"]');

            sets.forEach(set => {
                const button = set.querySelector(':scope > [data-rlta-element="button"]');
                if (button && button.rlta !== undefined) {
                    button.rlta.item.open({scroll: false});
                }
            });
        },

        toggleAll: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const sets = parent.querySelectorAll('[data-rlta-type="accordions"],[data-rlta-element="button-list"]');

            sets.forEach(set => {
                const button = set.querySelector(':scope > [data-rlta-element="button"]');
                if (button && button.rlta !== undefined) {
                    button.rlta.item.toggle(false);
                }
            });
        },

        closeAccordions: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const buttons = parent.querySelectorAll('[data-rlta-type="accordions"] > [data-rlta-element="button"]');

            buttons.forEach(button => {
                if (button && button.rlta !== undefined) {
                    button.rlta.item.close();
                }
            });
        },

        openAccordions: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const sets = parent.querySelectorAll('[data-rlta-type="accordions"]');

            sets.forEach(set => {
                const button = set.querySelector(':scope > [data-rlta-element="button"]');
                if (button && button.rlta !== undefined) {
                    button.rlta.item.open({scroll: false});
                }
            });
        },

        toggleAccordions: function(parent) {
            parent = Helper.getParentByMixed(parent);

            const sets = parent.querySelectorAll('[data-rlta-type="accordions"]');

            sets.forEach(set => {
                const button = set.querySelector(':scope > [data-rlta-element="button"]');
                if (button && button.rlta !== undefined) {
                    button.rlta.item.toggle(false);
                }
            });
        },

        getItemAndAnchorById: function(anchor_id) {
            if (anchor_id === '') {
                return [null, null];
            }

            // Ignore the url hash if it contains weird characters
            if (anchor_id.indexOf('/') > -1 || anchor_id.indexOf('/') > -1) {
                return [null, null];
            }

            let item = Helper.getItemByMixed(anchor_id);

            if (item !== null) {
                return [item, null];
            }

            const anchor = document.querySelector('#' + anchor_id);

            if ( ! anchor) {
                return [null, null];
            }

            const panel = anchor.closest('[data-rlta-element="panel"]');

            if ( ! panel) {
                return [null, null];
            }

            const id = panel.getAttribute('aria-labelledby');

            if ( ! id) {
                return [null, null];
            }

            item = Helper.getItemByMixed(id);

            return [item, anchor];
        },

        scrollToNestedAnchor: async function() {
            const hash_id = decodeURIComponent(window.location.hash.replace('#', '')).trim();

            const item_anchor = this.getItemAndAnchorById(hash_id);
            const item        = item_anchor[0];
            const anchor      = item_anchor[1];

            if (item === null || anchor === null) {
                return;
            }

            this.scrollToItem(item, anchor);
        },

        scrollToItem: async function(item, anchor) {
            if ( ! item) {
                setTimeout(() => {
                    this.scrollToNestedAnchor();
                }, 100);
                return;
            }

            if ( ! anchor) {
                this.open(item.id);
                return;
            }

            await this.open(item.id, false, false);

            const scroller   = new PageScroller(item);
            const anchor_top = anchor.getBoundingClientRect().top;
            const view       = scroller.getView();
            const offset     = scroller.getOffset();
            const offset_top = view.top - offset.top;

            scroller.scrollTo(offset_top + anchor_top, 0);
            history.pushState(null, null, '#' + anchor.id);
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        RegularLabs.TabsAccordions.init(null)
    });

    window.rlta = window.rlta || RegularLabs.TabsAccordions;
})();
