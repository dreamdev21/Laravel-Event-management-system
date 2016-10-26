$(function() {
    $('form.ajax').on('submit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var $form =
                $(this),
                $submitButton = $form.find('input[type=submit]'),
                ajaxFormConf = {
                    delegation: true,
                    beforeSerialize: function(jqForm, options) {
                        window.doSubmit = true;
                        clearFormErrors(jqForm[0]);
                        toggleSubmitDisabled($submitButton);
                    },
                    beforeSubmit: function() {
                        $submitButton = $form.find('input[type=submit]');
                        toggleSubmitDisabled($submitButton);
                        return window.doSubmit;
                    },
                    error: function(data, statusText, xhr, $form) {
                        $submitButton = $form.find('input[type=submit]');

                        // Form validation error.
                        if (422 == data.status) {
                            processFormErrors($form, $.parseJSON(data.responseText));
                            return;
                        }

                        toggleSubmitDisabled($submitButton);
                        showMessage('Whoops!, it looks like the server returned an error.\n\
                   Please try again, or contact the webmaster if the problem persists.');
                    },
                    success: function(data, statusText, xhr, $form) {
                        var $submitButton = $form.find('input[type=submit]');

                        if (data.message) {
                            showMessage(data.message);
                        }
                        switch (data.status) {
                            case 'success':

                                if (data.redirectUrl) {
                                    if(data.redirectData)  {
                                        $.redirectPost(data.redirectUrl, data.redirectData);
                                    } else {
                                        document.location.href = data.redirectUrl;
                                    }
                                }
                                break;

                            case 'error':
                                if (data.messages) {
                                    processFormErrors($form, data.messages);
                                    return;
                                }
                                break;

                            default:
                                break;


                        }

                        toggleSubmitDisabled($submitButton);


                    },
                    dataType: 'json'
                };

        toggleSubmitDisabled($submitButton);

        if ($form.hasClass('payment-form') && !$('#pay_offline').is(":checked")) {
            clearFormErrors($('.payment-form'));

            Stripe.setPublishableKey($form.data('stripe-pub-key'));

            var
                    noErrors = true,
                    $cardNumber = $('.card-number'),
                    $cardName = $('.card-name'),
                    $cvcNumber = $('.card-cvc'),
                    $expiryMonth = $('.card-expiry-month'),
                    $expiryYear = $('.card-expiry-year');


            if (!Stripe.validateCardNumber($cardNumber.val())) {
                showFormError($cardNumber, 'The credit card number appears to be invalid.');
                noErrors = false;
            }

            if (!Stripe.validateCVC($cvcNumber.val())) {
                showFormError($cvcNumber, 'The CVC number appears to be invalid.');
                noErrors = false;
            }

            if (!Stripe.validateExpiry($expiryMonth.val(), $expiryYear.val())) {
                showFormError($expiryMonth, 'The expiration date appears to be invalid.');
                showFormError($expiryYear, '');
                noErrors = false;
            }

            if (noErrors) {
                Stripe.card.createToken({
                    name: $cardName.val(),
                    number: $cardNumber.val(),
                    cvc: $cvcNumber.val(),
                    exp_month: $expiryMonth.val(),
                    exp_year: $expiryYear.val()
                },
                function(status, response) {

                    if (response.error) {
                        clearFormErrors($('.payment-form'));
                        showFormError($('*[data-stripe=' + response.error.param + ']', $('.payment-form')), response.error.message);
                        toggleSubmitDisabled($submitButton);
                    } else {
                        var token = response.id;
                        $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                        $form.ajaxSubmit(ajaxFormConf);
                    }

                });
            } else {
                showMessage('Please check your card details and try again.');
                toggleSubmitDisabled($submitButton);
            }

        } else {
            $form.ajaxSubmit(ajaxFormConf);
        }
    });

    $('a').smoothScroll({
        offset: -60
    });


    /* Scroll to top */
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.totop').fadeIn();
        } else {
            $('.totop').fadeOut();
        }
    });

    $('#organiserHead').on('click', function(e) {
        e.stopImmediatePropagation();
        $('#organiser')[0].scrollIntoView();
    });

    $('#contact_organiser').on('click', function(e) {
        e.preventDefault();
        $('.contact_form').slideToggle();
    });

    $('#mirror_buyer_info').on('click', function(e) {
        $('.ticket_holder_first_name').val($('#order_first_name').val());
        $('.ticket_holder_last_name').val($('#order_last_name').val());
        $('.ticket_holder_email').val($('#order_email').val());
    });

    $('.card-number').payment('formatCardNumber');
    $('.card-cvc').payment('formatCardCVC');

    $('#pay_offline').change(function () {
        $('.online_payment').toggle(!this.checked);
        $('.offline_payment').toggle(this.checked);

        // Disable CC form inputs to prevent Chrome trying to validate hidden fields
        $('.online_payment input,  .online_payment select').attr('disabled', this.checked);

    }).change();


});

