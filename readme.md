
<p align="center">
  <img src="https://www.attendize.com/img/logo-dark.png" alt="Attendize"/>
</p>
# Attendize 
### Open-source ticket selling and event management platform

[![Join the chat at https://gitter.im/Attendize/Attendize](https://badges.gitter.im/Attendize/Attendize.svg)](https://gitter.im/Attendize/Attendize?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

https://www.attendize.com

> PLEASE NOTE: Attendize is in the early stages of development and therefore is likely to contain bugs and unfinished features. Be wary about using Attendize in a production environment.


######Demo Event Page: http://attendize.website/e/1/acmes-amazing-demo-event


*Attendize* is an open-source event ticketing and event management application built using the Laravel PHP framework. Attendize was created to offer event organisers a simple solution to managing general admission events, without paying extortionate service fees.


##Features
 - Beautiful mobile friendly event pages
 - Easy attendee management - Refunds, Messaging etc.
 - Data export - attendees list to XLS, CSV etc.
 - Generate print friendly attendee list
 - Ability to manage unlimited organisers / events
 - Manage multiple organisers 
 - Real-time event statistics
 - Customizable event pages
 - Multiple currency support
 - Quick and easy checkout process
 - Customizable tickets - with QR codes, organiser logos etc.
 - Fully brandable - Have your own logos on tickets etc.
 - Affiliate tracking
    - track sales volume / number of visits generated etc.
 - Widget support - embed ticket selling widget into existing websites / WordPress blogs
 - Social sharing 
 - Support multiple payment gateways - Stripe, PayPal & Coinbase so far, with more being added
 - Support for offline payments
 - Refund payments - partial refund & full refunds
 - Ability to add service charge to tickets
 - Messaging - eg. Email all attendees with X ticket
 - Public event listings page for organisers
 - Ability to ask custom questions during checkout
 - Browser based QR code scanner for door management
    
##Upcoming Features
 - Theme support
 - Plugin Support
 - Localisation 
 - IOS/Android check-in / door management apps
 - Coupon/discount code support
 - Support for more payment providers
 - WordPress Plug-in 


## Official Documentation

Limited Documentation available at https://www.attendize.com/documentation.php. Github will be updated with more comprehensive documentation soon.


## Contribution

Feel free to fork and contribute. I could use the help!

## Docker dev environment

To run a docker dev entionment do the following:

```
git clone https://github.com/Attendize/Attendize
cd Attendize
cp .env.example .env
docker-compose build
docker run --rm -v $(pwd):/app composer/composer install
docker-compose up
docker-compose run php php artisan attendize:install
chmod a+w -R storage
chmod a+w -R public/user_content
```

Attendize will be available at `http://localhost:8080` and maildev at `http://localhost:1080`

## License

Attendize is open-sourced software licensed under the Attribution Assurance License. See [https://www.attendize.com/licence.php](https://www.attendize.com/licence.php) for further details. We also have white-label licence options available.

## Contributors 

* Brett B ([Github](https://github.com/bretto36))
* G0dLik3 ([Github](https://github.com/G0dLik3))
* Honoré Hounwanou ([Github](http://github.com/mercuryseries)) <mercuryseries@gmail.com>
* James Campbell ([Github](https://github.com/jncampbell))
* JapSeyz ([Github](https://github.com/JapSeyz))
* Mark Walet ([Github](https://github.com/markwalet))
