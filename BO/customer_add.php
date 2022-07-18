<?php 
$page_name = "Customer";
include('header.php'); ?>
                <div class="row">
                    <?php 
                    if (isset($_POST['add'])) {
                        $business_name = (isset($_POST['business_name']) && $_POST['business_name'] != '') ? htmlspecialchars($_POST['business_name']) : NULL;
                        $registration = (isset($_POST['registration']) && $_POST['registration'] != '') ? htmlspecialchars($_POST['registration']) : NULL;
                        $taxid = (isset($_POST['taxid']) && $_POST['taxid'] != '') ? htmlspecialchars($_POST['taxid']) : NULL;
                        $address = (isset($_POST['address']) && $_POST['address'] != '') ? htmlspecialchars($_POST['address']) : NULL;
                        $pcode = (isset($_POST['pcode']) && $_POST['pcode'] != '') ? htmlspecialchars($_POST['pcode']) : NULL;
                        $city = (isset($_POST['city']) && $_POST['city'] != '') ? htmlspecialchars($_POST['city']) : NULL;
                        $country = (isset($_POST['country']) && $_POST['country'] != '') ? $_POST['country'] : NULL;
                        $phone = (isset($_POST['phone']) && $_POST['phone'] != '') ? htmlspecialchars($_POST['phone']) : NULL;
                        $emailc = (isset($_POST['emailc']) && $_POST['emailc'] != '') ? htmlspecialchars($_POST['emailc']) : NULL;
                        $website = (isset($_POST['website']) && $_POST['website'] != '') ? htmlspecialchars($_POST['website']) : NULL;
                        $datec = date('Y-m-d', strtotime('now'));

                        $first_name = (isset($_POST['first_name']) && $_POST['first_name'] != '') ? htmlspecialchars($_POST['first_name']) : NULL;
                        $last_name = (isset($_POST['last_name']) && $_POST['last_name'] != '') ? htmlspecialchars($_POST['last_name']) : NULL;
                        $phone2 = (isset($_POST['phone2']) && $_POST['phone2'] != '') ? htmlspecialchars($_POST['phone2']) : NULL;
                        $emailc2 = (isset($_POST['emailc2']) && $_POST['emailc2'] != '') ? htmlspecialchars($_POST['emailc2']) : NULL;
                        $gender = (isset($_POST['gender']) && $_POST['gender'] != '') ? intval($_POST['gender']) : NULL;

                        $stmt1 = $conn->prepare("INSERT INTO `accounts`(`business_name`, `registration`, `taxid`, `address`, `code_postal`, `city`, `country`, `phone`, `emailc`, `website`, `date_add`, `date_end`, `status`) VALUES (:bus,:reg,:tax,:adr,:cp,:ct,:cnt,:ph,:em,:web,:dt,NULL,1)");
                        $stmt1->bindParam(':bus', $business_name, PDO::PARAM_STR);
                        $stmt1->bindParam(':reg', $registration, PDO::PARAM_STR);
                        $stmt1->bindParam(':tax', $taxid, PDO::PARAM_STR);
                        $stmt1->bindParam(':adr', $address, PDO::PARAM_STR);
                        $stmt1->bindParam(':cp', $pcode, PDO::PARAM_STR);
                        $stmt1->bindParam(':ct', $city, PDO::PARAM_STR);
                        $stmt1->bindParam(':cnt', $country, PDO::PARAM_STR);
                        $stmt1->bindParam(':ph', $phone, PDO::PARAM_STR);
                        $stmt1->bindParam(':em', $emailc, PDO::PARAM_STR);
                        $stmt1->bindParam(':web', $website, PDO::PARAM_STR);
                        $stmt1->bindParam(':dt', $datec, PDO::PARAM_STR);
                        $stmt1->execute();
                        $last_id = $conn->lastInsertId();
                        $affected_rows = $stmt1->rowCount();

                        if ($affected_rows != 0) {
                            $stmt2 = $conn->prepare("INSERT INTO `managers`(`gender`, `firstname`, `lastname`, `phonep`, `emailp`, `id_account`) VALUES (:gd,:fn,:ln,:ph,:em,:ID)");
                            $stmt2->bindParam(':gd', $gender, PDO::PARAM_INT);
                            $stmt2->bindParam(':fn', $first_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':ln', $last_name, PDO::PARAM_STR);
                            $stmt2->bindParam(':ph', $phone2, PDO::PARAM_STR);
                            $stmt2->bindParam(':em', $emailc2, PDO::PARAM_STR);
                            $stmt2->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt2->execute();

                            $stmt4 = $conn->prepare("INSERT INTO `accounts_settings`(`consultant`, `customer`, `id_account`) VALUES (NULL,NULL,:ID)");
                            $stmt4->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt4->execute();

                            $username = $first_name." ".$last_name;
                            $login = (isset($emailc2) && $emailc2 != '') ? $emailc2 : $first_name."-".$last_id;
                            $pwd = strtotime('now')."-".$last_id;
                            $stmt3 = $conn->prepare("INSERT INTO `users`(`username`, `login`, `password`, `profile`, `id_profile`, `date_add`, `picture`, `active`, `last_connect`, `lastday`, `nbr_essai`) VALUES (:us,:lo,:pw,2,:ID,:dt,NULL,1,NULL,NULL,NULL)");
                            $stmt3->bindParam(':us', $username, PDO::PARAM_INT);
                            $stmt3->bindParam(':lo', $login, PDO::PARAM_STR);
                            $stmt3->bindParam(':pw', $pwd, PDO::PARAM_STR);
                            $stmt3->bindParam(':ID', $last_id, PDO::PARAM_INT);
                            $stmt3->bindParam(':dt', $datec, PDO::PARAM_STR);
                            $stmt3->execute();

                            echo "<div class='col-md-12'><div class='alert alert-success alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer account has been created successfully </div></div>";
                        } else {
                            echo "<div class='col-md-12'><div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button> The customer account has not been created </div></div>";
                        }
                        unset($_POST);
                    }
                    ?>
                    <form action="" method="POST" class="row col-md-12 pr-0">
                        <div class="col-md-7">
                            <div class="card card-body">
                                <h3 class="box-title m-b-0">Add Customer</h3>
                                <p class="text-muted m-b-30 font-13"> Add the customer informations.</p>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="acctInput1">Business name</label>
                                            <input type="text" name="business_name" class="form-control" id="acctInput1" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput2">Registration number</label>
                                            <input type="text" name="registration" class="form-control" id="acctInput2" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput22">Tax ID</label>
                                            <input type="text" name="taxid" class="form-control" id="acctInput22" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput3">Address</label>
                                            <input type="text" name="address" class="form-control" id="acctInput3" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput4">Postal code</label>
                                            <input type="text" name="pcode" class="form-control" id="acctInput4" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput44">City</label>
                                            <input type="text" name="city" class="form-control" id="acctInput44" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="country">Country</label>
                                            <select name="country" id="country" class="form-control select2 select-search country">
                                                <option></option>
                                                <option value="AF">Afghanistan</option>
                                                <option value="AX">Åland Islands</option>
                                                <option value="AL">Albania</option>
                                                <option value="DZ">Algeria</option>
                                                <option value="AS">American Samoa</option>
                                                <option value="AD">Andorra</option>
                                                <option value="AO">Angola</option>
                                                <option value="AI">Anguilla</option>
                                                <option value="AQ">Antarctica</option>
                                                <option value="AG">Antigua and Barbuda</option>
                                                <option value="AR">Argentina</option>
                                                <option value="AM">Armenia</option>
                                                <option value="AW">Aruba</option>
                                                <option value="AU">Australia</option>
                                                <option value="AT">Austria</option>
                                                <option value="AZ">Azerbaijan</option>
                                                <option value="BS">Bahamas</option>
                                                <option value="BH">Bahrain</option>
                                                <option value="BD">Bangladesh</option>
                                                <option value="BB">Barbados</option>
                                                <option value="BY">Belarus</option>
                                                <option value="BE">Belgium</option>
                                                <option value="BZ">Belize</option>
                                                <option value="BJ">Benin</option>
                                                <option value="BM">Bermuda</option>
                                                <option value="BT">Bhutan</option>
                                                <option value="BO">Bolivia, Plurinational State of</option>
                                                <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                                <option value="BA">Bosnia and Herzegovina</option>
                                                <option value="BW">Botswana</option>
                                                <option value="BV">Bouvet Island</option>
                                                <option value="BR">Brazil</option>
                                                <option value="IO">British Indian Ocean Territory</option>
                                                <option value="BN">Brunei Darussalam</option>
                                                <option value="BG">Bulgaria</option>
                                                <option value="BF">Burkina Faso</option>
                                                <option value="BI">Burundi</option>
                                                <option value="KH">Cambodia</option>
                                                <option value="CM">Cameroon</option>
                                                <option value="CA">Canada</option>
                                                <option value="CV">Cape Verde</option>
                                                <option value="KY">Cayman Islands</option>
                                                <option value="CF">Central African Republic</option>
                                                <option value="TD">Chad</option>
                                                <option value="CL">Chile</option>
                                                <option value="CN">China</option>
                                                <option value="CX">Christmas Island</option>
                                                <option value="CC">Cocos (Keeling) Islands</option>
                                                <option value="CO">Colombia</option>
                                                <option value="KM">Comoros</option>
                                                <option value="CG">Congo</option>
                                                <option value="CD">Congo, the Democratic Republic of the</option>
                                                <option value="CK">Cook Islands</option>
                                                <option value="CR">Costa Rica</option>
                                                <option value="CI">Côte d'Ivoire</option>
                                                <option value="HR">Croatia</option>
                                                <option value="CU">Cuba</option>
                                                <option value="CW">Curaçao</option>
                                                <option value="CY">Cyprus</option>
                                                <option value="CZ">Czech Republic</option>
                                                <option value="DK">Denmark</option>
                                                <option value="DJ">Djibouti</option>
                                                <option value="DM">Dominica</option>
                                                <option value="DO">Dominican Republic</option>
                                                <option value="EC">Ecuador</option>
                                                <option value="EG">Egypt</option>
                                                <option value="SV">El Salvador</option>
                                                <option value="GQ">Equatorial Guinea</option>
                                                <option value="ER">Eritrea</option>
                                                <option value="EE">Estonia</option>
                                                <option value="ET">Ethiopia</option>
                                                <option value="FK">Falkland Islands (Malvinas)</option>
                                                <option value="FO">Faroe Islands</option>
                                                <option value="FJ">Fiji</option>
                                                <option value="FI">Finland</option>
                                                <option value="FR">France</option>
                                                <option value="GF">French Guiana</option>
                                                <option value="PF">French Polynesia</option>
                                                <option value="TF">French Southern Territories</option>
                                                <option value="GA">Gabon</option>
                                                <option value="GM">Gambia</option>
                                                <option value="GE">Georgia</option>
                                                <option value="DE">Germany</option>
                                                <option value="GH">Ghana</option>
                                                <option value="GI">Gibraltar</option>
                                                <option value="GR">Greece</option>
                                                <option value="GL">Greenland</option>
                                                <option value="GD">Grenada</option>
                                                <option value="GP">Guadeloupe</option>
                                                <option value="GU">Guam</option>
                                                <option value="GT">Guatemala</option>
                                                <option value="GG">Guernsey</option>
                                                <option value="GN">Guinea</option>
                                                <option value="GW">Guinea-Bissau</option>
                                                <option value="GY">Guyana</option>
                                                <option value="HT">Haiti</option>
                                                <option value="HM">Heard Island and McDonald Islands</option>
                                                <option value="VA">Holy See (Vatican City State)</option>
                                                <option value="HN">Honduras</option>
                                                <option value="HK">Hong Kong</option>
                                                <option value="HU">Hungary</option>
                                                <option value="IS">Iceland</option>
                                                <option value="IN">India</option>
                                                <option value="ID">Indonesia</option>
                                                <option value="IR">Iran, Islamic Republic of</option>
                                                <option value="IQ">Iraq</option>
                                                <option value="IE">Ireland</option>
                                                <option value="IM">Isle of Man</option>
                                                <option value="IL">Israel</option>
                                                <option value="IT">Italy</option>
                                                <option value="JM">Jamaica</option>
                                                <option value="JP">Japan</option>
                                                <option value="JE">Jersey</option>
                                                <option value="JO">Jordan</option>
                                                <option value="KZ">Kazakhstan</option>
                                                <option value="KE">Kenya</option>
                                                <option value="KI">Kiribati</option>
                                                <option value="KP">Korea, Democratic People's Republic of</option>
                                                <option value="KR">Korea, Republic of</option>
                                                <option value="KW">Kuwait</option>
                                                <option value="KG">Kyrgyzstan</option>
                                                <option value="LA">Lao People's Democratic Republic</option>
                                                <option value="LV">Latvia</option>
                                                <option value="LB">Lebanon</option>
                                                <option value="LS">Lesotho</option>
                                                <option value="LR">Liberia</option>
                                                <option value="LY">Libya</option>
                                                <option value="LI">Liechtenstein</option>
                                                <option value="LT">Lithuania</option>
                                                <option value="LU">Luxembourg</option>
                                                <option value="MO">Macao</option>
                                                <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                                                <option value="MG">Madagascar</option>
                                                <option value="MW">Malawi</option>
                                                <option value="MY">Malaysia</option>
                                                <option value="MV">Maldives</option>
                                                <option value="ML">Mali</option>
                                                <option value="MT">Malta</option>
                                                <option value="MH">Marshall Islands</option>
                                                <option value="MQ">Martinique</option>
                                                <option value="MR">Mauritania</option>
                                                <option value="MU">Mauritius</option>
                                                <option value="YT">Mayotte</option>
                                                <option value="MX">Mexico</option>
                                                <option value="FM">Micronesia, Federated States of</option>
                                                <option value="MD">Moldova, Republic of</option>
                                                <option value="MC">Monaco</option>
                                                <option value="MN">Mongolia</option>
                                                <option value="ME">Montenegro</option>
                                                <option value="MS">Montserrat</option>
                                                <option value="MA">Morocco</option>
                                                <option value="MZ">Mozambique</option>
                                                <option value="MM">Myanmar</option>
                                                <option value="NA">Namibia</option>
                                                <option value="NR">Nauru</option>
                                                <option value="NP">Nepal</option>
                                                <option value="NL">Netherlands</option>
                                                <option value="NC">New Caledonia</option>
                                                <option value="NZ">New Zealand</option>
                                                <option value="NI">Nicaragua</option>
                                                <option value="NE">Niger</option>
                                                <option value="NG">Nigeria</option>
                                                <option value="NU">Niue</option>
                                                <option value="NF">Norfolk Island</option>
                                                <option value="MP">Northern Mariana Islands</option>
                                                <option value="NO">Norway</option>
                                                <option value="OM">Oman</option>
                                                <option value="PK">Pakistan</option>
                                                <option value="PW">Palau</option>
                                                <option value="PS">Palestinian Territory, Occupied</option>
                                                <option value="PA">Panama</option>
                                                <option value="PG">Papua New Guinea</option>
                                                <option value="PY">Paraguay</option>
                                                <option value="PE">Peru</option>
                                                <option value="PH">Philippines</option>
                                                <option value="PN">Pitcairn</option>
                                                <option value="PL">Poland</option>
                                                <option value="PT">Portugal</option>
                                                <option value="PR">Puerto Rico</option>
                                                <option value="QA">Qatar</option>
                                                <option value="RE">Réunion</option>
                                                <option value="RO">Romania</option>
                                                <option value="RU">Russian Federation</option>
                                                <option value="RW">Rwanda</option>
                                                <option value="BL">Saint Barthélemy</option>
                                                <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                                                <option value="KN">Saint Kitts and Nevis</option>
                                                <option value="LC">Saint Lucia</option>
                                                <option value="MF">Saint Martin (French part)</option>
                                                <option value="PM">Saint Pierre and Miquelon</option>
                                                <option value="VC">Saint Vincent and the Grenadines</option>
                                                <option value="WS">Samoa</option>
                                                <option value="SM">San Marino</option>
                                                <option value="ST">Sao Tome and Principe</option>
                                                <option value="SA">Saudi Arabia</option>
                                                <option value="SN">Senegal</option>
                                                <option value="RS">Serbia</option>
                                                <option value="SC">Seychelles</option>
                                                <option value="SL">Sierra Leone</option>
                                                <option value="SG">Singapore</option>
                                                <option value="SX">Sint Maarten (Dutch part)</option>
                                                <option value="SK">Slovakia</option>
                                                <option value="SI">Slovenia</option>
                                                <option value="SB">Solomon Islands</option>
                                                <option value="SO">Somalia</option>
                                                <option value="ZA">South Africa</option>
                                                <option value="GS">South Georgia and the South Sandwich Islands</option>
                                                <option value="SS">South Sudan</option>
                                                <option value="ES">Spain</option>
                                                <option value="LK">Sri Lanka</option>
                                                <option value="SD">Sudan</option>
                                                <option value="SR">Suriname</option>
                                                <option value="SJ">Svalbard and Jan Mayen</option>
                                                <option value="SZ">Swaziland</option>
                                                <option value="SE">Sweden</option>
                                                <option value="CH">Switzerland</option>
                                                <option value="SY">Syrian Arab Republic</option>
                                                <option value="TW">Taiwan, Province of China</option>
                                                <option value="TJ">Tajikistan</option>
                                                <option value="TZ">Tanzania, United Republic of</option>
                                                <option value="TH">Thailand</option>
                                                <option value="TL">Timor-Leste</option>
                                                <option value="TG">Togo</option>
                                                <option value="TK">Tokelau</option>
                                                <option value="TO">Tonga</option>
                                                <option value="TT">Trinidad and Tobago</option>
                                                <option value="TN">Tunisia</option>
                                                <option value="TR">Turkey</option>
                                                <option value="TM">Turkmenistan</option>
                                                <option value="TC">Turks and Caicos Islands</option>
                                                <option value="TV">Tuvalu</option>
                                                <option value="UG">Uganda</option>
                                                <option value="UA">Ukraine</option>
                                                <option value="AE">United Arab Emirates</option>
                                                <option value="GB">United Kingdom</option>
                                                <option value="US">United States</option>
                                                <option value="UM">United States Minor Outlying Islands</option>
                                                <option value="UY">Uruguay</option>
                                                <option value="UZ">Uzbekistan</option>
                                                <option value="VU">Vanuatu</option>
                                                <option value="VE">Venezuela, Bolivarian Republic of</option>
                                                <option value="VN">Viet Nam</option>
                                                <option value="VG">Virgin Islands, British</option>
                                                <option value="VI">Virgin Islands, U.S.</option>
                                                <option value="WF">Wallis and Futuna</option>
                                                <option value="EH">Western Sahara</option>
                                                <option value="YE">Yemen</option>
                                                <option value="ZM">Zambia</option>
                                                <option value="ZW">Zimbabwe</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone">Phone number</label>
                                            <input type="tel" name="phone" id="phone" class="form-control phonenumber" value="" >
                                            <span id="valid-msg" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                            <span id="error-msg" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="Email1">Email</label>
                                            <input type="email" name="emailc" class="form-control" id="Email1" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput7">Website</label>
                                            <input type="url" name="website" class="form-control" id="acctInput7" value="">
                                        </div>                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 pr-0">
                            <div class="card card-body">
                                <h3 class="box-title m-b-0">Manager</h3>
                                <p class="text-muted m-b-30 font-13"> Add the manager informations.</p>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">

                                        <div class="form-group">
                                            <label for="acctInput1">First name</label>
                                            <input type="text" name="first_name" class="form-control" id="acctInput1" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="acctInput2">Last name</label>
                                            <input type="text" name="last_name" class="form-control" id="acctInput2" value="">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label">Gender</label>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio1" name="gender" value="1" class="custom-control-input">
                                                <label class="custom-control-label" for="customRadio1">Male</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="customRadio2" name="gender" value="2" class="custom-control-input">
                                                <label class="custom-control-label" for="customRadio2">Female</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="phone">Personal phone number</label>
                                            <input type="tel" name="phone2" id="phone2" class="form-control phonenumber" value="" >
                                            <span id="valid-msg2" data-type="valid-msg" class="hide text-success">✓ Valid</span>
                                            <span id="error-msg2" data-type="error-msg" class="hide text-danger">Invalid number</span>
                                        </div>
                                        <div class="form-group">
                                            <label for="Email1">Personal email</label>
                                            <input type="email" name="emailc2" class="form-control" id="Email1" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pr-0">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12 text-right">
                                        <button type="submit" name="add" class="btn btn-primary waves-effect waves-light m-r-10">Add</button>
                                        <button type="submit" class="btn btn-secondary waves-effect waves-light">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
        <footer class="footer">© 2019 Private chat by Diamond services</footer>        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../assets/node_modules/popper/popper.min.js"></script>
    <script src="../assets/node_modules/bootstrap/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../assets/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="../assets/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="../assets/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="../assets/node_modules/sticky-kit-master/sticky-kit.min.js"></script>
    <script src="../assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
    <!--Custom JavaScript -->
    <script src="../assets/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <script src="../assets/js/pages/jasny-bootstrap.js"></script>
    <script src="../assets/node_modules/select2/select2.full.min.js" type="text/javascript"></script>
    <script src="../assets/int-phone-number/js/intlTelInput-jquery.js"></script>
    <script type="text/javascript">
        $(".select2").select2();
        var errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");

        // here, the index maps to the error code returned from getValidationError - see readme
        var errorMap = [ "Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];

        // initialise plugin
        var iti = $("#phone").intlTelInput({
            nationalMode: true,
            autoPlaceholder: "off",
            initialCountry: "fr",
            utilsScript: "../assets/int-phone-number/js/utils.js"
        });

        var reset = function() {
            $("#phone").removeClass("error");
            errorMsg.html("");
            errorMsg.addClass("hide");
            validMsg.addClass("hide");
        };

        // on blur: validate
        $("#phone").on('blur', function() {
            reset();
            if ($("#phone").val().trim()) {
                if ($("#phone").intlTelInput("isValidNumber")) {
                    $("#phone").val($("#phone").intlTelInput("getNumber"));
                    validMsg.removeClass("hide");
                } else {
                    $("#phone").addClass("error");
                    var errorCode = $("#phone").intlTelInput("getValidationError");
                    errorMsg.html(errorMap[errorCode]);
                    errorMsg.removeClass("hide");
                }
            }
        });

        // on keyup / change flag: reset
        $("#phone").on('change', reset);
        $("#phone").on('keyup', reset);
        // listen to the address dropdown for changes

        var errorMsg2 = $("#error-msg2"),
        validMsg2 = $("#valid-msg2");

        // here, the index maps to the error code returned from getValidationError - see readme
        var errorMap2 = [ "Invalid number.", "Invalid country code.", "Too short.", "Too long.", "Invalid number."];

        // initialise plugin
        var iti = $("#phone2").intlTelInput({
            nationalMode: true,
            autoPlaceholder: "off",
            initialCountry: "fr",
            utilsScript: "../assets/int-phone-number/js/utils.js"
        });

        var reset2 = function() {
            $("#phone2").removeClass("error");
            errorMsg2.html("");
            errorMsg2.addClass("hide");
            validMsg2.addClass("hide");
        };

        // on blur: validate
        $("#phone2").on('blur', function() {
            reset();
            if ($("#phone2").val().trim()) {
                if ($("#phone2").intlTelInput("isValidNumber")) {
                    $("#phone2").val($("#phone2").intlTelInput("getNumber"));
                    validMsg2.removeClass("hide");
                } else {
                    $("#phone2").addClass("error");
                    var errorCode2 = $("#phone2").intlTelInput("getValidationError");
                    errorMsg2.html(errorMap2[errorCode2]);
                    errorMsg2.removeClass("hide");
                }
            }
        });

        // on keyup / change flag: reset
        $("#phone2").on('change', reset2);
        $("#phone2").on('keyup', reset2);


        $("#country").on('change', function() {
            $('#phone').intlTelInput('setCountry', $(this).val() );
            $('#phone2').intlTelInput('setCountry', $(this).val() );
        });
    </script>
</body>
</html>