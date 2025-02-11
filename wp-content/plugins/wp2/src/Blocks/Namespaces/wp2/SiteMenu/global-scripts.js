"use strict";

document.addEventListener("DOMContentLoaded", function () {

  const currentPageUrl = window.location.href;

  const navItems = document.querySelectorAll(".wp-block-navigation-item a");

  navItems.forEach(function (navItem) {
    if (navItem.href === currentPageUrl) {

      var parent = navItem.closest(".wp-block-navigation-item");

      parent.setAttribute("aria-current", "page");

    }
  });

});