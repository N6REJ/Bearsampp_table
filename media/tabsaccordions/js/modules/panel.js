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

import {Helper} from './helper.js?2.1.2';
import {Prototypes} from './prototypes.js?2.1.2';

export function Panel(element, item) {
    this.set     = item.set;
    this.item    = item;
    this.element = element;
    this.events  = [];

    this.init = async function() {
        const createEvents = () => {
            ['open', 'opening', 'closed', 'closing'].forEach(state => {
                this.events[state] = new CustomEvent(`rlta.${state}`, {bubbles: true, detail: this});
            });
        };

        createEvents();
    };

    this.init();
}

Panel.prototype = {
    setState: function(state) {
        return new Promise(resolve => {
            if (this.getState() === state) {
                return;
            }

            const active = state !== 'closed';

            this.setData('state', state);
            this.element.hidden = ! active;

            this.element.dispatchEvent(this.events[state]);
            resolve();
        });
    },

    getContent: function() {
        return this.element.querySelector('[data-rlta-element="panel-content"]');
    },

    open: function() {
        return new Promise((resolve) => {
            this.element.style.height = 'auto';
            resolve();
        });
    },

    close: function() {
        return new Promise((resolve) => {
            this.element.style.height = 0;
            resolve();
        });
    },

    show: function() {
        return new Promise((resolve) => {
            const panel_content = this.getContent();

            panel_content.style.opacity = 1;
            resolve();
        });
    },

    hide: function() {
        return new Promise((resolve) => {
            const panel_content = this.getContent();

            panel_content.style.opacity = 0;
            resolve();
        });
    },

    slideOpen: function(start_height) {
    },

    slideClose: function(start_height) {
    },

    slide: function(start_height, end_height, action = 'open') {
    },

    shouldSlide: function(action = 'open') {
    },

    fadeOpen: function() {
    },

    fadeClose: function() {
    },

    fade: function(to_opacity, action = 'open') {
    },

    shouldFade: function(action = 'open') {
    },
};

Panel.prototype.hasData    = Prototypes.hasData;
Panel.prototype.getData    = Prototypes.getData;
Panel.prototype.setData    = Prototypes.setData;
Panel.prototype.removeData = Prototypes.removeData;
Panel.prototype.getState   = Prototypes.getState;
