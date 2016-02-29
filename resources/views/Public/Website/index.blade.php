<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Attendize</title>

       {!! HTML::style('website_assets/stylesheet/main.css') !!}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->


        <style>

            .interface {
                width: 792px;
                height: 400px;
                margin: 70px auto 0px auto; }
            @media only screen and (max-width: 1024px) {
                .interface {
                    width: 580px;
                    height: 300px;
                    margin: 0px auto 0px auto; } }
            @media only screen and (max-width: 600px) {
                .interface {
                    width: 280px;
                    height: 140px;
                    margin: 0px auto 0px auto; } }

            .interface-screen {
                position: relative;
                padding: 40px;
                background: -webkit-linear-gradient(0deg, #ced0d6, #eeeff2);
                background: linear-gradient(90deg, #ced0d6, #eeeff2);
                -webkit-background-size: cover cover;
                background-size: cover;
                border: 3px solid #7d828c;
                border-radius: 30px;
                -webkit-transform-style: preserve-3d;
                transform-style: preserve-3d;
                -webkit-transform-origin: 50% 100%;
                -ms-transform-origin: 50% 100%;
                transform-origin: 50% 100%;
                -webkit-transform: rotateX(0deg);
                transform: rotateX(0deg); }
            @media only screen and (max-width: 1024px) {
                .interface-screen {
                    padding: 30px;
                    border-radius: 20px; } }
            @media only screen and (max-width: 600px) {
                .interface-screen {
                    padding: 15px;
                    border-radius: 10px; } }
            .interface-screen:after {
                position: absolute;
                left: 0;
                right: 0;
                top: 15px;
                content: "";
                width: 10px;
                height: 10px;
                margin: 0 auto;
                background-color: #222;
                border-radius: 5px;
                -webkit-box-shadow: 40px 0px 0px -2px  #7d828c, -40px 0px 0px -2px  #7d828c;
                box-shadow: 40px 0px 0px -2px  #7d828c, -40px 0px 0px -2px  #7d828c; }
            @media only screen and (max-width: 1024px) {
                .interface-screen:after {
                    top: 12px;
                    width: 6px;
                    height: 6px;
                    -webkit-box-shadow: 30px 0px 0px -1px #7d828c, -30px 0px 0px -1px #7d828c;
                    box-shadow: 30px 0px 0px -1px #7d828c, -30px 0px 0px -1px #7d828c; } }
            @media only screen and (max-width: 600px) {
                .interface-screen:after {
                    top: 6px;
                    width: 2px;
                    height: 2px;
                    -webkit-box-shadow: 5px 0px 0px -1px #7d828c, 5px 0px 0px -1px #7d828c;
                    box-shadow: 5px 0px 0px -1px #7d828c, 5px 0px 0px -1px #7d828c; } }
            .interface-screen img {
                width: 100%;
                /*		box-shadow: 0 0 12px 0px rgba(235,235,255,0.25);*/
                -webkit-animation: screenglow 0.5s linear infinite;
                animation: screenglow 0.5s linear infinite; }
            </style>
        </head>
        <body>
            <!--Header Section-->
            <div id="home" class="top">
            <div class="container">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 logo">
                    <img src="website_assets/images/header/logo.png" alt=".Square">
                </div>
                <!--Navigation-->
                <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 pull-right ">
                    <div class="nav">
                        <ul class="navi">
                            <li ><a href="#features">Features</a></li>
                            <li><a href="#pricing">Pricing</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="big_head_mask"   >

            <section class="big_head">
                <div class="intro" >
                    <div class="col-md-12">
                        <div class="content">
                            <h1>Sell Tickets The Right Way</h1>
                            <p>Attendize offers a <b>powerful</b> & <b>cost affective</b> way to sell tickets online and manage your events.  </p>
                            <div class="btn-cta"><a href="#signup">REQUEST INVITATION</a></div>
                            <div class="interface ">
                                <div class="interface-screen" id="screen">
                                    <img src="//placehold.it/1437x753" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>



      
        <!--Features Section-->
        <section class="feature-styles" id="features" >
            <div class="container text-center">
                <h1>All the tools you need to sell tickets online</h1>
            </div>
            <div class="container feature-container">
                <div class="col-lg-push-6 col-md-6 col-sm-12 col-xs-12 text-center">
                    <div class="feature-icon red hidden-xs"><i class="fa fa-trello" ></i></div>
                    <div class="feature-icon green hidden-xs"><i class="fa fa-laptop" ></i></div>
                    <img src="website_assets/images/body/howitwork-img1.png" alt="How it works?"></div>
                <div class="col-lg-pull-6 col-md-6 col-sm-12 col-xs-12">
                    <h1>Sell More Tickets</h1>
                    <div class="txt">
                        With powerful event management tools, beautiful event pages and 
                    </div>
                    <div class="getstarted"><a href="#signup">Get Started Today! <i class="fa fa-long-arrow-right"></i></a></div>
                </div>



            </div>
            <div class="container feature-container">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center">
                    <div class="feature-icon blue hidden-xs"><i class="fa fa-trello" ></i></div>
                    <div class="feature-icon orange hidden-xs"><i class="fa fa-laptop" ></i></div>
                    <img src="website_assets/images/body/devtime-img.png" alt="Reduce development time"></div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <h1>Reduce development time</h1>
                    <div class="txt">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam suscipit aliquet felis, quis ultrices orci condiment. Suspendisse ut eleifend sem, nec iaculis nulla. </div>
                    <div class="getstarted"><a href="#signup">Get Started Today! <i class="fa fa-long-arrow-right"></i></a></div>
                </div>

            </div>
            <div class="container feature-container">
                <div class="col-lg-push-6 col-md-6 col-sm-12 col-xs-12 text-center">
                    <div class="feature-icon pgreen hidden-xs"><i class="fa fa-trello" ></i></div>
                    <div class="feature-icon yellow hidden-xs"><i class="fa fa-laptop" ></i></div>
                    <img src="website_assets/images/body/alldevices-img.png" alt="Works on all devices"></div>
                <div class="col-lg-pull-6 col-md-6 col-sm-12 col-xs-12">
                    <h1>Works on all devices</h1>
                    <div class="txt">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam suscipit aliquet felis, quis ultrices orci condiment. Suspendisse ut eleifend sem, nec iaculis nulla. </div>
                    <div class="getstarted"><a href="#signup">Get Started Today! <i class="fa fa-long-arrow-right"></i></a></div>
                </div>
            </div>
        </section>
        
        
        
        
        
          <!--start features-->
          <section id="services" class="wrapper-white">
        <div class="container">

            <!-- SERVICE TOP IMAGE -->
            <div class="row">
                <div class="col-md-12">
                    <div class="content-photo">
                        <img class="services-img" src="img/mockup/imac-01.jpg" alt="???">
                    </div>
                </div>
            </div>
            <!-- /END SERVICE TOP IMAGE -->

            <div class="row">
                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-yellow icon_adjust-horiz"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Easy to Customize</h3>
                        <p>
                            Uptat pos reseces nos aut doluptatus eat a volupta consed quidemquos ad untur, odis ea nonsectiis dolorum consequi recture, quiaqu idemquos denis denis volescia sendunt rem dolupta.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->

                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-pink icon_archive_alt"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Bootstrap Based</h3>
                        <p>
                            denis denis volescia sendunt rem doluptam, que soluptas re volorep erundi tecti occabore rene evero aut mincili quissin pa vel est lique pro eariatae ped ut essit vel ipsunt ommolum.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->
            </div>

            <div class="row">
                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-red fa fa-bars"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Responsive Design</h3>
                        <p>
                            Uptat pos reseces nos aut doluptatus eat a volupta consed quidemquos ad untur, odis ea nonsectiis dolorum consequi recture, quiaqu idemquos denis denis volescia sendunt rem dolupta.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->

                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-blue fa fa-tint"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Fresh Colors</h3>
                        <p>
                            denis denis volescia sendunt rem doluptam, que soluptas re volorep erundi tecti occabore rene evero aut mincili quissin pa vel est lique pro eariatae ped ut essit vel ipsunt ommolum.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->
            </div>

            <div class="row">
                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Box Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-teal fa fa-life-ring"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Premium Support</h3>
                        <p>
                            Uptat pos reseces nos aut doluptatus eat a volupta consed quidemquos ad untur, odis ea nonsectiis dolorum consequi recture, quiaqu idemquos denis denis volescia sendunt rem dolupta.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->

                <!-- SERVICE BOX -->
                <div class="col-md-6 col-sm-6 services-box">

                    <!-- Service Box Icon -->
                    <div class="icon-box">
                        <div class="icon-clean txt-green fa fa-cogs"></div>
                    </div>

                    <!-- Service Txt Box -->
                    <div class="services-txt">
                        <h3>Tons of Options</h3>
                        <p>
                            denis denis volescia sendunt rem doluptam, que soluptas re volorep erundi tecti occabore rene evero aut mincili quissin pa vel est lique pro eariatae ped ut essit vel ipsunt ommolum.
                        </p>
                        <a href="#" class="meta">Learn more about this section</a>
                    </div>
                </div>
                <!-- /END SERVICE BOX -->
            </div>

        </div>
        <!-- /End Container -->
    </section>  
        <!--Opt-in Section-->
        <section class="bggray" id="signup">

            <div class="container msg">
                <div id="message" style="display:none"></div>
                <div class="col-lg-12 col-md-12 col-sm-12 opt-container">

                    <h1>Are you an event organiser?</h1>
                    <h2>Signup absolutly <span>FREE</span> to .Square now and start creating your websites.</h2>

                    <form id="contactform" action method="post">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form">
                            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12"><input name="email" type="text" id="email" placeholder="Enter Your Email"></div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12"><div><input class="btn-blue" type="button" value="submit" id="submit"></div></div></div></form>


                </div>
            </div>
        </section>
        <section id="pricing">
            <div class="container">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pricing">
                    <h1>Singup to .Square</h1>
                    <h2>Start creating your websites now!</h2>
                </div>
            </div>
            <div class="container">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 pricing-box">
                    <div class="bggray price">
                        <div class="package">Basic</div>
                        <div class="divider"></div>
                        <div class="amount">$19</div>
                        <div class="duration">monthly</div>
                    </div>
                    <div class="featcontent">
                        <div class="feat-list">
                            <ul>
                                <li>2 Websites</li>
                                <li>1 Users</li>
                                <li>2 GB Storage</li>
                                <li>1000 GB Bandwith</li>
                            </ul>
                        </div>
                        <div class="signup-btn"><a href="#">Signup now</a></div>

                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 pricing-box">
                    <div class="bgorange price">
                        <div class="package">Standard</div>
                        <div class="divider"></div>
                        <div class="amount">$29</div>
                        <div class="duration">monthly</div>
                    </div>
                    <div class="featcontent">
                        <div class="feat-list">
                            <ul>
                                <li>2 Websites</li>
                                <li>1 Users</li>
                                <li>2 GB Storage</li>
                                <li>1000 GB Bandwith</li>
                            </ul>
                        </div>
                        <div class="signup-btn"><a href="#">Signup now</a></div>

                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 pricing-box">
                    <div class="bgblue price">
                        <div class="package">Premium</div>
                        <div class="divider"></div>
                        <div class="amount">$39</div>
                        <div class="duration">monthly</div>
                    </div>
                    <div class="featcontent">
                        <div class="feat-list">
                            <ul>
                                <li>2 Websites</li>
                                <li>1 Users</li>
                                <li>2 GB Storage</li>
                                <li>1000 GB Bandwith</li>
                            </ul>
                        </div>
                        <div class="signup-btn"><a href="#">Signup now</a></div>

                    </div>
                </div>
            </div>
        </section>
        <!--Contact Section-->
        <section id="contact">
            <div class="container">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 contact">
                    <h1>Get in touch</h1>
                    <h2>Have a question? do not hesitate to contact us.</h2>
                </div>
            </div>
            <div class="container contact-info">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                    <i class="fa fa-phone"></i>1.800.321.4567-8
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                    <i class="fa fa-envelope"></i>support@dotsquare.com
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 ">
                    <i class="fa fa-map-marker"></i>Some street, New York, USA
                </div>
            </div>
            <div class="container contact-form">
                <form id="contactmsg" action method="post">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <input name="name" type="text" id="name" placeholder="Enter Your Name" >
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <input name="email" type="text" id="email" placeholder="Enter Your Email" >
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <input name="phone" type="text" id="phone" placeholder="Enter Your Phone" >
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <textarea name="detail" id="detail"  rows="8" placeholder="Message"></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center"><input class="btn-message" type="button" value="Send Message" id="sendmsg"></div>
                </form>
            </div>
            <div id="contact-message" style="display:none"></div>
        </section>
        <!-- Footer Section-->
        <section id="footer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 footer">
                <div class="footer-nav text-center">
                    <ul class="navi">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="social-icons text-center">
                    <ul>
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                    </ul>
                </div>
                <div class="copyright">Copyright © 2014 dotSquare. All rights reserved.</div>
            </div>
        </section>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
<!--	<script src="js/custom.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script defer src="js/jquery.flexslider-min.js"></script>-->

       {!! HTML::script('website_assets/javascript/custom.js'); !!}

    </body>
</html>