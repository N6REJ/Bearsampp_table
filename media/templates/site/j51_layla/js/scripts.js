(function($) {
  "use strict";

  $(document).ready(function() {
    var templateOptions = Joomla.getOptions("j51_template");


    // Bootstrap stuff
    $(document).ready(function() {
      // Turn radios into btn-group
      $(".radio.btn-group label").addClass("btn");
      $(".btn-group label:not(.active)").click(function() {
        var label = $(this);
        var input = $("#" + label.attr("for"));

        if (!input.prop("checked")) {
          label
            .closest(".btn-group")
            .find("label")
            .removeClass("active btn-success btn-danger btn-primary");
          if (input.val() == "") {
            label.addClass("active btn-primary");
          } else if (input.val() == 0) {
            label.addClass("active btn-danger");
          } else {
            label.addClass("active btn-success");
          }
          input.prop("checked", true);
        }
      });
      $(".btn-group input[checked=checked]").each(function() {
        if ($(this).val() == "") {
          $("label[for=" + $(this).attr("id") + "]").addClass(
            "active btn-primary"
          );
        } else if ($(this).val() == 0) {
          $("label[for=" + $(this).attr("id") + "]").addClass(
            "active btn-danger"
          );
        } else {
          $("label[for=" + $(this).attr("id") + "]").addClass(
            "active btn-success"
          );
        }
      });
    });
  });
})(jQuery);

(function() {
  document.addEventListener("DOMContentLoaded", function() {
    var templateOptions = Joomla.getOptions("j51_template");

    // Add parent-hover class to hornav
    var header = document.getElementById('container_header');
    const hornavli = document.querySelector(".hornav li");

    hornavli.addEventListener("mouseenter", e => {
      hornavli.classList.toggle("parent-hover");
    });

    // Mobile menu
    const menu = new MmenuLight(document.querySelector("#mobile-menu"));

    const navigator = menu.navigation({
      theme: "dark",
      title: templateOptions.mobileMenuTitle
    });

    const drawer = menu.offcanvas({
      position: templateOptions.mobileMenuPosition
    });

    document.querySelector('a[href="#mobile-menu"]')
      .addEventListener('click', (evnt) => {
        evnt.preventDefault();
        drawer.open();
      });

      // one page website - close on click
      // document.querySelector('.mobile-menu')
      // .addEventListener('mouseup', (evnt) => {
      //   evnt.preventDefault();
      //   drawer.close();
      // });

    // Animate on scroll
    var executeThis = function() {
      var continuousElements = document.getElementsByClassName("animate");
      for (var i = 0; i < continuousElements.length; i++) {
        new Waypoint({
          element: continuousElements[i],
          handler: function() {
            this.element.classList.add("animated");
          },
          offset: "85%"
        });
      }
    };
    var checkWaypoint = function() {
      if (typeof Waypoint === "undefined") {
        setTimeout(checkWaypoint, 50);
      } else {
        executeThis();
      }
    };
    checkWaypoint();

    // Add class to body on scroll

    let scrollpos = window.scrollY
    const header_height = header.offsetHeight

    const add_class_on_scroll = () => document.body.classList.add('scrolled')
    const remove_class_on_scroll = () => document.body.classList.remove('scrolled')

    window.addEventListener('scroll', function() {
      scrollpos = window.scrollY;

      if (scrollpos >= header_height) {
        add_class_on_scroll()
      } else {
        remove_class_on_scroll()
      }
    })

  });
})();

function dodajAktywne(elem) {
  var a = document.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    a[i].parentNode.classList.remove("active");
  }
  elem.parentNode.classList.add("active");
}
