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

import {Button} from './button.js?2.1.2';
import {Panel} from './panel.js?2.1.2';
import {PageScroller} from './page_scroller.js?2.1.2';

export function Item(id, set) {
    this.id  = id;
    this.set = set;

    const button = this.getButtonByID(id);
    const panel  = this.getPanelByID(id);

    this.button = button.rlta = new Button(button, this);
    this.panel  = panel.rlta = new Panel(panel, this);
}

Item.prototype = {
    getButtonByID: function(id) {
        return this.set.element.querySelector(`#${id}`);
    },

    getPanelByID: function(id) {
        return this.set.element.querySelector(`[aria-labelledby="${id}"]`);
    },

    toggle: function(scroll) {
        if ( ! this.button.isOpen()) {
            this.open({scroll: scroll});
            return;
        }

        if (this.set.isTabs()) {
            return;
        }

        this.close();
    },

    close: function() {
        if ( ! this.button.isOpen()) {
            return;
        }

        const fade  = this.shouldFade() || false;
        const slide = this.shouldSlide() || false;

        this.closeAnimate({fade: fade && slide, slide: slide});
    },

    openNoAnimate: function() {
        return this.open({scroll: false, focus: false, animate: false});
    },

    closeNoAnimate: function() {
        return this.closeAnimate({fade: false, slide: false});
    },

    getScroll: function(scroll) {
        return false;
    },

    prepareActionsObject: function(actions) {
        actions = actions || {};

        actions.scroll  = actions && actions.scroll !== undefined ? actions.scroll : true;
        actions.focus   = actions && actions.focus !== undefined ? actions.focus : true;
        actions.animate = actions && actions.animate !== undefined ? actions.animate : true;
        actions.hash    = actions && actions.hash !== undefined ? actions.hash : true;

        return actions;
    },

    prepareAnimationsObject: function(animations) {
        animations = animations || {};

        animations.fade  = animations && animations.fade !== undefined ? animations.fade : this.shouldFade();
        animations.slide = animations && animations.slide !== undefined ? animations.slide : this.shouldSlide();

        return animations;
    },

    open: async function(actions) {
        if (this.set.parent) {
            await this.set.parent.openNoAnimate();
        }

        actions = this.prepareActionsObject(actions);

        const animations = {
            fade : actions.animate ? this.shouldFade() : false,
            slide: actions.animate ? this.shouldSlide() : false,
        };

        let previous_item = this.set.getActive();

        this.set.setActive(this);

        if (this.set.isAccordions()) {
            this.closeOthers(previous_item);

            if (previous_item && previous_item !== this) {
                previous_item.closeAnimate({fade: animations.fade && animations.slide, slide: animations.slide});
            }

            return this.openAnimate(actions, animations);
        }

        const previous_height = previous_item ? previous_item.panel.element.offsetHeight : 0;

        this.button.center();

        this.closeOthers();

        return this.openAnimate(actions, animations, previous_height);
    },

    closeOthers: function(previous_item) {
        this.set.items.forEach(item => {
            if (item === this || item === previous_item) {
                return;
            }

            item.closeNoAnimate();
        });
    },

    openAnimate: async function(actions, animations, start_height) {
        actions = this.prepareActionsObject(actions);

        return new Promise(async (resolve) => {
            await this.set.removeFocus();

            if (actions.focus) {
                this.button.element.focus({preventScroll: !! scroll});
            }

            if (this.getState() === 'open' || this.getState() === 'opening') {

                resolve();
                return;
            }

            if (window.getComputedStyle(this.panel.element).display === 'none') {
                this.panel.element.style.height = 0;
            }

            await this.setState('opening');
            await this.openAnimateAction(animations, start_height);

            this.set.storeActive(this);
            if (actions.hash) {
                this.set.setUrlHash(this);
            }

            await this.setState('open');
            resolve();
        });
    },

    closeAnimate: async function(animations) {
        return new Promise(async (resolve) => {
            if (this.getState() === 'closing') {
                resolve();
                return;
            }

            if (this.getState() === 'closed') {
                animations = {
                    fade : false,
                    slide: false,
                };
            }

            await this.setState('closing');
            await this.closeAnimateAction(animations);
            await this.setState('closed');

            resolve();
        });
    },

    openAnimateAction: function(animations, start_height) {

        this.panel.open();
        return this.panel.show();
    },

    closeAnimateAction: async function(animations) {

        this.panel.close();
        return this.panel.hide();
    },

    scroll: function(scroll) {
    },

    getData: function(key) {
        return this.button.getData(key);
    },

    setState: function(state) {
        return Promise.all([
            this.button.setState(state),
            this.panel.setState(state)
        ]);
    },

    getState: function() {
        return this.button.getState();
    },

    getIndex: function() {
        return this.set.items.findIndex(item => item === this);
    },

    getAnimations: function() {
    },

    shouldFade: function() {
    },

    shouldSlide: function() {
    },
    addFocus   : function() {
        this.button.setData('focus', true);
        this.button.center();
    },
    removeFocus: function() {
        this.button.removeData('focus');
    },
};