function processFormErrors($form, errors)
{
    $.each(errors, function (index, error)
    {
        var selector = (index.indexOf(".") >= 0) ? '.' + index.replace(/\./g, "\\.") : ':input[name=' + index + ']';
        var $input = $(selector, $form);

        if ($input.prop('type') === 'file') {
            $('#input-' + $input.prop('name')).append('<div class="help-block error">' + error + '</div>')
                .parent()
                .addClass('has-error');
        } else {
            if($input.parent().hasClass('input-group')) {
                $input = $input.parent();
            }

            $input.after('<div class="help-block error">' + error + '</div>')
                .parent()
                .addClass('has-error');
        }
    });

    var $submitButton = $form.find('input[type=submit]');
    toggleSubmitDisabled($submitButton);
}

/**
 * Toggle a submit button disabled/enabled
 *
 * @param element $submitButton
 * @returns void
 */
function toggleSubmitDisabled($submitButton) {

    if ($submitButton.hasClass('disabled')) {
        $submitButton.attr('disabled', false)
                .removeClass('disabled')
                .val($submitButton.data('original-text'));
        return;
    }

    $submitButton.data('original-text', $submitButton.val())
            .attr('disabled', true)
            .addClass('disabled')
            .val('Just a second...');
}

/**
 * Clears given form of any error classes / messages
 *
 * @param {Element} $form
 * @returns {void}
 */
function clearFormErrors($form) {
    $($form)
            .find('.error.help-block')
            .remove();
    $($form).find(':input')
            .parent()
            .removeClass('has-error');
    $($form).find(':input')
            .parent().parent()
            .removeClass('has-error');
}

function showFormError($formElement, message) {
    $formElement.after('<div class="help-block error">' + message + '</div>')
            .parent()
            .addClass('has-error');
}

/**
 * Shows users a message.
 * Currently uses humane.js
 *
 * @param string message
 * @returns void
 */
function showMessage(message) {
    humane.log(message, {
        timeoutAfterMove: 3000,
        waitForMove: true
    });
}

function hideMessage() {
    humane.remove();
}

/**
 * Counts down to the given number of seconds
 *
 * @param element $element
 * @param int seconds
 * @returns void
 */
function setCountdown($element, seconds) {

    var endTime, mins, msLeft, time, twoMinWarningShown = false;

    function twoDigits(n) {
        return (n <= 9 ? "0" + n : n);
    }

    function updateTimer() {
        msLeft = endTime - (+new Date);
        if (msLeft < 1000) {
            alert("You have run out of time! You will have to restart the order process.");
            location.reload();
        } else {

            if (msLeft < 120000 && !twoMinWarningShown) {
                showMessage("You only have 2 minutes left to complete this order!");
                twoMinWarningShown = true;
            }

            time = new Date(msLeft);
            mins = time.getUTCMinutes();
            $element.html('<b>' + mins + '</b> minutes and <b>' + twoDigits(time.getUTCSeconds()) + '</b> seconds');
            setTimeout(updateTimer, time.getUTCMilliseconds() + 500);
        }
    }

    endTime = (+new Date) + 1000 * seconds + 500;
    updateTimer();
}

$.extend(
    {
        redirectPost: function(location, args)
        {
            var form = '';
            $.each( args, function( key, value ) {
                value = value.split('"').join('\"')
                form += '<input type="hidden" name="'+key+'" value="'+value+'">';
            });
            $('<form action="' + location + '" method="POST">' + form + '</form>').appendTo($(document.body)).submit();
        }
    });

/*!
 * Smooth Scroll - v1.4.13 - 2013-11-02
 * https://github.com/kswedberg/jquery-smooth-scroll
 * Copyright (c) 2013 Karl Swedberg
 * Licensed MIT (https://github.com/kswedberg/jquery-smooth-scroll/blob/master/LICENSE-MIT)
 */
