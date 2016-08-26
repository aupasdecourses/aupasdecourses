<!DOCTYPE html>
<html class="full" lang="en">
<!-- Make sure the <html> tag is set to the .full CSS class. Change the background image in the full.css file. -->
<head>

    <?php
        include_once('lib/functions.php');
        $data=csv_to_array('data.csv',',');
        $zipcode=$_GET['zipcode'];
        $img_path=img_replace($data[$zipcode]['nom']);
    ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Faites vos courses avec Au Pas De Courses, et bénéficiez du service de livraison ultralocal de vos commerçants de proximité à "<?php echo $data[$zipcode]['nom'];?>"." />
	<meta name="keywords" content="courses,ecommerce, local, paris, petits commerçants, alimentaire, produits de bouche, artisans, livraison, commis,boucher,primeur,poissonnier,<?php echo $data[$zipcode]['nom'];?>,<?php echo $zipcode;?>,boulanger,fromager,livraison" />
    <meta name="author" content="Pierre Mainguet pour Au Pas De Courses">
    <meta name="robots" content="INDEX,FOLLOW" />
	<link rel="icon" href="https://www.aupasdecourses.com/media/favicon/default/favicon_3.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="https://www.aupasdecourses.com/media/favicon/default/favicon_3.ico" type="image/x-icon" />

    <title>Au Pas De Courses <?php echo $data[$zipcode]['nom'];?> - Les meilleurs commerçants du quartier livrés chez vous</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!--Google Fonts-->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Raleway:300,400,500,700,600">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Montserrat:400,700">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Courgette">
    <link rel="stylesheet" type="text/css" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/assets/elegant-icons/style.css" media="all" />
    <link rel="stylesheet" href="css/ladda.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script type="text/javascript" src="https://www.aupasdecourses.com/js/googleanalytics/ga.js"></script>
	<script type="text/javascript" src="https://www.aupasdecourses.com/js/lib/jquery/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="https://www.aupasdecourses.com/js/lib/jquery/noconflict.js"></script>
	<script type="text/javascript" src="https://www.aupasdecourses.com/js/tawkto/tawkto.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />

	<!--Iphone webapp-->
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="format-detection" content="telephone=yes"/>
	<link rel="apple-touch-icon" sizes="72x72" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/apple-touch-icon-72x72.png"/>
	<link rel="apple-touch-icon" sizes="114x114" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/apple-touch-icon-114x114.png"/>
	<link rel="apple-touch-icon" sizes="180x180" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/apple-touch-icon-180x180.png"/>
	<link rel="apple-touch-icon" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/apple-touch-icon-180x180.png"/>
	<link rel="apple-touch-startup-image" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/splash-startup.png"/>
	<!--Chrome webapp-->
	<link rel="manifest" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/webapp/manifest.json"/>
	<meta name="mobile-web-app-capable" content="yes"/>
	<link rel="icon" sizes="192x192" href="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/apple-touch-icon-192x192.png"/>
	<!--Other webapp-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="HandheldFriendly" content="true"/>
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/screens.png" />
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/bg-2.jpg" />
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/logo@2x.png" />
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/livraison.jpg" />
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/bg-1.jpg" />
	<meta property="og:image" content="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/bg-3.jpg" />
	<meta property="og:title" content="Au Pas De Courses - Mon Quartier, mes commerçants." />
	<meta property="og:url" content="https://www.aupasdecourses.com" />
	<meta property="og:type" content="website" />
	<meta property="og:description" content="Commandez facilement et faites vous livrer le soir-même les produits frais et artisanaux des commerçants de votre quartier!" />

