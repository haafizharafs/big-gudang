("use strict");
(function () {
    var isWindows = navigator.userAgent.indexOf("Win") != -1 ? true : false;
    if (isWindows) {
        // if we are on windows OS we activate the perfectScrollbar function
        if (document.getElementsByClassName("main-content")[0]) {
            var mainpanel = document.querySelector(".main-content");
            var ps = new PerfectScrollbar(mainpanel);
        }

        if (document.getElementsByClassName("navbar-collapse")[0]) {
            var fixedplugin = document.querySelector(".navbar-collapse");
            var ps2 = new PerfectScrollbar(fixedplugin);
        }

        if (document.getElementsByClassName("modal-body")[0]) {
            var modalDialogs = document.querySelectorAll(
                ".modal-dialog-scrollable .modal-body"
            );
            for (var i = 0; i < modalDialogs.length; i++) {
                var modalDialog = modalDialogs[i];
                var ps3 = new PerfectScrollbar(modalDialog);
            }
        }
    }
})();

// initialization of Tooltips
var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// when input is focused add focused class for style
window.focused = function (el) {
    if (el.parentElement.classList.contains("input-group")) {
        el.parentElement.classList.add("focused");
    }
};

// when input is focused remove focused class for style
window.defocused = function (el) {
    if (el.parentElement.classList.contains("input-group")) {
        el.parentElement.classList.remove("focused");
    }
};

// helper for adding on all elements multiple attributes
function setAttributes(el, options) {
    Object.keys(options).forEach(function (attr) {
        el.setAttribute(attr, options[attr]);
    });
}

// adding on inputs attributes for calling the focused and defocused functions
if (document.querySelectorAll(".input-group").length != 0) {
    var allInputs = document.querySelectorAll("input.form-control");
    allInputs.forEach((el) =>
        setAttributes(el, {
            onfocus: "focused(this)",
            onfocusout: "defocused(this)",
        })
    );
}

// Debounce Function
// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this,
            args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Toggle Sidenav
const iconNavbarSidenav = document.getElementById("iconNavbarSidenav");
const iconSidenav = document.getElementById("iconSidenav");
let body = document.getElementsByTagName("body")[0];
let className = "g-sidenav-pinned";

if (iconNavbarSidenav) {
    iconNavbarSidenav.addEventListener("click", toggleSidenav);
}

if (iconSidenav) {
    iconSidenav.addEventListener("click", toggleSidenav);
}

function toggleSidenav() {
    if (body.classList.contains(className)) {
        body.classList.remove(className);
    } else {
        body.classList.add(className);
    }
}

let html = document.getElementsByTagName("html")[0];

html.addEventListener("click", function (e) {
    if (
        body.classList.contains("g-sidenav-pinned") &&
        !e.target.classList.contains("nav-toggler") &&
        !e.target.classList.contains("nav-collapse")
    ) {
        body.classList.remove(className);
    }
});

// Deactivate sidenav type buttons on resize and small screens
window.addEventListener("resize", sidenavTypeOnResize);
window.addEventListener("load", sidenavTypeOnResize);

function sidenavTypeOnResize() {
    let elements = document.querySelectorAll('[onclick="sidebarType(this)"]');
    if (window.innerWidth < 1200) {
        elements.forEach(function (el) {
            el.classList.add("disabled");
        });
    } else {
        elements.forEach(function (el) {
            el.classList.remove("disabled");
        });
    }
}

// Tabs navigation

var total = document.querySelectorAll(".nav-pills");

