<?php

return [
    /*
    EventAttendedeesController.php
    */
    'ticket-does-not-exist'        =>"The ticket you have selected does not exist",
    'ticket-field-required'        =>"The ticket field is required.",
    'attendee-success-invited'        =>"Attendee Successfully Invited",
    'error-while-inviting'        =>"An error occurred while inviting this attendee. Please try again.",
    'error-while-inviting'        =>"You need to create a ticket before you can add an attendee.",
    'message-successfully-sent'        =>"Message Successfully Sent",
    'successfully-updated-attendee'        =>"Successfully Updated Attendee",
    'attendee-already-cancelled'        =>"Attendee Already Cancelled",
    'processing-refund-try-again'        =>"There has been a problem processing your refund. Please check your information and try again.",
    'successfully-cancelled-attenddee'        =>"Successfully Cancelled Attenddee",
    'ticket-successfully-resent'        =>"Ticket Successfully Resent",

    /*
    EventCheckInController.php
    */
    'attendee-already-checked'        =>"Attendee Already Checked",
    'attendee-successfully-checked'        =>"Attendee Successfully Checked",
    'invailid-ticket-try-again'        =>"Invalid Ticket! Please try again.",
    'attendees-checked-in'        =>" Attendee(s) Checked in.",

    /*
    EventCheckOutController.php
    */
    'no-tickets-selected'        =>"No tickets selected",
    'max-register'        =>"The maximum number of tickets you can register is :attribute",
    'min-register'        =>"You must select at least :attribute tickets.",
    'ticket-holder-firstname'        =>"Ticket holder :attribute 's first name is required",
    'ticket-holder-lastname'        =>"Ticket holder :attribute 's last name is required",
    'ticket-holder-email'        =>"Ticket holder :attribute 's email is required",
    'ticket-holder-email-appear'        =>"Ticket holder :attribute 's email appears to be invalid",
    'this-question-required'        =>"This question is required",
    'javascript-enable'        =>"Please enable Javascript in your browser.",
    'session-expired'        =>"Your session has expired.",
    'order-for-customer'        =>"Order for customer: ",
    'ticket-sales'        =>"Ticket sales ",
    'no-payment-gateway-configured'        =>"No payment gateway configured.",
    'error-processing-payment'        =>"Sorry, there was an error processing your payment. Please try again.",
    'you-cancelled-payment'        =>"You cancelled your payment. You may try again.",
    'error-problem-processing-order'        =>"Whoops! There was a problem processing your order. Please try again.",
    'give-name-for-event'        =>"You must give a name for the event organiser.",
    'issue-finding-organiser'        =>"There was an issue finding the organiser.",
    'event-successfully-update'        =>"Event Successfully Updated",
    'problem-uploading-image'        =>"There was a problem uploading your image.",
    /*
    EventCustomizeController.php
    */
    'keep-under-3000'        =>"Please keep the text under 3000 characters.",
    'social-setting-update'        =>"Social Settings Successfully Updated.",
    'enter-background-color'        =>"Please enter a background color.",
    'ticket-setting-updated'        =>"Ticket Settings Updated",
    'value-0-100'        =>"Please enter a value between 0 and 100",
    'check-format'        =>"Please check the format. It should be in the format 0.00.",
    'order-page-updated'        =>"Order Page Successfully Updated.",
    'ensure-uploading-image'        =>"Please ensure you are uploading an image (JPG, PNG, JPEG)",
    'ensure-uploading-image-larger'        =>"Please ensure the image is not larger than 2.5MB",
    'event-page-updated'        =>"Event Page Successfully Updated",
    /*
    EventPromoteController.php
    */
    'order-has-updated'        =>"The order has been updated.",
    'refund-amount-contain-numbers'        =>"Refund amount must only contain numbers.",
    'order-payment-status-updated'        =>"Order Payment Status Successfully Updated.",
    /*
    EventOrdersController.php
    */
    'invalid-ticket'        =>"Invalid Ticket! Please try again.",
    'check-in-all-tickets'        =>"Check in all tickets associated to this order",
    'attendee-has-checked'        =>"Warning: This attendee has already been checked in at ",
    'attendee-check-in'        =>" Attendee(s) Checked in.",
    /*
        EventSurveyController.php
    */
    'successfully-created-question'        =>"Successfully Created Question",
    'refreshing'        =>"Refreshing..",
    'successfully-edited-question'        =>"Successfully Edited Question",
    'successfully-deleted-question'        =>"Question Successfully Deleted",
    'cannot-deleted-question'        =>"This question can't be deleted",
    'woop-wrong'        =>"Whoops! Looks like something went wrong. Please try again.",
    'question-order-updated'        =>"Question Order Successfully Updated",
    /*
        EventTicketsController.php
    */
    'successfully-created-ticket'        =>"Successfully Created Ticket",
    'cannot-delete-ticket'        =>"Sorry, you can't delete this ticket as some have already been sold",
    'cannot-delete-ticket'        =>"Ticket Successfully Deleted",
    'ticket-failed-delete'        =>"Ticket Failed to delete",
    'quantity-available-cannot'        =>"Quantity available can't be less the amount sold or reserved.",
    'ticket-order-updated'        =>"Ticket Order Successfully Updated",
    /*
        ManageAccountController.php
    */
    'error-connecting-stripe'        =>"There was an error connecting your Stripe account. Please try again.",
    'success-connecting-stripe'        =>"You have successfully connected your Stripe account.",
    'account-successfully-updated'        =>"Account Successfully Updated",
    'payment-info-success'        =>"Payment Information Successfully Updated",
    'enter-valid-address'        =>"Please enter a valid E-mail address.",
    'email-address-required'        =>"E-mail address is required.",
    'email-address-use-already'        =>"E-mail already in use for this account.",

    /*
        MyBaseController.php
    */
    'error-woop-unknown-problem'        =>"Whoops! An unknown error has occurred. Please try again or contact support if the problem persists.",
    /*
        MyBaseController.php
    */
    'successfully-created-organiser'        =>"Successfully Created Organiser.",
    /*
        MyBaseController.php
    */
    'successfully-updated-organiser'        =>"Successfully Updated Organiser",
    'enter-header-background'        =>"Please enter a header background color.",
    'organiser-design-updated'        =>"Organiser Design Successfully Updated",
    'successfully-created-organiser'        =>"Successfully Created Organiser.",
    /*
        RemindersController.php
    */
    'your-password-reset-link'        =>"Your Password Reset Link",
    'password-successfully-reset'        =>"Password Successfully Reset",
    /*
        UserController.php
    */
    'enter-valid-email'        =>"Please enter a valid E-mail address.",
    'email-required'        =>"E-mail address is required.",
    'password-incorrect'        =>"This password is incorrect.",
    'email-already-use'        =>"This E-mail is already in use.",
    'enter-firstname'        =>"Please enter your first name.",
    'enter-lastname'        =>"Please enter your last name.",
    'successfully-saved-details'        =>"Successfully Saved Details",
    /*
        UserLoginController.php
    */
    'fill-in-your-email-password'        =>"Please fill in your email and password",
    'email-password-incorrect'        =>"Your username/password combination was incorrect",
    /*
       UserSignupController.php
    */
    'you-can-login'        =>"Success! You can now login.",
    'confirmation-code-missing'        =>"The confirmation code is missing or malformed.",
    'email-verified'        =>"Success! Your email is now verified. You can now login.",



];
