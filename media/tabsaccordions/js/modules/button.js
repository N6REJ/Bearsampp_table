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

import {Prototypes} from './prototypes.js?2.1.2';

export function Button(element, item) {
    this.set     = item.set;
    this.item    = item;
    this.element = element;
    this.events  = [];

    this.init = async function() {
        const addListeners = () => {
            const action = ['hover', 'mouseenter'].includes(this.set.settings.mode) ? 'mouseenter' : 'click';

            this.element.addEventListener(action, (event) => this.set.handleButtonSelect(event));
            this.element.addEventListener('keydown', (event) => this.set.handleButtonKeyDown(event));
            this.element.addEventListener('keyup', (event) => this.set.handleButtonKeyUp(event));
        };

        const createEvents = () => {
            ['open', 'opening', 'closed', 'closing'].forEach(state => {
                this.events[state] = new CustomEvent(`rlta.${state}`, {bubbles: true, detail: this});
            });
        };

        await createEvents();
        addListeners();
    };

    this.init();
}

Button.prototype = {
    center: function() {
        return this.set.buttonScroller.center(this.item);
    },

    setState: function(state) {
        return new Promise(resolve => {
            const active = state !== 'closed';

            this.setData('state', state);
            if (this.set.isTabs()) {
                this.element.setAttribute('aria-selected', active);
            } else {
                this.element.setAttribute('aria-expanded', active);
            }

            if (this.isClosed() && this.getData('title-inactive')) {
                this.setText(this.getData('title-inactive'));
            }

            if ( ! this.isClosed() && this.getData('title-active')) {
                this.setText(this.getData('title-active'));
            }

            this.element.dispatchEvent(this.events[state]);

            resolve();
        });
    },

    isSelected: function() {
        return this.element.getAttribute('aria-selected') === 'true' || this.element.getAttribute('aria-expanded') === 'true';
    },

    isOpen: function() {
        return this.getState() === 'open';
    },

    isClosed: function() {
        return this.getState() === 'closed';
    },

    setText: function(text) {
        this.element.querySelector('[data-rlta-element="heading"]').innerText = text;
    },
};

Button.prototype.hasData    = Prototypes.hasData;
Button.prototype.getData    = Prototypes.getData;
Button.prototype.setData    = Prototypes.setData;
Button.prototype.removeData = Prototypes.removeData;
Button.prototype.getState   = Prototypes.getState;
