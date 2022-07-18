<?php 
function regenerate_landing($id_website) {
	global $conn;
	$stmt = $conn->prepare("SELECT a.*, b.`name`, b.`url_directory`, b.`activity`, b.`seo_description`, b.`seo_keywords`, b.`id_account` FROM `websites_landing` a INNER JOIN `websites` b ON a.`id_website` = b.`id_website`  WHERE a.`id_website` = :fl");
	$stmt->bindParam(':fl', $id_website, PDO::PARAM_INT);
	$stmt->execute();
	$total = $stmt->rowCount();
	$result = $stmt->fetchObject();

	$backg = "";
	if ($result->background != NULL) {
		$supported_format = array('gif','jpg','jpeg','png');
		$ext = strtolower(pathinfo($result->background, PATHINFO_EXTENSION));
		if (in_array($ext, $supported_format)) {
			$backg = '../../accounts/uploads/'.$result->background;
		} else {
			$backg = '../assets/images/backgrounds/'.$result->background.'.jpg';
		}
	}

	if (file_exists('../../landing-page/'.$result->url_directory)) {
		$newcontent = '<!DOCTYPE html><html lang=en><head><meta charset=utf-8><meta http-equiv=X-UA-Compatible content="IE=edge"/><meta name=author content="Private Chat"/><meta name=description content="'.$result->seo_description.'"/><meta name=keywords content="'.$result->seo_keywords.'"><meta name=viewport content="width=device-width, initial-scale=1.0"/><title>'.$result->name.' - Private Chat</title><link rel="shortcut icon" href=../uploads/favicon.ico type=image/x-icon><link rel=icon href=../uploads/favicon.ico type=image/x-icon><link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900" rel=stylesheet><link href="https://fonts.googleapis.com/css?family=Noto+Sans+TC:300,400,500,700,900" rel=stylesheet><link href=../assets/css/bootstrap.min.css rel=stylesheet><link href=../assets/css/fontawesome.min.css rel=stylesheet><link href=../assets/css/flaticon.css rel=stylesheet><link href=../assets/css/magnific-popup.css rel=stylesheet><link href=../assets/css/owl.carousel.min.css rel=stylesheet><link href=../assets/css/owl.theme.default.min.css rel=stylesheet><link href=../assets/css/slick.css rel=stylesheet><link href=../assets/css/slick-theme.css rel=stylesheet><link href=../assets/css/animate.css rel=stylesheet><link href=../assets/css/spinner.css rel=stylesheet><link href=../assets/css/style.css rel=stylesheet><link href=../assets/css/responsive.css rel=stylesheet><link rel="stylesheet" type="text/css" href="../assets/int-phone-number/css/intlTelInput.min.css"></head><body><div id=loader-wrapper><div id=loader><div class=cssload-spinner></div></div></div><div id=page class=page><header id=header class=header><nav class="navbar fixed-top navbar-expand-md hover-menu navbar-dark bg-tra white-scroll"><div class=container><a href=#hero-12 class="navbar-brand logo-black"><img src=https://herastro.com/wp-content/themes/apuslisting/assets/images/logo_herastro.png width=120 height=30 alt=header-logo></a><a href=#hero-12 class="navbar-brand logo-white"><img src=https://herastro.com/wp-content/themes/apuslisting/assets/images/logo_herastro.png width=120 height=30 alt=header-logo></a><button class=navbar-toggler type=button data-toggle=collapse data-target=#navbarContent aria-controls=navbarContent aria-expanded=false aria-label="Toggle navigation"><span class=navbar-bar-icon><i class="fas fa-bars"></i></span></button><div id=navbarContent class="collapse navbar-collapse"><ul class="navbar-nav ml-auto">';
		if ($result->section_features == 1) {
			$newcontent .= '<li class="nav-item nl-simple"><a class=nav-link href=#features-2>Features</a></li>';
		}
		$newcontent .= '<li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href=# id=DropdownMenu data-toggle=dropdown aria-haspopup=true aria-expanded=false>About</a><ul class=dropdown-menu aria-labelledby=DropdownMenu><li><a class=dropdown-item href=#info-5>About '.$result->name.'</a></li>';
		if ($result->section_statistics == 1) {
			$newcontent .= '<li><a class=dropdown-item href=#statistic-1>Why Choose Us</a></li>';
		}
		if ($result->section_qualities == 1) {
			$newcontent .= '<li><a class=dropdown-item href=#info-10>Best Solutions</a></li>';
		}
		if ($result->section_testimonials == 1) {
			$newcontent .= '<li><a class=dropdown-item href=#reviews-1>Testimonials</a></li>';
		}
		$newcontent .= '</ul></li>';
		if ($result->section_consultants == 1) {
			$newcontent .= '<li class="nav-item nl-simple"><a class=nav-link href=#more-apps>Our consultants</a></li>';
		}
		if ($result->section_process == 1) {
			$newcontent .= '<li class="nav-item nl-simple"><a class=nav-link href=#process-2>How It Works</a></li>';
		}
		if ($result->section_pricing == 1) {
			$newcontent .= '<li class="nav-item nl-simple"><a class=nav-link href=#pricing-2>Pricing</a></li>';
		}
		if ($result->section_faqs == 1) {
			$newcontent .= '<li class="nav-item nl-simple"><a class=nav-link href=#faqs-1>FAQs</a></li>';
		}
		$newcontent .= '</ul></div></div></nav></header><section id=hero-12 class="bg-fixed hero-section division" style="';
		if ($backg != NULL) {
			$newcontent .= 'background-image: url('.$backg.')';
		}
		$newcontent .= '"><div class="backgroundcolor"></div><div class=container><div class="row d-flex align-items-center"><div class="col-md-7 col-xl-6"><div class="hero-txt white-color mb-40 wow fadeInUp" data-wow-delay=0.3s><h2 class=h2-xs>'.$result->title.'</h2><p class=p-lg>'.$result->subtitle.'</p></div></div><div class="col-md-5 col-xl-5 offset-xl-1"><div class="hero-form text-center mb-40 wow fadeInLeft" data-wow-duration="1.5s" data-wow-delay="0.3s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0.3s; animation-name: fadeInLeft;">';
		if ($result->section_form == 1) {
			$newcontent .= '<form name="registerForm" class="row register-form" novalidate="novalidate"><div class="col-md-12"><h4 class="h4-sm">Get started for FREE!</h4><p>Fill all fields so we can get some info about you. We’ll never send you spam</p></div><div id="input-name" class="col-md-12"><input class="form-control name" type="text" name="name" placeholder="Your Name"></div><div id="input-mail" class="col-md-12"><input class="form-control mail" type="text" name="mail" placeholder="Your Email"></div><div id="input-phone" class="col-md-12"><input id="phone" class="form-control phone phonenumber" type="text" name="phone" placeholder="Your Phone Number"><input id="cnt" class="cnt" type="hidden" name="cnt"><span id="error-msg" data-type="error-msg" class="hide text-danger">Invalid number</span></div><div class="col-md-12"><button type="submit" id="signup" class="btn btn-md btn-blue black-hover submit">Sign Up Now</button><p class="p-sm">By signing up, you accept our <a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a></p></div><div class="register-form-msg text-center"><div class="sending-msg"><span class="loading"></span></div></div></form>';
		}
		$newcontent .= '</div></div></div></div><div class="bg-fixed bottom-wave"></div></section>';
		if ($result->section_features == 1) {
			$newcontent .= '<section id=features-2 class="wide-60 features-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>'.$result->section_features_title.'</h3><p class=p-md>'.$result->section_features_desc.'</p></div></div><div class=row>';
			$nb_features = 0;
			for ($i=1; $i < 7; $i++) {
				if ($result->{'section_features_block'.$i.'_name'} != NULL) {
					$nb_features++;
				}
			}
			$feature_class="col-lg-2";
			switch ($nb_features) {
				case '1':$feature_class="col-lg-12";break;
				case '2':$feature_class="col-lg-6";break;
				case '3':$feature_class="col-lg-4";break;
				case '4':$feature_class="col-lg-6";break;
				case '5':$feature_class="col-lg-4";break;
				case '6':$feature_class="col-lg-4";break;
			}
			if ($result->section_features_block1_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.3s><span class=flaticon-004-tap></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block1_name.'</h5><p>'.$result->section_features_block1_detail.'</p></div></div></div>';
			} 
			if ($result->section_features_block2_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.5s><span class=flaticon-090-settings-1></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block2_name.'</h5><p>'.$result->section_features_block2_detail.'</p></div></div></div>';
			} 
			if ($result->section_features_block3_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.7s><span class=flaticon-061-fingerprint></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block3_name.'</h5><p>'.$result->section_features_block3_detail.'</p></div></div></div>';
			} 
			if ($result->section_features_block4_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.3s><span class=flaticon-014-calendar></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block4_name.'</h5><p>'.$result->section_features_block4_detail.'</p></div></div></div>';
			} 
			if ($result->section_features_block5_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.5s><span class=flaticon-044-wall-clock-1></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block5_name.'</h5><p>'.$result->section_features_block5_detail.'</p></div></div></div>';
			} 
			if ($result->section_features_block6_name != NULL) {
				$newcontent .= '<div class="col-md-6 '.$feature_class.'"><div class="fbox-2 icon-sm wow fadeInUp" data-wow-delay=0.7s><span class=flaticon-070-worldwide></span><div class=fbox-2-txt><h5 class=h5-sm>'.$result->section_features_block6_name.'</h5><p>'.$result->section_features_block6_detail.'</p></div></div></div>';
			}
			$newcontent .= '</div></div></section>';
		}
		if ($result->section_consultants == 1) {
			$s4 = $conn->prepare("SELECT * FROM `consultants` WHERE `websites` IN (:ID)");
			$s4->bindParam(':ID', $id_website, PDO::PARAM_INT);
			$s4->execute();
			$consultants_rows = $s4->rowCount();
			$consultants = $s4->fetchAll();

			$cons_class = "";
			switch ($consultants_rows) {
				case '1':$cons_class="col-md-4";break;
				case '2':$cons_class="col-md-2";break;
			}
			$newcontent .= '<section id=more-apps class="wide-60 moreapps-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>'.$result->section_consultants_title.'</h3><p class=p-md>'.$result->section_consultants_desc.'</p></div></div><div class=row><div class="col-xl-10 offset-xl-1"><div class=row>';
			if ($consultants_rows > 0) {
				$newcontent .= '<div class='.$cons_class.'></div>';
				foreach ($consultants as $cons) {
					$image = ($cons['photo'] != NULL) ? "../../accounts/uploads/consultants/".$cons['photo'] : "../../assets/images/consultant_men.jpg";
					$rating = intval($cons["contact_rating"]);
					$expertises = explode(",", $cons["contact_expertise"]);
					$newcontent .= '<div class="col-md-4"><div class="app-box text-center mb-40 wow fadeInUp" data-wow-delay=0.3s><div class=m-img><img class=img-fluid src="'.$image.'" width=150 height=150 alt=app-logo><div class=app-rating>';
					for ($i=1; $i < $rating; $i++) { 
						$newcontent .= '<i class="fas fa-star"></i>';
					}
					$newcontent .= '</div></div><h5 class=h5-xl>'.$cons['pseudo'].'</h5><span class=app-cat>'.$cons['contact_title'].'</span><p>'.$cons['contact_desc'].'</p><div class=app-links>';
					foreach ($expertises as $exp) {
						$newcontent .= '<a href=#><i class="fas fa-tag"></i> <span>'.$exp.'</span></a>';
					}
					$newcontent .= '</div></div></div>';
				}
			}
			$newcontent .= '</div></div></div></div></section>';
		}
		if ($result->section_payments == 1) {
			$newcontent .= '<section id="info-5" class="info-5-row pb-80 info-section"><div class="pt-100 bg-inner bg-lightgrey division"><div class="container"><div class="row"><div class="col-md-6"><div class="info-5-img pl-45 wow fadeInUp" data-wow-duration="1.5s" data-wow-delay="0.8s" style="visibility: visible; animation-duration: 1.5s; animation-delay: 0.8s; animation-name: fadeInUp;"><img class="img-fluid" src="../assets/images/image-06.png" alt="info-image"></div></div><div class="col-md-6"><div class="txt-block pc-45 mb-40 wow fadeInUp" data-wow-delay="0.3s" style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;"><span class="section-id id-color">Totally Optimized</span><h3 class="h3-lg">Your payments are secure, every time</h3><ul class="txt-list"><li>Vitae auctor integer congue magna at pretium</li><li>Integer congue magna at pretium purus pretium ligula rutrum luctus risus eros dolor auctor ipsum blandit purus </li><li>Egestas magna ipsum vitae purus efficitur ipsum cubilia and laoreet pretium ligula rutrum luctus congue impedit</li><li>Pretium purus pretium ligula rutrum luctus risus eros dolor auctor ipsum donec enim ipsum porta justo</li></ul></div></div></div></div></div></section>';
		}
		if ($result->section_qualities == 1) {
			$newcontent .= '<section id=info-1 class="info-2-row wide-60 info-section division"><div class=container><div class="row d-flex align-items-center"><div class=col-md-6><div class="txt-block pc-45 mb-40 wow fadeInUp" data-wow-delay=0.3s><span class="section-id id-color">Real-Time Connections</span><h3 class=h3-lg>'.$result->section_qualities_title.'</h3><ul class="txt-list mb-35">';
			if ($result->section_qualities_line1) {
				$newcontent .= '<li>'.$result->section_qualities_line1.'</li>';
			}
			if ($result->section_qualities_line2) {
				$newcontent .= '<li>'.$result->section_qualities_line2.'</li>';
			}
			if ($result->section_qualities_line3) {
				$newcontent .= '<li>'.$result->section_qualities_line3.'</li>';
			}
			if ($result->section_qualities_line4) {
				$newcontent .= '<li>'.$result->section_qualities_line4.'</li>';
			}
			$newcontent .= '</ul><a href="'.$result->section_qualities_url.'" class="video-popup2 btn btn-tra-grey black-hover">See the '.$result->name.' in action</a></div></div><div class=col-md-6><div class="img-block pl-45 mb-40 wow fadeInUp" data-wow-duration=1.5s data-wow-delay=0.8s><img class=img-fluid src=../assets/images/image-02.png alt=info-image></div></div></div></div></section>';
		}
		if ($result->section_video == 1) {
			$newcontent .= '<section id=info-8 class="info-8-row bg-map bg-darkviolet bg-fixed pt-100 info-section division"><div class="container white-color"><div class=row><div class="col-lg-10 offset-lg-1"><div class="txt-block text-center mb-50 wow fadeInUp" data-wow-delay=0.3s><h3 class=h3-lg>'.$result->section_video_title.'</h3><p class=p-md>'.$result->section_video_desc.'</p><div class="video-block mt-40"><a class="video-popup2" href="'.$result->section_video_url.'"><div class="video-btn play-icon-tra"><div class="video-block-wrapper"><svg class="svg-inline--fa fa-play fa-w-14" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="play" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg></div></div></a></div></div></div></div></div></section>';
		}
		if ($result->section_process == 1) {
			$newcontent .= '<section id=process-2 class="wide-60 process-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>Register, Connect, Enjoy '.$result->name.'</h3><p class=p-md>Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primis libero tempus, blandit posuere ligula varius magna congue cursus porta </p></div></div><div class=row><ul class=processbar><li id=step-1 class=col-md-4><div class="pbox-2 text-center"><div class=step-icon><img class=img-60 src=../assets/images/icons/add-user.png alt=process-icon /></div><h5 class=h5-sm>Create Account</h5><p>Nemo ipsam egestas volute fugit dolores quaerat sodales </p></div></li><li id=step-2 class=col-md-4><div class="pbox-2 text-center"><div class=step-icon><img class=img-60 src=../assets/images/icons/settings-1.png alt=process-icon /></div><h5 class=h5-sm>Configure Profile</h5><p>Nemo ipsam egestas volute fugit dolores quaerat sodales</p></div></li><li id=step-3 class=col-md-4><div class="pbox-2 text-center"><div class=step-icon><img class=img-60 src=../assets/images/icons/target.png alt=process-icon /></div><h5 class=h5-sm>Get Connection</h5><p>Nemo ipsam egestas volute fugit dolores quaerat sodales</p></div></li></ul></div></div></section>';
		}
		if ($result->section_statistics == 1) {
			$nb_stats = 0;
			for ($i=1; $i < 4; $i++) { 
				if ($result->{'section_statistics_data'.$i.'_name'} != NULL) {
					$nb_stats++;
				}
			}
			$stats_class="col-sm-4";
			switch ($nb_stats) {
				case '1':$stats_class="col-sm-12";break;
				case '2':$stats_class="col-sm-6";break;
				case '3':$stats_class="col-sm-4";break;
			}
			$newcontent .= '<div id=statistic-1 class="bg-fixed bg-graph wide-60 statistic-section division"><div class="container white-color"><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>'.$result->section_statistics_title.'</h3><p class=p-md>'.$result->section_statistics_desc.'</p></div></div><div class=row><div class="col-md-10 col-lg-8 offset-md-1 offset-lg-2"><div class=row>';
			if ($result->section_statistics_data1_name != NULL) {
				$newcontent .= '<div class='.$stats_class.'><div class="statistic-block wow fadeInUp" data-wow-duration=1s data-wow-delay=0.3s><h5 class=statistic-number><span class=count-element>'.$result->section_statistics_data1_number.'</span>%</h5><p class=txt-400>'.$result->section_statistics_data1_name.'</p></div></div>';
			} if ($result->section_statistics_data2_name != NULL) {
				$newcontent .= '<div class='.$stats_class.'><div class="statistic-block wow fadeInUp" data-wow-duration=1s data-wow-delay=0.5s><h5 class=statistic-number><span class=count-element>'.$result->section_statistics_data2_number.'</span>%</h5><p class=txt-400>'.$result->section_statistics_data2_name.'</p></div></div>';
			} if ($result->section_statistics_data3_name != NULL) {
				$newcontent .= '<div class='.$stats_class.'><div class="statistic-block wow fadeInUp" data-wow-duration=1s data-wow-delay=0.7s><h5 class=statistic-number><span class=count-element>'.$result->section_statistics_data3_number.'</span>%</h5><p class=txt-400>'.$result->section_statistics_data3_name.'</p></div></div>';
			}
			$newcontent .= '</div></div></div></div></div>';
		}
		if ($result->section_pricing == 1) {
			$s1 = $conn->prepare("SELECT * FROM `pricing` WHERE `id_website` = :ID");
			$s1->bindParam(':ID', $id_website, PDO::PARAM_INT);
			$s1->execute();
			$pricings_rows = $s1->rowCount();
			$pricings = $s1->fetchAll();

			$pricing_class = "col-md-4";
			switch ($pricings_rows) {
				case '1':$pricing_class="col-md-12";break;
				case '2':$pricing_class="col-md-6";break;
				case '3':$pricing_class="col-md-4";break;
				case '4':$pricing_class="col-md-3";break;
			}
			$newcontent .= '<section id=pricing-2 class="bg-lightgrey wide-100 pricing-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>'.$result->section_pricing_title.'</h3><p class=p-md>'.$result->section_pricing_desc.'</p></div></div><div class="row pricing-row">';
			if ($pricings_rows > 0) {
				foreach ($pricings as $pri) {
					if ($pri['status'] == 1) {
						$newcontent .= '<div class="'.$pricing_class.'"><div class="pricing-table highlight"><div class=price-icon><img class=img-130 src=../assets/images/icons/airship-grey.png alt=price-icon /></div><div class=pricing-plan><h5 class=h5-md>'.$pri["title"].'</h5><sup>'.Currency($pri["currency"]).'</sup><span class=price>'.$pri["price"].'</span><sup class=validity>/mo</sup></div><ul class=features><li><strong>'.$pri["messages"].'</strong> Messages</li></ul><a href=# class="btn btn-green black-hover">Get Started</a></div></div>';
					} else {
						$newcontent .= '<div class="'.$pricing_class.'"><div class=pricing-table><div class=price-icon><img class=img-130 src=../assets/images/icons/scooter-grey.png alt=price-icon /></div><div class=pricing-plan><h5 class=h5-md>'.$pri["title"].'</h5><sup>'.Currency($pri["currency"]).'</sup><span class=price>'.$pri["price"].'</span><sup class=validity>/mo</sup></div><ul class=features><li><strong>'.$pri["messages"].'</strong> Messages</li></ul><a href=# class="btn btn-tra-grey black-hover">Get Started</a></div></div>';
					}

				}
			}
			$newcontent .= '</div><div class=row><div class="col-lg-10 col-xl-8 offset-lg-1 offset-xl-2"><div class="pricing-notice text-center"><p class=p-md><span>Note!</span> Prices may vary from location to location due to local taxation laws and conversion rates from Euro.</p></div></div></div></div></section>';
		}
		if ($result->section_mobileapp == 1) {
			$newcontent .= '<section id=download-1 class="bg-scroll bg-image download-section division"><div class="container white-color"><div class=row><div class="col-lg-10 offset-lg-1"><div class="download-txt text-center wow fadeInUp" data-wow-delay=0.3s><h3 class=h3-xs>'.$result->section_mobileapp_title.'</h3><p class=p-md>'.$result->section_mobileapp_desc.'</p><div class=stores-badge>';
			if ($result->appstore != NULL) {
				$newcontent .= '<a href="'.$result->appstore.'" class=store><img class=appstore-white src=../assets/images/store_badges/appstore-tra-white.png alt=appstore-logo></a>';
			} if ($result->googleplay != NULL) {
				$newcontent .= '<a href="'.$result->googleplay.'" class=store><img class=googleplay-white src=../assets/images/store_badges/googleplay-tra-white.png alt=googleplay-logo></a>';
			}
			$newcontent .= '<span class=os-version>* Available on iPhone, iPad and all Android devices from 5.5</span></div></div></div></div></div></section>';
		}
		if ($result->section_testimonials == 1) {
			$s0 = $conn->prepare("SELECT * FROM `testimonials` WHERE `id_website` = :ID");
			$s0->bindParam(':ID', $id_website, PDO::PARAM_INT);
			$s0->execute();
			$testimonials_rows = $s0->rowCount();
			$testimonials = $s0->fetchAll();

			$newcontent .= '<section id=reviews-1 class="wide-100 bg-lightgrey reviews-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>'.$result->section_testimonials_title.'</h3><p class=p-md>'.$result->section_testimonials_desc.'</p></div></div><div class=reviews-carousel><div class="center slider">';
			if ($testimonials_rows > 0) {
				foreach ($testimonials as $tes) {
					$rating = intval($tes["rating"]);
					$newcontent .= '<div class=review-1><div class=review-1-txt><h5 class=h5-md>'.$tes["title"].'</h5><p>'.$tes["content"].'</p></div><div class="testimonial-avatar text-center"><img src="../../accounts/uploads/testimonials/'.$tes["photo"].'" alt=review-author-avatar><p>'.$tes["username"].'</p></div><div class=app-rating>';
					for ($i=1; $i < $rating; $i++) { 
						$newcontent .= '<i class="fas fa-star"></i>';
					}
					$newcontent .= '</div></div>';
				}
			}
			$newcontent .= '</div></div></div></section>';
		}
		if ($result->section_faqs == 1) {
			$newcontent .= '<section id=faqs-1 class="wide-100 faqs-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>Have Questions? Look Here.</h3><p class=p-md>Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primislibero tempus, blandit posuere ligula varius magna congue cursus porta </p></div></div><div class=row><div class="col-xl-10 offset-xl-1"><div id=accordion role=tablist><div class="card wow fadeInUp" data-wow-delay=0.3s><div class=card-header role=tab id=headingOne><h5 class=h5-sm><a data-toggle=collapse href=#collapseOne role=button aria-expanded=true aria-controls=collapseOne>Do you have a free trial?</a></h5></div><div id=collapseOne class="collapse show" role=tabpanel aria-labelledby=headingOne data-parent=#accordion><div class=card-body><p>Aliqum mullam blandit tempor sapien gravida donec ipsum, at porta justo. Velna vitae auctor eros congue magna nihil impedit ligula risus. Mauris donec ociis et magnis sapien etiam sapien sem sagittis congue tempor gravida donec enim ipsum porta justo integer at odio velna congue integer vitae auctor eros dolor luctus odio placerat massa magna </p><p>Nullam rutrum eget nunc vaius etiam mollis risus congue aliquam etiam sapien egestas, congue gestas posuere cubilia congue ipsum mauris lectus laoreet gestas neque vitae auctor eros dolor luctus odio placerat magna cursus </p></div></div></div><div class="card wow fadeInUp" data-wow-delay=0.5s><div class=card-header role=tab id=headingTwo><h5 class=h5-sm><a class=collapsed data-toggle=collapse href=#collapseTwo role=button aria-expanded=false aria-controls=collapseTwo>How can I update or cancel my personal information?</a></h5></div><div id=collapseTwo class=collapse role=tabpanel aria-labelledby=headingTwo data-parent=#accordion><div class=card-body><p>Maecenas gravida porttitor nunc, quis vehicula magna luctus tempor. Quisque vel laoreet turpis. Urna augue, viverra a augue eget, dictum tempor diam. Sed pulvinar consectetur nibh, vel imperdiet dui varius viverra. Pellentesque ac massa lorem. Fusce eu cursus est. Fusce non nulla vitae massa placerat vulputate vel a purus </p></div></div></div><div class="card wow fadeInUp" data-wow-delay=0.7s><div class=card-header role=tab id=headingThree><h5 class=h5-sm><a class=collapsed data-toggle=collapse href=#collapseThree role=button aria-expanded=false aria-controls=collapseThree>How do I download videos from online viewing?</a></h5></div><div id=collapseThree class=collapse role=tabpanel aria-labelledby=headingThree data-parent=#accordion><div class=card-body><p>Nullam rutrum eget nunc varius etiam mollis risus congue aliquam etiam sapien egestas, congue gestas posuere cubilia congue ipsum mauris lectus laoreet gestas neque vitae auctor eros dolor luctus odio placerat magna cursus</p><ul class=txt-list><li>Vitae auctor integer congue magna at pretium purus pretium ligula rutrum luctus risus eros dolor auctor</li><li>Sagittis congue augue egestas volutpat egestas magna suscipit egestas magna ipsum vitae purus efficitur ipsum primis in cubilia laoreet augue egestas luctus donec diam.Tempor sapien gravida donec enim ipsum blandit magna at purus pretium ligula rutrum luctus gravida donec porta justo integer</li><li>Justo odio integer a velna lectus aenean magna and mauris lectus pretium ligula rutrum luctus risus ac risus auctor gravida donec congue tempor gravida donec enim ipsum porta justo integer</li></ul></div></div></div><div class="card wow fadeInUp" data-wow-delay=0.9s><div class=card-header role=tab id=headingFour><h5 class=h5-sm><a class=collapsed data-toggle=collapse href=#collapseFour role=button aria-expanded=false aria-controls=collapseFour>Why do you require Bluetooth to be on?</a></h5></div><div id=collapseFour class=collapse role=tabpanel aria-labelledby=headingFour data-parent=#accordion><div class=card-body><p>Aliqum mullam blandit tempor sapien gravida donec ipsum, at porta justo. Velna vitae auctor eros congue magna nihil impedit ligula risus. Mauris donec ociis et magnis sapien etiam sapien sem sagittis congue tempor gravida donec enim ipsum porta justo integer at odio velna congue integer vitae auctor eros dolor luctus odio placerat</p></div></div></div><div class="card wow fadeInUp" data-wow-delay=1.1s><div class=card-header role=tab id=headingFive><h5 class=h5-sm><a class=collapsed data-toggle=collapse href=#collapseFive role=button aria-expanded=false aria-controls=collapseFive>Will there be a NextApp Android app?</a></h5></div><div id=collapseFive class=collapse role=tabpanel aria-labelledby=headingFive data-parent=#accordion><div class=card-body><p>Curabitur ac dapibus libero. Quisque eu tristique neque. Phasellus blandit tristique justo ut aliquam. Aliquam vitae molestie nunc. Quisque sapien justo, aliquet non molestie sed purus, venenatis nec. Aliquam eget lacinia elit. Vestibulum tincidunt tincidunt massa, et porttitor</p><p>Nullam non scelerisque lectus. In at mauris vel nisl convallis porta at vitae dui. Nam lacus ligula, vulputate molestie bibendum quis, aliquet elementum massa. Vestibulum ut sagittis odio</p></div></div></div></div></div></div><div class=row><div class="col-md-12 text-center"><div class="more-questions wow fadeInUp" data-wow-delay=1.3s><h5 class=h5-xs>Still have a question? <a href=faqs.php>Ask your question here</a></h5></div></div></div></div></section>';
		}
		if ($result->section_support == 1) {
			$newcontent .= '<section id=contacts-1 class="bg-fixed bg-map bg-lightgrey wide-100 contacts-section division"><div class=container><div class=row><div class="col-lg-10 offset-lg-1 section-title"><h3 class=h3-lg>Need Help? Looking For Support?</h3><p class=p-md>Aliquam a augue suscipit, luctus neque purus ipsum neque dolor primis nlibero tempus, blandit posuere ligula varius magna congue cursus porta</p></div></div><div class=row><div class="col-md-10 col-xl-8 offset-md-1 offset-xl-2"><div class=form-holder><form name=contactform class="row contact-form"><div id=input-name class=col-lg-6><input type=text name=name class="form-control name" placeholder="Your Name*"></div><div id=input-email class=col-lg-6><input type=text name=email class="form-control email" placeholder="Email Address*"></div><div id=input-subject class="col-md-12 input-subject"><select id=inlineFormCustomSelect1 name=Subject class="custom-select subject"><option>This question is about...</option><option>Registering/Authorising</option><option>Using Application</option><option>Troubleshooting</option><option>Backup/Restore</option><option>Other</option></select></div><div id=input-message class="col-lg-12 input-message"><textarea class="form-control message" name=message rows=6 placeholder="Your Message ..."></textarea></div><div class="col-lg-12 mt-15 form-btn text-right"><button type=submit class="btn btn-green black-hover submit">Send Your Message</button></div><div class="col-lg-12 contact-form-msg"><span class=loading></span></div></form></div></div></div></div></section>';
		}
		$newcontent .= '<footer id=footer-1 class="wide-50 footer division"><div class=container><div class=row><div class=col-xl-4><div class="footer-info mb-40"><img src="';
		if (isset($result->logo)){
			$newcontent .= '../../accounts/uploads/'.$result->logo;
		} else {
			$newcontent .= '../assets/images/footer-logo.png';
		}
		$newcontent .='" width=160 height=40 alt=footer-logo><p class=mt-20>'.$result->description.'</p></div></div><div class="col-md-3 col-xl-2"><div class=footer-links><ul class="foo-links clearfix"><li><p><a href=#>How It Works?</a></p></li><li><p><a href=#>Get the App</a></p></li><li><p><a href=#>Terms of Service</a></p></li></ul></div></div><div class="col-md-3 col-xl-2"><div class=footer-links><ul class="foo-links clearfix"><li><p><a href=#>FAQs</a></p></li><li><p><a href=#>Editor Help</a></p></li><li><p><a href=#>Life Chatting</a></p></li><li><p><a href=#>Contact Us</a></p></li></ul></div></div><div class="col-md-3 col-xl-2"><div class=footer-links><ul class="foo-links clearfix"><li><p><a href="'.$result->twitter.'"><i class="fab fa-twitter"></i> Twitter</a></p></li><li><p><a href="'.$result->facebook.'"><i class="fab fa-facebook"></i> Facebook</a></p></li><li><p><a href="'.$result->instagram.'"><i class="fab fa-instagram"></i> Instagram</a></p></li><li><p><a href="'.$result->pinterest.'"><i class="fab fa-c"></i> Pinterest</a></p></li></ul></div></div><div class="col-md-3 col-xl-2"><div class="footer-stores-badge text-right mb-40">';
		if ($result->appstore != NULL) {
			$newcontent .= '<a href="'.$result->appstore.'" class=store><img class=appstore-original src=../assets/images/store_badges/appstore.png alt=appstore-logo /></a>';
		}
		if ($result->googleplay != NULL) {
			$newcontent .= '<a href="'.$result->googleplay.'" class=store><img class=googleplay-original src=../assets/images/store_badges/googleplay.png alt=googleplay-logo /></a>';
		}
		$newcontent .= '</div></div></div><div class=bottom-footer><div class=row><div class=col-md-12><div class=footer-copyright><p class=p-sm>&copy; 2019 '.$result->name.'. All Rights Reserved</p></div></div></div></div></div></footer></div><script type="text/javascript">var id_website = "'.$id_website.'";var id_account = "'.$result->id_account.'";</script><script src=../assets/js/jquery-3.3.1.min.js></script><script src=../assets/js/bootstrap.min.js></script><script src=../assets/js/fontawesome.min.js></script><script src=../assets/js/modernizr.custom.js></script><script src=../assets/js/jquery.easing.js></script><script src=../assets/js/jquery.appear.js></script><script src=../assets/js/jquery.stellar.min.js></script><script src=../assets/js/jquery.scrollto.js></script><script src=../assets/js/imagesloaded.pkgd.min.js></script><script src=../assets/js/isotope.pkgd.min.js></script><script src=../assets/js/slick.min.js></script><script src=../assets/js/owl.carousel.min.js></script><script src=../assets/js/jquery.magnific-popup.min.js></script><script src=../assets/js/contact-form.js></script><script src=../assets/js/quick-form.js></script><script src=../assets/js/comment-form.js></script><script src=../assets/js/jquery.validate.min.js></script><script src=../assets/js/jquery.ajaxchimp.min.js></script><script src=../assets/js/wow.js></script><script src=../assets/js/custom.min.js></script><script src="../assets/int-phone-number/js/intlTelInput-jquery.js"></script><script>new WOW().init();var errorMsg = $("#error-msg");var errorMap = [ "Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];var iti = $("#phone").intlTelInput({nationalMode: true,autoPlaceholder: "off",initialCountry: "auto",geoIpLookup: function(callback) {	$.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {var countryCode = (resp && resp.country) ? resp.country : "";callback(countryCode);	});},utilsScript: "../assets/int-phone-number/js/utils.js"});var reset = function() {$("#phone").removeClass("error");errorMsg.html("");errorMsg.addClass("hide");};$("#phone").on("blur", function() {reset();if ($("#phone").val().trim()) {if ($("#phone").intlTelInput("isValidNumber")) {$("#phone").val($("#phone").intlTelInput("getNumber"));$("#cnt").val($("#phone").intlTelInput("getSelectedCountryData").iso2);} else {$("#phone").addClass("error");var errorCode = $("#phone").intlTelInput("getValidationError");errorMsg.html(errorMap[errorCode]);errorMsg.removeClass("hide");}}});$("#phone").on("change", reset);$("#phone").on("keyup", reset);</script><!-- [if lt IE 9]><script src=../assets/js/html5shiv.js type=text/javascript></script><script src=../assets/js/respond.min.js type=text/javascript></script><![endif] --></body></html>';
		$handle = fopen('../../landing-page/'.$result->url_directory.'/index.html','w+'); fwrite($handle,$newcontent); fclose($handle);
		return true;
	}
}
?>