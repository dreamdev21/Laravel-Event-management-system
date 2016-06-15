<!DOCTYPE html>
<html>
<head>
  <title>
      Attendize QRCode Check In: {{ $event->title }}
  </title>

    {!! HTML::style('assets/stylesheet/application.css') !!}
    {!! HTML::style('assets/stylesheet/qrcode-check-in.css') !!}
  {!! HTML::script('vendor/jquery/jquery.js') !!}

  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">

  @include('Shared/Layouts/ViewJavascript')

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->

  <script>
      $(function() {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-Token': "<?php echo csrf_token() ?>"
              }
          });
      });
  </script>
  {!! HTML::script('vendor/qrcode-scan/llqrcode.js') !!}
  {!! HTML::script('vendor/qrcode-scan/webqr.js') !!}
</head>
<body>
  <div id="main">
    <header id="header">
      <h2 class="text-center"><img style="width: 40px;" class="logo" alt="Attendize" src="{{ asset('/assets/images/logo-dark.png') }}"/><br><span style="font-size: 0.7em;">Check In: <strong>{{ $event->title }}</strong></span></h2>
    </header>

    <hr>

    @if(session()->has('success_message'))
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-xs-12">
                <div class="alert alert-success alert-dismissible text-center" role="alert">
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <p><strong>Success</strong>: {{ session('success_message') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div id="mainbody">
      <table class="tsel" border="0" width="100%">
        <tr>
         <td valign="top" align="center" width="50%">
          <table class="tsel" border="0">
            <tr>
              <td colspan="2" align="center">
                <div id="outdiv">
                </div>
              </td>
            </tr>
          </table>
         </td>
        </tr>
        <tr>
          <td colspan="3" align="center">
            <p id="help-text">Put the QR code in front of your Camera (Not too close)</p>
          </td>
        </tr>
        <tr>
          <td colspan="3" align="center">
            <p style="position: relative; bottom: -2em;"><a onclick="event.preventDefault(); workingAway = false; load();" href="{{ Request::url() }}"><i class="fa fa-refresh"></i> Scan another ticket</a></p>
            <div id="result"></div>
          </td>
        </tr>
      </table>
    </div>&nbsp;

    <footer id="footer">
      <br>
      <br>
      <h5 align="center" style="color: #6D717A;">Powered By <a href="https://www.attendize.com/">Attendize</a> </h5>
    </footer>
  </div>

  <canvas id="qr-canvas" width="800" height="600"></canvas>
  <script type="text/javascript">load();</script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
