import "../../node_modules/headroom.js/dist/headroom.min.js";

(function() {
    console.log("Initializing Headroom.js script");

    // Ensure the script runs after DOM is fully loaded
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOM fully loaded");

        // Select the navbar element
        var navbar = document.querySelector(".wp2-group--navbar-primary");
        
        // Ensure element exists before initializing Headroom.js
        if (navbar) {
            console.log("Navbar element found", navbar);

            // Define options for Headroom.js
            var options = {

                offset : 0,

                classes: {
                    initial: "wp2-animate",
                    pinned: "wp2-animate--slide-down",
                    unpinned: "wp2-animate--slide-up",
                },
                onPin: function() { console.log("Headroom: pinned"); },
                onUnpin: function() { console.log("Headroom: unpinned"); },
                onTop: function() { console.log("Headroom: at top"); },
                onNotTop: function() { console.log("Headroom: not at top"); },
                onBottom: function() { console.log("Headroom: at bottom"); },
                onNotBottom: function() { console.log("Headroom: not at bottom"); }
            };
            
            console.log("Initializing Headroom.js with options", options);

            // Initialize Headroom.js
            var headroom = new Headroom(navbar, options);
            headroom.init();
            console.log("Headroom.js initialized");
        } else {
            console.log("Navbar element not found");
        }
    });
})();
