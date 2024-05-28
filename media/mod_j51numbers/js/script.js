import { CountUp } from './countUp.min.js';

(function() {
    document.addEventListener('DOMContentLoaded', function() {

        var executeThis = function() {
            var options = Joomla.getOptions('j51_module_numbers');

            if (options.length) {
                options.forEach(function(option) {
                    const options = {
                        decimalPlaces: option.decimal,
                        duration: option.animation_length,
                        useGrouping: option.group,
                        separator: option.group_sep,
                        decimal: option.decimal_sep,
                    };
                    let countUp = new CountUp(option.id, option.counts, options);
                    // countUp.start();
                    
                    new Waypoint({
                        element: document.getElementById(option.id),
                        handler: function(direction) {
                            // setTimeout(countUp.start, option.delay);
                            countUp.start();
                            this.element.classList.add("animated");                            
                        },
                        offset: "85%"
                    });
                });
            }
        };

        var checkWaypoint = function(){
            if (typeof Waypoint === "undefined") {
                setTimeout(checkWaypoint, 50);
            } else {
                executeThis();
            }
        };
        checkWaypoint();
        
    });
})();