total.forEach(function (item, i) {
    var moving_div = document.createElement("div");
    var first_li = item.querySelector("li:first-child .nav-link");
    var tab = first_li.cloneNode();
    tab.innerHTML = "-";

    moving_div.classList.add("moving-tab", "position-absolute", "nav-link");
    moving_div.appendChild(tab);
    item.appendChild(moving_div);

    var list_length = item.getElementsByTagName("li").length;

    moving_div.style.padding = "0px";
    moving_div.style.width =
        item.querySelector("li:nth-child(1)").offsetWidth + "px";
    moving_div.style.transform = "translate3d(0px, 0px, 0px)";
    moving_div.style.transition = ".5s ease";

    item.onmouseover = function (event) {
        let target = getEventTarget(event);
        let li = target.closest("li"); // get reference
        if (li) {
            let nodes = Array.from(li.closest("ul").children); // get array
            let index = nodes.indexOf(li) + 1;
            item.querySelector(
                "li:nth-child(" + index + ") .nav-link"
            ).onclick = function () {
                moving_div = item.querySelector(".moving-tab");
                let sum = 0;
                if (item.classList.contains("flex-column")) {
                    for (var j = 1; j <= nodes.indexOf(li); j++) {
                        sum += item.querySelector(
                            "li:nth-child(" + j + ")"
                        ).offsetHeight;
                    }
                    moving_div.style.transform =
                        "translate3d(0px," + sum + "px, 0px)";
                    moving_div.style.height = item.querySelector(
                        "li:nth-child(" + j + ")"
                    ).offsetHeight;
                } else {
                    for (var j = 1; j <= nodes.indexOf(li); j++) {
                        sum += item.querySelector(
                            "li:nth-child(" + j + ")"
                        ).offsetWidth;
                    }
                    moving_div.style.transform =
                        "translate3d(" + sum + "px, 0px, 0px)";
                    moving_div.style.width =
                        item.querySelector("li:nth-child(" + index + ")")
                            .offsetWidth + "px";
                }
            };
        }
    };
});

// Tabs navigation resize

window.addEventListener("resize", function (event) {
    total.forEach(function (item, i) {
        item.querySelector(".moving-tab").remove();
        var moving_div = document.createElement("div");
        var tab = item.querySelector(".nav-link.active").cloneNode();
        tab.innerHTML = "-";

        moving_div.classList.add("moving-tab", "position-absolute", "nav-link");
        moving_div.appendChild(tab);

        item.appendChild(moving_div);

        moving_div.style.padding = "0px";
        moving_div.style.transition = ".5s ease";

        let li = item.querySelector(".nav-link.active").parentElement;

        if (li) {
            let nodes = Array.from(li.closest("ul").children); // get array
            let index = nodes.indexOf(li) + 1;

            let sum = 0;
            if (item.classList.contains("flex-column")) {
                for (var j = 1; j <= nodes.indexOf(li); j++) {
                    sum += item.querySelector(
                        "li:nth-child(" + j + ")"
                    ).offsetHeight;
                }
                moving_div.style.transform =
                    "translate3d(0px," + sum + "px, 0px)";
                moving_div.style.width =
                    item.querySelector("li:nth-child(" + index + ")")
                        .offsetWidth + "px";
                moving_div.style.height = item.querySelector(
                    "li:nth-child(" + j + ")"
                ).offsetHeight;
            } else {
                for (var j = 1; j <= nodes.indexOf(li); j++) {
                    sum += item.querySelector(
                        "li:nth-child(" + j + ")"
                    ).offsetWidth;
                }
                moving_div.style.transform =
                    "translate3d(" + sum + "px, 0px, 0px)";
                moving_div.style.width =
                    item.querySelector("li:nth-child(" + index + ")")
                        .offsetWidth + "px";
            }
        }
    });

    if (window.innerWidth < 991) {
        total.forEach(function (item, i) {
            if (!item.classList.contains("flex-column")) {
                item.classList.add("flex-column", "on-resize");
            }
        });
    } else {
        total.forEach(function (item, i) {
            if (item.classList.contains("on-resize")) {
                item.classList.remove("flex-column", "on-resize");
            }
        });
    }
});

function getEventTarget(e) {
    e = e || window.event;
    return e.target || e.srcElement;
}

// logut confirmation
const btnLogout = document.getElementById("btn-logout");
if (btnLogout) {
    btnLogout.addEventListener("click", () => {
        Swal.fire({
            title: "Konfirmasi Keluar",
            text: "Apakah Anda yakin ingin keluar?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Keluar!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: "btn bg-gradient-primary",
                cancelButton: "btn bg-gradient-secondary me-2",
            },
            buttonsStyling: false,
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("logout-form").submit();
            }
        });
    });
}
