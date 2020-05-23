if (typeof jQuery == "undefined") {
    var s = document.createElement("div")
    s.style.color = "#f00"
    s.style.fontSize = "18px"
    s.innerHTML = "jQuery Library is missing"
    var c = document.getElementsByTagName("div")[0]
    c.parentNode.insertBefore(s, c)
} else {
    jQuery(document).ready(function($) {
        $("#dob").datepicker({
            dateFormat: "dd/mm/yy",
            maxDate: "-18Y",
            changeMonth: true,
            changeYear: true,
        })
        $("#wrg-subcribe-form").on("submit", function(e) {
            e.preventDefault()
            if ($("#fname").val() == null || $("#fname").val() == "") {
                $("#fname").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>First Name is Required</span>"
                ).insertAfter("#fname")
                $("#fname").focus()
            } else if ($("#lname").val() == null || $("#lname").val() == "") {
                $("#lname").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>Last Name is Required</span>"
                ).insertAfter("#lname")
                $("#lname").focus()
            } else if ($("#email").val() == null || $("#email").val() == "") {
                $("#email").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>Email is Required</span>"
                ).insertAfter("#email")
                $("#email").focus()
            } else if ($("#phone").val() == null || $("#phone").val() == "") {
                $("#phone").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>Phone Number is Required</span>"
                ).insertAfter("#phone")
                $("#phone").focus()
            } else if ($("#dob").val() == null || $("#dob").val() == "") {
                $("#dob").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>Dob is Required</span>"
                ).insertAfter("#dob")
                $("#dob").focus()
            } else if ($("#website").val() == null || $("#website").val() == "") {
                $("#website").next(".error").remove()
                $(
                    "<span class='error' style='color:#a00;'>Website is Required</span>"
                ).insertAfter("#website")
                $("#website").focus()
            } else {
                $.ajax({
                    url: ajax_object.ajax_url,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        var response = JSON.parse(res)
                        $(
                            "<p style='color:#0f0'>Please find the Database Insert ID: " +
                            response.insert_id +
                            "</p>"
                        ).insertAfter("#wrg-subcribe-form")
                        $(
                            "<p style='color:#0f0'>Please find the Mailchimp Insert Status: " +
                            response.mailchimp +
                            "</p>"
                        ).insertAfter("#wrg-subcribe-form")
                    },
                })
            }
        })
    })
}