</head>
<style>
.full {
  background: url('<?php echo $img_path;?>') no-repeat center center fixed; 
}
</style>
<body>
    <!-- Page Content -->
    <div class="container">
        <div class="row" style="padding:60px 50px 0;">
            <div class="col-md-6 col-sm-12">
                <div class="logo">
                    <img src="https://www.aupasdecourses.com/skin/frontend/rwd/landing-page/images/logo.png" alt="Au Pas De Courses <?php echo $data[$zipcode]['nom'];?>" class="large">
                    <div id="storename"><?php echo $data[$zipcode]['nom'];?></div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <h1>Nous travaillons dur pour vous livrer bientôt les meilleurs produits de votre quartier!</h1>
                <h3>Pour être prévenu de notre lancement inscrivez-vous ci-dessous </h3>
                <!-- <p>Et si vous avez de bonnes adresses dans le quartier, n'hésitez pas à nous en faire part <a href="https://www.aupasdecourses.com/#inscription">ici</a>.</p> -->
                <div id="mc_embed_signup">
                    <form action="//aupasdecourses.us10.list-manage.com/subscribe/post?u=813feff892b5d2dac949b8ad4&amp;id=7472ced010" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                        <div id="mc_embed_signup_scroll">
                            <!-- <div class="indicates-required"><span class="asterisk">*</span> indicates required</div> -->
                            <div class="mc-field-group">
                                <!-- <label for="mce-EMAIL">Votre email  <span class="asterisk">*</span></label> -->
                                <input type="email" placeholder="Votre email" name="EMAIL" class="required email form-control" id="mce-EMAIL">
                            </div>                            
                            <div class="mc-field-group">
								<textarea row="5" col="26" placeholder="Des commerçants à nous recommander?" name="COMMERCANT" class="form-control" id="mce-COMMERCANT"></textarea>
							</div>
							<div class="mc-field-group row">
								<div class="col-xs-6" id="moreinfo"><a href="https://www.aupasdecourses.com/#video-presentation" target="_blank">En savoir plus</a></div>
								<div class="col-xs-6">
									<button type="submit" name="subscribe" class="ladda-button" id="mc-embedded-subscribe" data-style="expand-right"><span class="ladda-label">Envoyer</span></button>
								</div>
							</div>
                            <div class="mc-field-group input-group" style="display:none;">
                                <strong>QUARTIER </strong>
                                <ul>
                                    <li><input type="checkbox" value="<?php echo $data[$zipcode]['valuemailchimp'];?>" name="group[8929][<?php echo $data[$zipcode]['valuemailchimp'];?>]" id="mce-group[8929]-8929-<?php echo $data[$zipcode]['idmailchimp'];?>" checked><label for="mce-group[8929]-8929-<?php echo $data[$zipcode]['idmailchimp'];?>"><?php echo $data[$zipcode]['nom'];?></label></li>
                                </ul>
                            </div>
                            <div id="mce-responses" style="font-weight:500;">
                                <p class="response" id="mce-error-response" style="display:none"></p>
                                <p class="response" id="mce-success-response" style="display:none"></p>
                            </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                            <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_813feff892b5d2dac949b8ad4_7472ced010" tabindex="-1" value=""></div>
                        </div>
                    </form>
                </div>
             </div>
            </div>
        </div>
    </div>
    <!-- /.container -->
    <div id="footer" class="container">
        <div class="row">
            <div class="col-md-4 return">
				<a href="https://www.aupasdecourses.com"><?= "<-- changer d'addresse" ?></a>
            </div>
            <div class="col-md-4 col-sm-12">
                <ul class="social-icons">
                    <li><a href="https://www.facebook.com/aupasdecourses"><i class="social_facebook_square"></i></a></li>
                    <li><a href="https://twitter.com/aupasdecourses"><i class="social_twitter_square"></i></a></li>
                    <li><a href="https://plus.google.com/114890137995721785465/about"><i class="social_googleplus_square"></i></a></li>
                    <li><a href="https://instagram.com/aupasdecourses/"><i class="social_instagram_square"></i></a></li>
                </ul>
            </div>
            <div class="col-md-4 credits">
                <a href="https://flic.kr/p/5v7rs8" class="credit_link" target="_blank">Crédit photo: <?php echo $data[$zipcode]['credits'];?></a>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/spin.min.js"></script>
	<script src="js/ladda.min.js"></script>
    <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script>
    <script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email'; /*
             * Translated default messages for the $ validation plugin.
             * Locale: FR
             */
            $.extend($.validator.messages, {
                    required: "Ce champ est requis.",
                    remote: "Veuillez remplir ce champ pour continuer.",
                    email: "Veuillez entrer une adresse email valide.",
                    url: "Veuillez entrer une URL valide.",
                    date: "Veuillez entrer une date valide.",
                    dateISO: "Veuillez entrer une date valide (ISO).",
                    number: "Veuillez entrer un nombre valide.",
                    digits: "Veuillez entrer (seulement) une valeur numérique.",
                    creditcard: "Veuillez entrer un numéro de carte de crédit valide.",
                    equalTo: "Veuillez entrer une nouvelle fois la même valeur.",
                    accept: "Veuillez entrer une valeur avec une extension valide.",
                    maxlength: $.validator.format("Veuillez ne pas entrer plus de {0} caractères."),
                    minlength: $.validator.format("Veuillez entrer au moins {0} caractères."),
                    rangelength: $.validator.format("Veuillez entrer entre {0} et {1} caractères."),
                    range: $.validator.format("Veuillez entrer une valeur entre {0} et {1}."),
                    max: $.validator.format("Veuillez entrer une valeur inférieure ou égale à {0}."),
                    min: $.validator.format("Veuillez entrer une valeur supérieure ou égale à {0}.")
            });}(jQuery));var $mcj = jQuery.noConflict(true);
    </script>
    <script>
    	Ladda.bind( 'button[type=submit]', { timeout: 1500 } );
    </script>

</body>

</html>
