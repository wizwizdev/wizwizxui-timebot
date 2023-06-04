/**
 * wizwiz v7.5.3
 * https://github.com/wizwizdev/wizwizxui-timebot
 *
 * Copyright (c) @wizwizch
 */
function fadeOut() {
    var myDiv = document.getElementById("myDiv");
    var opacity = 1;
    var interval = setInterval(function () {
        if (opacity <= 0) {
            clearInterval(interval);
            myDiv.style.display = "none";
        } else {
            myDiv.style.opacity = opacity;
            opacity -= 0.1;
        }
    }, 100);
}

setTimeout(function () {
    fadeOut();
}, 2000); // Wait for 3 seconds before fading out the div




$(document).ready(function () {
    $("#insert_volume_gb").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "add-volume.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_volume_gb",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#insert_volume_day").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "add-volume.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_volume_day",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#insert_plans").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "plans.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_plans",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});

$(document).ready(function () {
    $("#insert_discount").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "discount.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_discount",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#insert_rahgozar").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "rahgozar.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_rahgozar",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});

$(document).ready(function () {
    $("#insert_gift").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "gift.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_gift",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});

$(document).ready(function () {
    $("#insert_software").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "software.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_software",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});

$(document).ready(function () {
    $("#save_admin").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "settings.php",
            type: "POST",
            data: $(this).serialize() + "&action=save_admin",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#save_backup").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "settings.php",
            type: "POST",
            data: $(this).serialize() + "&action=save_backup",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#save_pay").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "settings.php",
            type: "POST",
            data: $(this).serialize() + "&action=save_pay",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#save_state").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "settings.php",
            type: "POST",
            data: $(this).serialize() + "&action=save_state",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});
$(document).ready(function () {
    $("#insert_category").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "category.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_category",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});


$(document).ready(function () {
    $("#insert_server_backup").submit(function (event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "wizwizbackup.php",
            type: "POST",
            data: $(this).serialize() + "&action=insert_server_backup",
            success: function (response) {
                $("#result").html(response);
            }
        });
    });
});