(function(t) {
    function e(t) {
        return t.replace(/(:|\.)/g, "\\$1")
    }
    var l = "1.4.13", o = {}, s = {exclude: [], excludeWithin: [], offset: 0, direction: "top", scrollElement: null, scrollTarget: null, beforeScroll: function() {
        }, afterScroll: function() {
        }, easing: "swing", speed: 400, autoCoefficent: 2, preventDefault: !0}, n = function(e) {
        var l = [], o = !1, s = e.dir && "left" == e.dir ? "scrollLeft" : "scrollTop";
        return this.each(function() {
            if (this != document && this != window) {
                var e = t(this);
                e[s]() > 0 ? l.push(this) : (e[s](1), o = e[s]() > 0, o && l.push(this), e[s](0))
            }
        }), l.length || this.each(function() {
            "BODY" === this.nodeName && (l = [this])
        }), "first" === e.el && l.length > 1 && (l = [l[0]]), l
    };
    t.fn.extend({scrollable: function(t) {
            var e = n.call(this, {dir: t});
            return this.pushStack(e)
        }, firstScrollable: function(t) {
            var e = n.call(this, {el: "first", dir: t});
            return this.pushStack(e)
        }, smoothScroll: function(l, o) {
            if (l = l || {}, "options" === l)
                return o ? this.each(function() {
                    var e = t(this), l = t.extend(e.data("ssOpts") || {}, o);
                    t(this).data("ssOpts", l)
                }) : this.first().data("ssOpts");
            var s = t.extend({}, t.fn.smoothScroll.defaults, l), n = t.smoothScroll.filterPath(location.pathname);
            return this.unbind("click.smoothscroll").bind("click.smoothscroll", function(l) {
                var o = this, r = t(this), i = t.extend({}, s, r.data("ssOpts") || {}), c = s.exclude, a = i.excludeWithin, f = 0, h = 0, u = !0, d = {}, p = location.hostname === o.hostname || !o.hostname, m = i.scrollTarget || (t.smoothScroll.filterPath(o.pathname) || n) === n, S = e(o.hash);
                if (i.scrollTarget || p && m && S) {
                    for (; u && c.length > f; )
                        r.is(e(c[f++])) && (u = !1);
                    for (; u && a.length > h; )
                        r.closest(a[h++]).length && (u = !1)
                } else
                    u = !1;
                u && (i.preventDefault && l.preventDefault(), t.extend(d, i, {scrollTarget: i.scrollTarget || S, link: o}), t.smoothScroll(d))
            }), this
        }}), t.smoothScroll = function(e, l) {
        if ("options" === e && "object" == typeof l)
            return t.extend(o, l);
        var s, n, r, i, c = 0, a = "offset", f = "scrollTop", h = {}, u = {};
        "number" == typeof e ? (s = t.extend({link: null}, t.fn.smoothScroll.defaults, o), r = e) : (s = t.extend({link: null}, t.fn.smoothScroll.defaults, e || {}, o), s.scrollElement && (a = "position", "static" == s.scrollElement.css("position") && s.scrollElement.css("position", "relative"))), f = "left" == s.direction ? "scrollLeft" : f, s.scrollElement ? (n = s.scrollElement, /^(?:HTML|BODY)$/.test(n[0].nodeName) || (c = n[f]())) : n = t("html, body").firstScrollable(s.direction), s.beforeScroll.call(n, s), r = "number" == typeof e ? e : l || t(s.scrollTarget)[a]() && t(s.scrollTarget)[a]()[s.direction] || 0, h[f] = r + c + s.offset, i = s.speed, "auto" === i && (i = h[f] || n.scrollTop(), i /= s.autoCoefficent), u = {duration: i, easing: s.easing, complete: function() {
                s.afterScroll.call(s.link, s)
            }}, s.step && (u.step = s.step), n.length ? n.stop().animate(h, u) : s.afterScroll.call(s.link, s)
    }, t.smoothScroll.version = l, t.smoothScroll.filterPath = function(t) {
        return t.replace(/^\//, "").replace(/(?:index|default).[a-zA-Z]{3,4}$/, "").replace(/\/$/, "")
    }, t.fn.smoothScroll.defaults = s
})(jQuery);
