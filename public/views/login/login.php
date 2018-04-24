<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="../asset/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
    	@import "bourbon";
      body {
      	background: rgba(146,192,221,1);
background: -moz-radial-gradient(center, ellipse cover, rgba(146,192,221,1) 0%, rgba(117,174,209,1) 41%, rgba(74,146,191,1) 100%);
background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, rgba(146,192,221,1)), color-stop(41%, rgba(117,174,209,1)), color-stop(100%, rgba(74,146,191,1)));
background: -webkit-radial-gradient(center, ellipse cover, rgba(146,192,221,1) 0%, rgba(117,174,209,1) 41%, rgba(74,146,191,1) 100%);
background: -o-radial-gradient(center, ellipse cover, rgba(146,192,221,1) 0%, rgba(117,174,209,1) 41%, rgba(74,146,191,1) 100%);
background: -ms-radial-gradient(center, ellipse cover, rgba(146,192,221,1) 0%, rgba(117,174,209,1) 41%, rgba(74,146,191,1) 100%);
background: radial-gradient(ellipse at center, rgba(146,192,221,1) 0%, rgba(117,174,209,1) 41%, rgba(74,146,191,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#92c0dd', endColorstr='#4a92bf', GradientType=1 );

      }

      .wrapper {	
      	margin-top: calc(0px + 13%);
        margin-bottom: 80px;
      }
      @media (max-width: 768px){
          .wrapper {  
            margin-top: calc(0px + 40%);
            margin-bottom: 80px;
          }
      }
      .form-signin {
        max-width: 380px;
        padding: 15px 35px 45px;
        margin: 0 auto;
        background-color: #fff;
        border: 1px solid rgba(0,0,0,0.1);  
        box-shadow: 0px 0px 89px -8px rgba(0,0,0,0.75);
        .form-signin-heading,
      	.checkbox {
      	  margin-bottom: 30px;
      	}

      	.checkbox {
      	  font-weight: normal;
      	}

      	.form-control {
      	  position: relative;
      	  font-size: 16px;
      	  height: auto;
      	  padding: 10px;
      		@include box-sizing(border-box);

      		&:focus {
      		  z-index: 2;
      		}
      	}

      	input[type="text"] {
      	  margin-bottom: -1px;
      	  border-bottom-left-radius: 0;
      	  border-bottom-right-radius: 0;
      	}

      	input[type="password"] {
      	  margin-bottom: 20px;
      	  border-top-left-radius: 0;
      	  border-top-right-radius: 0;
      	}
      }

    </style>
  </head>
  <body>
    <div class="wrapper">
      <form class="form-signin" action="checklogin.php" method="POST">       
        <h2 class="form-signin-heading">Please login</h2>
        <input type="text" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
        <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
        
        <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
      </form>
      <div style="max-width: 380px;margin: auto;padding-top: 10px;">
        <div style="float: left;text-align: center;min-width: 49%;">
        <a href="">
            <img src="/views/asset/img/icon-free.png" width="65px" height="60px"><br>
            
        </a>
      </div>
      <div style="float: right;text-align: center;min-width: 49%;">
        <a href="">
            <img src="/views/asset/img/icon-silla.png" width="60px" height="60px"><br>
            
        </a>
      </div>
      </div>
    </div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../asset/js/bootstrap.min.js"></script>
  </body>
</html>