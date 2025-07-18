<?php
/**
 * Shortcodes functionality.
 *
 * @package    Rise
 * @subpackage Rise/Core
 *
 * @author     Roundhouse Designs <nick@roundhouse-designs.com>
 *
 * @since      1.2.0
 */

namespace RHD\Rise\Core;

/**
 * Shortcodes class.
 *
 * @since 1.2.0
 */
class Shortcodes {
	/**
	 * Initialize the class.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.2.0
	 */
	private function init_hooks() {
		add_shortcode( 'rise_copyright_year', [$this, 'copyright_year'] );
		add_shortcode( 'rise_bloomerang_self_hosted_form', [$this, 'bloomerang_self_hosted_form'] );
	}

	/**
	 * Display the current year with a copyright symbol.
	 *
	 * @since 1.2.0
	 *
	 * @return string The HTML of the year.
	 */
	public function copyright_year() {
		return sprintf(
			esc_html__( '© %d', 'rise' ),
			date( 'Y' )
		);
	}

	/**
	 * Display the Bloomerang self-hosted form.
	 *
	 * @since 1.2.0
	 *
	 * @param array $atts The attributes of the shortcode.
	 *
	 * @return string The HTML of the form.
	 */
	public function bloomerang_self_hosted_form( $atts ) {
		ob_start();
		?>
		<script type="text/javascript" id="bloomerangForm63488"></script>
<pre><script type="text/javascript">
    var insertForm63488 = function() {
        var html63488 = '<style text=\'text/css\'>' + 
'.donation-form label{color: #404040;' + 
'                display: block;}' + 
'' + 
'.donation-form label.error,' + 
'                     .donation-form #card-errors{color: #900;}' + 
'' + 
'.donation-form label.error{display: inline-block;' + 
'                padding: 0 10px;}' + 
'' + 
'.donation-form section{padding: 5px;}' + 
'' + 
'.donation-form .field{padding: 4px 0;}' + 
'' + 
'.donation-form .field .required-star{color: #aa0000; ' + 
'                display: inline-block; ' + 
'                margin-left: 5px;}' + 
'' + 
'.donation-form .field .checkboxes{max-width:275px;' + 
'                border: 1px solid #A9A9A9;' + 
'                -webkit-transition: all .3s ease-out;' + 
'                -moz-transition: all .3s ease-out;' + 
'                transition: all .3s ease-out;}' + 
'' + 
'.donation-form .field .checkbox{display:block;' + 
'                position:relative;' + 
'                -moz-box-sizing:border-box;' + 
'                box-sizing:border-box;' + 
'                height:30px;' + 
'                line-height:26px;' + 
'                padding:2px 28px 2px 8px;' + 
'                border-bottom:1px solid rgba(0,0,0,0.1);' + 
'                color:#404040;  ' + 
'                overflow:hidden;' + 
'                text-decoration:none; }' + 
'' + 
'.donation-form .field .checkbox input{opacity:0.01;' + 
'                position:absolute;' + 
'                left:-50px;  ' + 
'                z-index:-5;}' + 
'' + 
'.donation-form .field .checkbox:last-child{border-bottom:none;}' + 
'' + 
'.donation-form .field .checkbox.selected{background: rgb(50, 142, 253);' + 
'                color:#fff; }' + 
'' + 
'.donation-form .field .checkbox.selected:before{color:#fff;' + 
'                line-height:30px;' + 
'                position:absolute;' + 
'                right:10px; }' + 
'' + 
'.donation-form .field input,' + 
'                     .donation-form .field select,' + 
'                     .donation-form .field textarea,' + 
'                     .donation-form #card-element,' + 
'                     .donation-form #paypal-button-container{padding: 4px; ' + 
'                max-width: 275px;' + 
'                width: 96%}' + 
'' + 
'.donation-form .start-date label.error{padding: 4px; ' + 
'                max-width: 350px;' + 
'                width: 96%}' + 
'' + 
'.donation-form .field .StripeElement{background-color: white;' + 
'                background-color: -moz-field;' + 
'                border: 1px solid darkgray;' + 
'                display: inline-block;}' + 
'' + 
'.donation-form .field textarea.international{height: 120px;}' + 
'' + 
'.donation-form .field select{box-sizing: content-box;}' + 
'' + 
'.donation-form .field.radio label,' + 
'                     .donation-form .field.checkbox label{display: inline-block;}' + 
'' + 
'.donation-form .field.radio input,' + 
'                     .donation-form .field.checkbox input{width: auto;' + 
'                margin-left:0;}' + 
'' + 
'.donation-form .section.consent .field.checkbox:not(.consent-all){margin-left: 20px;}' + 
'' + 
'.donation-form #cardExpirationMonth,' + 
'                     .donation-form #cardExpirationYear{width: 85px;}' + 
'' + 
'.donation-form #security-code{width: 55px;}' + 
'' + 
'.donation-form .field .sidenote{color: #999;' + 
'                font-size: 10px; }' + 
'' + 
'.donation-form .field.other-option,' + 
'                     .donation-form .field.other-amount,' + 
'                     .donation-form .field.cardExpirationMonth,' + 
'                     .donation-form .field.cardExpirationYear{display: inline-block;}' + 
'' + 
'.donation-form .field.other-amount{padding-left: 6px;}' + 
'' + 
'.donation-form .field.cardExpirationMonth{padding-right: 20px;}' + 
'' + 
'.donation-form #other-amount{width: 65px;}' + 
'' + 
'.donation-form .errors{border: 1px solid #900;' + 
'                color: #900;  ' + 
'                padding: 10px;}' + 
'' + 
'.donation-form .radio label.error{display: inline-block;' + 
'                padding-right: 86%;  ' + 
'                float: right;}' + 
'' + 
'.donation-form .hidden{display: none;}' + 
'' + 
'.btn-group .btn-submit-donation{padding: 4px 10px;}' + 
'' + 
'iframe#spreedly-iframe[style=\'visibility: hidden;\']{display: none;}' + 
'' + 
'input, select, textarea, button{font-family: inherit;}' + 
'' + 
'#spreedly-modal-overlay[style="visibility:hidden"]{display: none;}' + 
'' + 
'</style>' + 
'' + 
'<div id="donation-form-container">' + 
'  <form id="donation-form" class="donation-form" method="post" onsubmit="event.preventDefault(); collectPayment();">' + 
'    <div class="errors hidden"></div>' + 
'    <div class="section donation">' + 
'      <h3>Donation</h3>' + 
'      <div class="field radio 66560 required" >' + 
'<label for="66560"><input id="66560" name="donation-level" class="required" type="radio" value="25.000000" maxlength="255"></input><span class="label">$25.00 - Support RISE Theatre</span></label>' + 
'</div>' + 
'<div class="field radio 66561 required" >' + 
'<label for="66561"><input id="66561" name="donation-level" class="required" type="radio" value="50.000000" maxlength="255"></input><span class="label">$50.00 - Support RISE Theatre</span></label>' + 
'</div>' + 
'<div class="field radio 66562 required" >' + 
'<label for="66562"><input id="66562" name="donation-level" class="required" type="radio" value="100.000000" maxlength="255"></input><span class="label">$100.00 - Support RISE Theatre</span></label>' + 
'</div>' + 
'<div class="field radio 66563 required" >' + 
'<label for="66563"><input id="66563" name="donation-level" class="required" type="radio" value="500.000000" maxlength="255"></input><span class="label">$500.00 - Support RISE Theatre</span></label>' + 
'</div>' + 
'<div class="field radio 66564 required" >' + 
'<label for="66564"><input id="66564" name="donation-level" class="required" type="radio" value="1000.000000" maxlength="255"></input><span class="label">$1,000.00 - Support RISE Theatre</span></label>' + 
'</div>' + 
'<div class="field radio other-option required" >' + 
'<label for="other-option"><input id="other-option" name="donation-level" class="required" type="radio" maxlength="255"></input><span class="label">Other</span></label>' + 
'</div>' + 
'<div class="field text other-amount" >' + 
'<input id="other-amount" name="other-amount" class="otherAmount" type="text" placeholder="$0.00"></input>' + 
'</div>' + 
'' + 
'    <div class="section recurring">' + 
'      <div class="field checkbox recurring" >' + 
'<label for="recurring"><input id="recurring" name="recurring" type="checkbox" maxlength="255"></input><span class="label">Show my support by making this a recurring donation</span></label>' + 
'</div>' + 
'<div class="field label recurring-paypal-venmo-label-info" >' + 
'<label for="recurring-paypal-venmo-label-info"><span class="label">(Not available for payments made through PayPal or Venmo.)</span></label>' + 
'<html id="recurring-paypal-venmo-label-info" name="recurring-paypal-venmo-label-info" type="label" maxlength="255"></html>' + 
'</div>' + 
'<div class="field  frequency required" style="display: none">' + 
'<label for="frequency">Frequency<span class="required-star">*</span></label>' + 
'<select id="frequency" name="frequency" class="required"><option value=""></option>' + 
'<option value="Weekly">Weekly</option>' + 
'<option value="Monthly" selected>Monthly</option>' + 
'<option value="Quarterly">Quarterly</option>' + 
'<option value="SemiAnnually">Every six months</option>' + 
'<option value="Yearly">Yearly</option>' + 
'</select>' + 
'</div>' + 
'' + 
'</div>' + 
'</div>' + 
'    ' + 
'    ' + 
'    <div class="section contact">' + 
'      <h3>Contact Information</h3>' + 
'      <div class="field text first-name required" >' + 
'<label for="first-name"><span class="label">First Name</span><span class="required-star">*</span></label>' + 
'<input id="first-name" name="first-name" class="required" type="text"></input>' + 
'</div>' + 
'<div class="field text last-name required" >' + 
'<label for="last-name"><span class="label">Last Name</span><span class="required-star">*</span></label>' + 
'<input id="last-name" name="last-name" class="required" type="text"></input>' + 
'</div>' + 
'<div class="field email email-address required" >' + 
'<label for="email-address"><span class="label">Email</span><span class="required-star">*</span></label>' + 
'<input id="email-address" name="email-address" class="email required" type="email" placeholder="someone@website.com"></input>' + 
'</div>' + 
'<div class="field tel phone-number" >' + 
'<label for="phone-number"><span class="label">Phone</span></label>' + 
'<input id="phone-number" name="phone-number" class="phoneUS" type="tel"></input>' + 
'</div>' + 
'' + 
'</div>' + 
'    <div class="section billing-address">' + 
'      <h3>Billing Address</h3>' + 
'      <div class="field  country required" >' + 
'<label for="country">Country<span class="required-star">*</span></label>' + 
'<select id="country" name="country" class="required"><option value=""></option>' + 
'<option value="AF">Afghanistan</option>' + 
'<option value="AX">Aland Islands</option>' + 
'<option value="AL">Albania</option>' + 
'<option value="DZ">Algeria</option>' + 
'<option value="AS">American Samoa</option>' + 
'<option value="AD">Andorra</option>' + 
'<option value="AO">Angola</option>' + 
'<option value="AI">Anguilla</option>' + 
'<option value="AQ">Antarctica</option>' + 
'<option value="AG">Antigua and Barbuda</option>' + 
'<option value="AR">Argentina</option>' + 
'<option value="AM">Armenia</option>' + 
'<option value="AW">Aruba</option>' + 
'<option value="AU">Australia</option>' + 
'<option value="AT">Austria</option>' + 
'<option value="AZ">Azerbaijan</option>' + 
'<option value="BS">Bahamas</option>' + 
'<option value="BH">Bahrain</option>' + 
'<option value="BD">Bangladesh</option>' + 
'<option value="BB">Barbados</option>' + 
'<option value="BY">Belarus</option>' + 
'<option value="BE">Belgium</option>' + 
'<option value="BZ">Belize</option>' + 
'<option value="BJ">Benin</option>' + 
'<option value="BM">Bermuda</option>' + 
'<option value="BT">Bhutan</option>' + 
'<option value="BO">Bolivia</option>' + 
'<option value="BA">Bosnia and Herzegovina</option>' + 
'<option value="BW">Botswana</option>' + 
'<option value="BV">Bouvet Island</option>' + 
'<option value="BR">Brazil</option>' + 
'<option value="IO">British Indian Ocean Territory</option>' + 
'<option value="BN">Brunei Darussalam</option>' + 
'<option value="BG">Bulgaria</option>' + 
'<option value="BF">Burkina Faso</option>' + 
'<option value="BI">Burundi</option>' + 
'<option value="KH">Cambodia</option>' + 
'<option value="CM">Cameroon</option>' + 
'<option value="CA">Canada</option>' + 
'<option value="CV">Cape Verde</option>' + 
'<option value="KY">Cayman Islands</option>' + 
'<option value="CF">Central African Republic</option>' + 
'<option value="TD">Chad</option>' + 
'<option value="CL">Chile</option>' + 
'<option value="CN">China</option>' + 
'<option value="CX">Christmas Island</option>' + 
'<option value="CC">Cocos (Keeling) Islands</option>' + 
'<option value="CO">Colombia</option>' + 
'<option value="KM">Comoros</option>' + 
'<option value="CG">Congo</option>' + 
'<option value="CD">Democratic Republic of the Congo</option>' + 
'<option value="CK">Cook Islands</option>' + 
'<option value="CR">Costa Rica</option>' + 
'<option value="CI">Cote d\'Ivoire</option>' + 
'<option value="HR">Croatia</option>' + 
'<option value="CU">Cuba</option>' + 
'<option value="CW">Curacao</option>' + 
'<option value="CY">Cyprus</option>' + 
'<option value="CZ">Czech Republic</option>' + 
'<option value="DK">Denmark</option>' + 
'<option value="DJ">Djibouti</option>' + 
'<option value="DM">Dominica</option>' + 
'<option value="DO">Dominican Republic</option>' + 
'<option value="EC">Ecuador</option>' + 
'<option value="EG">Egypt</option>' + 
'<option value="SV">El Salvador</option>' + 
'<option value="GQ">Equatorial Guinea</option>' + 
'<option value="ER">Eritrea</option>' + 
'<option value="EE">Estonia</option>' + 
'<option value="SZ">Eswatini</option>' + 
'<option value="ET">Ethiopia</option>' + 
'<option value="FK">Falkland Islands (Malvinas)</option>' + 
'<option value="FO">Faroe Islands</option>' + 
'<option value="FJ">Fiji</option>' + 
'<option value="FI">Finland</option>' + 
'<option value="FR">France</option>' + 
'<option value="GF">French Guiana</option>' + 
'<option value="PF">French Polynesia</option>' + 
'<option value="TF">French Southern Territories</option>' + 
'<option value="GA">Gabon</option>' + 
'<option value="GM">Gambia</option>' + 
'<option value="GE">Georgia</option>' + 
'<option value="DE">Germany</option>' + 
'<option value="GH">Ghana</option>' + 
'<option value="GI">Gibraltar</option>' + 
'<option value="GR">Greece</option>' + 
'<option value="GL">Greenland</option>' + 
'<option value="GD">Grenada</option>' + 
'<option value="GP">Guadeloupe</option>' + 
'<option value="GU">Guam</option>' + 
'<option value="GT">Guatemala</option>' + 
'<option value="GG">Guernsey</option>' + 
'<option value="GN">Guinea</option>' + 
'<option value="GW">Guinea-Bissau</option>' + 
'<option value="GY">Guyana</option>' + 
'<option value="HT">Haiti</option>' + 
'<option value="HM">Heard Island</option>' + 
'<option value="HN">Honduras</option>' + 
'<option value="HK">Hong Kong</option>' + 
'<option value="HU">Hungary</option>' + 
'<option value="IS">Iceland</option>' + 
'<option value="IN">India</option>' + 
'<option value="ID">Indonesia</option>' + 
'<option value="IR">Islamic Republic of Iran</option>' + 
'<option value="IQ">Iraq</option>' + 
'<option value="IE">Ireland</option>' + 
'<option value="IM">Isle of Man</option>' + 
'<option value="IL">Israel</option>' + 
'<option value="IT">Italy</option>' + 
'<option value="JM">Jamaica</option>' + 
'<option value="JP">Japan</option>' + 
'<option value="JE">Jersey</option>' + 
'<option value="JO">Jordan</option>' + 
'<option value="KZ">Kazakhstan</option>' + 
'<option value="KE">Kenya</option>' + 
'<option value="KI">Kiribati</option>' + 
'<option value="KP">Democratic People\'s Republic of Korea</option>' + 
'<option value="KR">Republic of Korea</option>' + 
'<option value="KW">Kuwait</option>' + 
'<option value="KG">Kyrgyzstan</option>' + 
'<option value="LA">Lao People\'s Democratic Republic</option>' + 
'<option value="LV">Latvia</option>' + 
'<option value="LB">Lebanon</option>' + 
'<option value="LS">Lesotho</option>' + 
'<option value="LR">Liberia</option>' + 
'<option value="LY">Libya</option>' + 
'<option value="LI">Liechtenstein</option>' + 
'<option value="LT">Lithuania</option>' + 
'<option value="LU">Luxembourg</option>' + 
'<option value="MO">Macao</option>' + 
'<option value="MK">Macedonia</option>' + 
'<option value="MG">Madagascar</option>' + 
'<option value="MW">Malawi</option>' + 
'<option value="MY">Malaysia</option>' + 
'<option value="MV">Maldives</option>' + 
'<option value="ML">Mali</option>' + 
'<option value="MT">Malta</option>' + 
'<option value="MH">Marshall Islands</option>' + 
'<option value="MQ">Martinique</option>' + 
'<option value="MR">Mauritania</option>' + 
'<option value="MU">Mauritius</option>' + 
'<option value="YT">Mayotte</option>' + 
'<option value="MX">Mexico</option>' + 
'<option value="FM">Federated States of Micronesia</option>' + 
'<option value="MD">Republic of Moldova</option>' + 
'<option value="MC">Monaco</option>' + 
'<option value="MN">Mongolia</option>' + 
'<option value="ME">Montenegro</option>' + 
'<option value="MS">Montserrat</option>' + 
'<option value="MA">Morocco</option>' + 
'<option value="MZ">Mozambique</option>' + 
'<option value="MM">Myanmar</option>' + 
'<option value="NA">Namibia</option>' + 
'<option value="NR">Nauru</option>' + 
'<option value="NP">Nepal</option>' + 
'<option value="NL">Netherlands</option>' + 
'<option value="NC">New Caledonia</option>' + 
'<option value="NZ">New Zealand</option>' + 
'<option value="NI">Nicaragua</option>' + 
'<option value="NE">Niger</option>' + 
'<option value="NG">Nigeria</option>' + 
'<option value="NU">Niue</option>' + 
'<option value="NF">Norfolk Island</option>' + 
'<option value="MP">Northern Mariana Islands</option>' + 
'<option value="NO">Norway</option>' + 
'<option value="OM">Oman</option>' + 
'<option value="PK">Pakistan</option>' + 
'<option value="PW">Palau</option>' + 
'<option value="PS">State of Palestine</option>' + 
'<option value="PA">Panama</option>' + 
'<option value="PG">Papua New Guinea</option>' + 
'<option value="PY">Paraguay</option>' + 
'<option value="PE">Peru</option>' + 
'<option value="PH">Philippines</option>' + 
'<option value="PN">Pitcairn</option>' + 
'<option value="PL">Poland</option>' + 
'<option value="PT">Portugal</option>' + 
'<option value="PR">Puerto Rico</option>' + 
'<option value="QA">Qatar</option>' + 
'<option value="RE">Reunion</option>' + 
'<option value="RO">Romania</option>' + 
'<option value="RU">Russian Federation</option>' + 
'<option value="RW">Rwanda</option>' + 
'<option value="BL">Saint Barthelemy</option>' + 
'<option value="SH">Ascension and Tristan da Cunha Saint Helena</option>' + 
'<option value="KN">Saint Kitts and Nevis</option>' + 
'<option value="LC">Saint Lucia</option>' + 
'<option value="MF">Saint Martin (French part)</option>' + 
'<option value="PM">Saint Pierre and Miquelon</option>' + 
'<option value="VC">Saint Vincent and the Grenadines</option>' + 
'<option value="WS">Samoa</option>' + 
'<option value="SM">San Marino</option>' + 
'<option value="ST">Sao Tome and Principe</option>' + 
'<option value="SA">Saudi Arabia</option>' + 
'<option value="SN">Senegal</option>' + 
'<option value="RS">Serbia</option>' + 
'<option value="SC">Seychelles</option>' + 
'<option value="SL">Sierra Leone</option>' + 
'<option value="SG">Singapore</option>' + 
'<option value="SX">Sint Maarten (Dutch part)</option>' + 
'<option value="SK">Slovakia</option>' + 
'<option value="SI">Slovenia</option>' + 
'<option value="SB">Solomon Islands</option>' + 
'<option value="SO">Somalia</option>' + 
'<option value="ZA">South Africa</option>' + 
'<option value="GS">South Georgia</option>' + 
'<option value="SS">South Sudan</option>' + 
'<option value="ES">Spain</option>' + 
'<option value="LK">Sri Lanka</option>' + 
'<option value="SD">Sudan</option>' + 
'<option value="SR">Suriname</option>' + 
'<option value="SJ">Svalbard and Jan Mayen</option>' + 
'<option value="SE">Sweden</option>' + 
'<option value="CH">Switzerland</option>' + 
'<option value="SY">Syrian Arab Republic</option>' + 
'<option value="TW">Taiwan</option>' + 
'<option value="TJ">Tajikistan</option>' + 
'<option value="TZ">United Republic of Tanzania</option>' + 
'<option value="TH">Thailand</option>' + 
'<option value="TL">Timor-Leste</option>' + 
'<option value="TG">Togo</option>' + 
'<option value="TK">Tokelau</option>' + 
'<option value="TO">Tonga</option>' + 
'<option value="TT">Trinidad and Tobago</option>' + 
'<option value="TN">Tunisia</option>' + 
'<option value="TR">Turkey</option>' + 
'<option value="TM">Turkmenistan</option>' + 
'<option value="TC">Turks and Caicos Islands</option>' + 
'<option value="TV">Tuvalu</option>' + 
'<option value="UG">Uganda</option>' + 
'<option value="UA">Ukraine</option>' + 
'<option value="AE">United Arab Emirates</option>' + 
'<option value="GB">United Kingdom</option>' + 
'<option value="US" selected>United States</option>' + 
'<option value="UM">United States Minor Outlying Islands</option>' + 
'<option value="UY">Uruguay</option>' + 
'<option value="UZ">Uzbekistan</option>' + 
'<option value="VU">Vanuatu</option>' + 
'<option value="VA">Vatican City</option>' + 
'<option value="VE">Venezuela</option>' + 
'<option value="VN">Viet Nam</option>' + 
'<option value="VG">British Virgin Islands</option>' + 
'<option value="VI">U.S. Virgin Islands</option>' + 
'<option value="WF">Wallis and Futuna</option>' + 
'<option value="EH">Western Sahara</option>' + 
'<option value="YE">Yemen</option>' + 
'<option value="ZM">Zambia</option>' + 
'<option value="ZW">Zimbabwe</option>' + 
'</select>' + 
'</div>' + 
'<div class="field  street-address required" >' + 
'<label for="street-address"><span class="label">Address</span><span class="required-star">*</span></label>' + 
'<textarea id="street-address" name="street-address" class="required"></textarea>' + 
'</div>' + 
'<div class="field text city required" >' + 
'<label for="city"><span class="label">City</span><span class="required-star">*</span></label>' + 
'<input id="city" name="city" class="required" type="text" data-us-label="City" data-bm-label="Parish"></input>' + 
'</div>' + 
'<div class="field  state required" >' + 
'<label for="state">State<span class="required-star">*</span></label>' + 
'<select id="state" name="state" class="required"><option value=""></option>' + 
'<option value="AL">Alabama</option>' + 
'<option value="AK">Alaska</option>' + 
'<option value="AS">American Samoa</option>' + 
'<option value="AZ">Arizona</option>' + 
'<option value="AR">Arkansas</option>' + 
'<option value="AE">Armed Forces Africa, Canada, Europe, Middle East</option>' + 
'<option value="AA">Armed Forces Americas (except Canada)</option>' + 
'<option value="AP">Armed Forces Pacific</option>' + 
'<option value="CA">California</option>' + 
'<option value="CO">Colorado</option>' + 
'<option value="CT">Connecticut</option>' + 
'<option value="DE">Delaware</option>' + 
'<option value="DC">District of Columbia</option>' + 
'<option value="FL">Florida</option>' + 
'<option value="GA">Georgia</option>' + 
'<option value="GU">Guam</option>' + 
'<option value="HI">Hawaii</option>' + 
'<option value="ID">Idaho</option>' + 
'<option value="IL">Illinois</option>' + 
'<option value="IN">Indiana</option>' + 
'<option value="IA">Iowa</option>' + 
'<option value="KS">Kansas</option>' + 
'<option value="KY">Kentucky</option>' + 
'<option value="LA">Louisiana</option>' + 
'<option value="ME">Maine</option>' + 
'<option value="MD">Maryland</option>' + 
'<option value="MA">Massachusetts</option>' + 
'<option value="MI">Michigan</option>' + 
'<option value="MN">Minnesota</option>' + 
'<option value="MS">Mississippi</option>' + 
'<option value="MO">Missouri</option>' + 
'<option value="MT">Montana</option>' + 
'<option value="NE">Nebraska</option>' + 
'<option value="NV">Nevada</option>' + 
'<option value="NH">New Hampshire</option>' + 
'<option value="NJ">New Jersey</option>' + 
'<option value="NM">New Mexico</option>' + 
'<option value="NY">New York</option>' + 
'<option value="NC">North Carolina</option>' + 
'<option value="ND">North Dakota</option>' + 
'<option value="OH">Ohio</option>' + 
'<option value="OK">Oklahoma</option>' + 
'<option value="OR">Oregon</option>' + 
'<option value="PA">Pennsylvania</option>' + 
'<option value="PR">Puerto Rico</option>' + 
'<option value="RI">Rhode Island</option>' + 
'<option value="SC">South Carolina</option>' + 
'<option value="SD">South Dakota</option>' + 
'<option value="TN">Tennessee</option>' + 
'<option value="TX">Texas</option>' + 
'<option value="VI">US Virgin Islands</option>' + 
'<option value="UT">Utah</option>' + 
'<option value="VT">Vermont</option>' + 
'<option value="VA">Virginia</option>' + 
'<option value="WA">Washington</option>' + 
'<option value="WV">West Virginia</option>' + 
'<option value="WI">Wisconsin</option>' + 
'<option value="WY">Wyoming</option>' + 
'</select>' + 
'</div>' + 
'<div class="field  province required" style="display: none">' + 
'<label for="province">Province<span class="required-star">*</span></label>' + 
'<select id="province" name="province" class="required"><option value=""></option>' + 
'<option value="AB">Alberta</option>' + 
'<option value="BC">British Columbia</option>' + 
'<option value="MB">Manitoba</option>' + 
'<option value="NB">New Brunswick</option>' + 
'<option value="NL">Newfoundland and Labrador</option>' + 
'<option value="NT">Northwest Territories</option>' + 
'<option value="NS">Nova Scotia</option>' + 
'<option value="NU">Nunavut</option>' + 
'<option value="ON">Ontario</option>' + 
'<option value="PE">Prince Edward Island</option>' + 
'<option value="QC">Quebec</option>' + 
'<option value="SK">Saskatchewan</option>' + 
'<option value="YT">Yukon Territory</option>' + 
'</select>' + 
'</div>' + 
'<div class="field number zip-code required" >' + 
'<label for="zip-code"><span class="label">ZIP Code</span><span class="required-star">*</span></label>' + 
'<input id="zip-code" name="zip-code" class="zipcodeUS required" type="number" minlength="5" maxlength="10"></input>' + 
'</div>' + 
'<div class="field text postal-code required" style="display: none">' + 
'<label for="postal-code"><span class="label">Postal Code</span><span class="required-star">*</span></label>' + 
'<input id="postal-code" name="postal-code" class="required" type="text"></input>' + 
'</div>' + 
'' + 
'</div>' + 
'    <div class="section payment">' + 
'      <h3>Payment Information</h3>' + 
'      <div class="field radio DebitCard required" >' + 
'<label for="DebitCard"><input id="DebitCard" name="payment-type" class="required" type="radio" maxlength="255"></input><span class="label">Debit Card</span></label>' + 
'</div>' + 
'<div class="field radio CreditCard required" >' + 
'<label for="CreditCard"><input id="CreditCard" name="payment-type" class="required" type="radio" maxlength="255"></input><span class="label">Credit Card</span></label>' + 
'</div>' + 
'<div class="field radio PayPalVenmo required" >' + 
'<label for="PayPalVenmo"><input id="PayPalVenmo" name="payment-type" class="required" type="radio" maxlength="255"></input><span class="label">PayPal or Venmo</span></label>' + 
'</div>' + 
'<div class="field text payment required" id="stripe-cc-fields">' + 
'                                  <label for="card-element">' + 
'                                    <span class="label">Credit or debit card</span>' + 
'                                    <span class="required-star">*</span>' + 
'                                  </label>' + 
'                                  <div id="card-element"></div>' + 
'                                  <div id="card-errors" role="alert"></div>' + 
'                                </div>' + 
'' + 
'</div>' + 
'    ' + 
'    <div class="section comment">' + 
'      <div class="field text comment" >' + 
'<label for="comment"><span class="label">Comments</span></label>' + 
'<textarea id="comment" name="comment" type="text" value="Comments"></textarea>' + 
'</div>' + 
'' + 
'</div>' + 
'    <div class="section consent hidden">' + 
'      <div class="field checkbox consent-all" >' + 
'<label for="consent-all"><input id="consent-all" name="consent-all" type="checkbox" maxlength="255"></input><span class="label">I would like to receive or continue receiving updates from Maestra Music</span></label>' + 
'</div>' + 
'<div class="field checkbox consent-email" style="display: none">' + 
'<label for="consent-email"><input id="consent-email" name="consent-email" type="checkbox" checked="checked" maxlength="255"></input><span class="label">by email</span></label>' + 
'</div>' + 
'<div class="field checkbox consent-mail" style="display: none">' + 
'<label for="consent-mail"><input id="consent-mail" name="consent-mail" type="checkbox" checked="checked" maxlength="255"></input><span class="label">by postal mail</span></label>' + 
'</div>' + 
'<div class="field checkbox consent-phone" style="display: none">' + 
'<label for="consent-phone"><input id="consent-phone" name="consent-phone" type="checkbox" checked="checked" maxlength="255"></input><span class="label">by phone</span></label>' + 
'</div>' + 
'' + 
'</div>' + 
'    <div class="section true-impact">' + 
'      <h3>Increase My Impact</h3>' + 
'      <div class="field checkbox true-impact" >' + 
'<label for="true-impact"><input id="true-impact" name="true-impact" type="checkbox" maxlength="255"></input><span class="label">Add [amount] to help cover our transaction fees</span></label>' + 
'</div>' + 
'' + 
'</div>' + 
'    <div class="section captcha">' + 
'      <label id=\'noCaptchaResponseError\' class=\'error noCaptchaResponseError\' style=\'display: none\'>You must fill out the CAPTCHA</label><div id="captcha63488"></div>' + 
'' + 
'</div>' + 
'    <div class="btn-group">' + 
'      <input class="btn btn-submit btn-submit-donation" type="submit" value="Enter Payment" id="express-submit" disabled="true" />' + 
'    </div>' + 
'    <div id="paypal-button-container"></div>' + 
'  </form>' + 
'</div>' + 
'<div id="donation-processing-container" style="display: none">' + 
'  <h2>Processing...</h2><p>Your transaction is being processed. Please do not close your browser or leave this page.</p>' + 
'</div>' + 
'' + 
'' +  '';var successHtml63488 = '<div class=\'donation-success\'>' +
                                                       '  <h2>Thank You for Your Donation!</h2>' +
                                                       '  <p>Thank you for contributing to Maestra Music, Inc and RISE Theatre!\r\u003cbr /\u003e\r\u003cbr /\u003eMaestra Music, Inc., is a 501(c)(3) charitable organization, EIN 83-3439518. All donations are deemed tax-deductible absent any limitations on deductibility applicable to a particular tax payer. No goods or services were provide in exchange for your contribution.</p>' + 
                                                        '\u003cdiv class=\"social-media-buttons\" style=\"margin-top:10px;\"\u003e\u003cdiv id=\"twitter-buttons\"\u003e\u003c/div\u003e\u003cdiv class=\"fb-like\" data-href=\"https://facebook.com/maestramusicorg/\" data-layout=\"button_count\" data-show-faces=\"true\" style=\"padding-right: 10px;\" data-action=\"like\" \u003e\u003c/div\u003e\u003cdiv id=\"facebookShareOnly\" class=\"fb-share-button\" data-href=\"https://facebook.com/maestramusicorg/\" data-layout=\"button_count\"\u003e\u003c/div\u003e\u003cscript type=\u0027text/javascript\u0027\u003edocument.getElementById(\u0027facebookShareOnly\u0027).setAttribute(\u0027data-href\u0027, window.location.href);\u003c/script\u003e\u003c/div\u003e \u003cscript\u003e\r\n                    if (navigator.userAgent.indexOf(\u0027Edge/\u0027) === -1) {\r\n                        !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\"http\":\"https\";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\"://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document, \"script\", \"twitter-wjs\");\r\n                        var twitterButtons = document.getElementById(\u0027twitter-buttons\u0027);\r\n                        twitterButtons.innerHTML = \u0027\u003cdiv id=\"tweetButton\" style=\"float:left; margin-right:10px\"\u003e \u003ca href=\"https://twitter.com/share\" data-text=\"I just made a donation to @MaestraMusicOrg. You should too!\" class=\"twitter-share-button\"\u003eTweet\u003c/a\u003e \u003c/div\u003e\u003cdiv id=\"followButton\"\u003e \u003ca href=\"https://twitter.com/maestramusicorg\" class=\"twitter-follow-button\"\u003eFollow\u003c/a\u003e \u003c/div\u003e\u0027;\r\n                        twitterButtons.style.height = \u002735px\u0027;\r\n                    }\r\n                \u003c/script\u003e \u003cdiv id=\"fb-root\"\u003e\u003c/div\u003e \u003cscript\u003e(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1\u0026version=v2.8\"; fjs.parentNode.insertBefore(js, fjs); }(document, \u0027script\u0027, \u0027facebook-jssdk\u0027));\u003c/script\u003e ' + 
                                                        '</div>';( function($) {if (Bloomerang.useDonationId('63488')) { 
                                Bloomerang.useProcessor('32769', 'Stripe', 'pk_live_51EBTbMK976kGZuMr9o0wgyDK50z7wjAeWgx0OnOzQeziP5ZE5Gq1aSPKe5jHTN5Y5yqR8i5rBWn9bfmfan151vgi00RnuMcwm3');
                            } else {
                                html63488 = '<p style="color: red">Only one donation or event registration form can be used on each page.</p>';
                            }if (jQuery('#bloomerangForm63488').length) {
                        
                    if (window.ActiveXObject) { // they are using IE < 11, which doesn't support TLS 1.1
                        html63488 = '<p style="color: red">Your browser does not support the minimum security requirements for keeping your Credit Card information safe when processing payments. Please upgrade your browser or download the latest version of' + 
                        ' <a target=\'_blank\' href=\'https://www.google.com/chrome/browser/desktop/\'>Chrome</a> or <a target=\'_blank\' href=\'https://www.mozilla.org/en-US/firefox/new/\'>Firefox</a>.</p>';
                    }
                        jQuery('#bloomerangForm63488').after(html63488);
                        
                         Bloomerang.Util.requireStripe(function() {
                                        Bloomerang.Util.Stripe = Stripe('pk_live_51EBTbMK976kGZuMr9o0wgyDK50z7wjAeWgx0OnOzQeziP5ZE5Gq1aSPKe5jHTN5Y5yqR8i5rBWn9bfmfan151vgi00RnuMcwm3'); 
                                        let elements = null;

                                            // Create an instance of the stripe card element.
                                            elements = Bloomerang.Util.Stripe.elements();
                                            Bloomerang.Util.StripeCard = elements.create('card', {
                                              hidePostalCode: true,
                                              style: {
                                                base: {
                                                  color: "#272D30",
                                                  fontSize: "14px",
                                                  fontFamily: "'Century Gothic', verdana, sans-serif"
                                                },
                                                invalid: {
                                                  color: "#272D30"
                                                }
                                              }
                                            });
                                     
                                         if(!!document.querySelector("#membership-form")){
                                            Bloomerang.Util.StripeCardIsValid = true;
                                            Bloomerang.Util.RecurringStripeCardIsValid = true;
                                            Bloomerang.Util.RecurringStripeCard.mount('#card-element');
                                        }
                                        else
                                        {
                                            // Add an instance of the stripe card/payment element into the `card-element` <div>.
                                            Bloomerang.Util.StripeCard.mount('#card-element');
                                            Bloomerang.Util.StripeCardIsValid = false;
                                        }
                                     
                                        // Handle real-time validation errors from the card Element.
                                        Bloomerang.Util.StripeCard.addEventListener('change', function(event) {
                                          var displayError = document.getElementById('card-errors');
                                          if (event.error) {
                                            displayError.textContent = event.error.message;
                                          } else {
                                            displayError.textContent = '';
                                          }
                                          if (event.complete) {
                                            Bloomerang.Util.StripeCardIsValid = true;
                                          } else {
                                            Bloomerang.Util.StripeCardIsValid = false;
                                          }
                                        });
                                      });

                                      jQuery(".donation-form #express-submit").val("Donate");
                                      jQuery(".registration-form #express-submit").val("Register");
                                      jQuery(".membership-form #express-submit").val("Join");
                                      jQuery("#express-submit").attr("disabled", false);
                        Bloomerang.Data.PayPal.IsPayPalVenmoEnabled = false;
                                           jQuery("#donation-form .recurring-paypal-venmo-label-info").remove();
                                           jQuery("#paypal-button-container").remove();
                                           jQuery("#donation-form .PayPalVenmo").remove();
                                           jQuery("#donation-form .PayPal").remove();
                                           jQuery("#donation-form .Venmo").remove();
Bloomerang.Data.PayPal.IsPayPalPaymentMethodVaultingEnabled = false;Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled = false;
                        jQuery("#donation-form .DebitCard").remove();jQuery("#donation-form .CreditCard").remove();jQuery("#donation-form .section.payment h3").remove();
                    };
                    if (Bloomerang.paymentFormLoaded) {
                                            return false;
                                        }
                                        Bloomerang.paymentFormLoaded = true;
                      
                        Bloomerang.reCaptchaV3SiteKey = '';
                        Bloomerang.gRecaptchaV3Threshold = 0.5;

                        window.captchaLoadCallback = function() {
                            Bloomerang.gRecaptchaLoaded = true;
                        };
                        
                        window.captchaV3LoadCallback = function() {
                            Bloomerang.gRecaptchaV3Loaded = true;
                        };

                        if(Bloomerang.donationFormReCaptchaV3SelfHostedFlag === undefined) {
                            function success (result) {
                                Bloomerang.donationFormReCaptchaV3SelfHostedFlag = result.DonationFormReCaptchaV3SelfHostedFlag;
                                initializeCaptcha();
                            }
                                
                            function failure (result) {
                                Bloomerang.donationFormReCaptchaV3SelfHostedFlag = false;
                                initializeCaptcha();
                            } 

                            Bloomerang.Api._post('v1/ReCaptchaV3SelfHostedFlag', {}, success, failure);
                        } else {
                            initializeCaptcha();
                        }

                        async function getRecaptchaV3Token() {
                              const token = await new Promise((resolve, reject) => {
                                 grecaptcha.ready(function() {
                                    grecaptcha.execute(Bloomerang.reCaptchaV3SiteKey, {action: 'submit'}).then(function(token) {
                                       resolve(token);
                                     });
                                  });
                                });

                                return token;
                        }
                        
                        function initializeCaptcha() {
                            Bloomerang.Util.load('https://www.google.com/recaptcha/api.js?onload=captchaLoadCallback&render=explicit',
                                function() { return Bloomerang.gRecaptchaLoaded; },
                                function() {
                                    jQuery('.section.captcha').removeAttr('style');
                                    jQuery('form.donation-form').data('captcha-id', grecaptcha.render('captcha63488', { 'sitekey' : '6Le7Z9EZAAAAAJuo02PdEKPhA_WtcZ7SCu8Whs8y' }));
                                    
                                    if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm && Bloomerang.isDonationForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey && Bloomerang.isDonationForm)) {
                                        jQuery('.section.captcha').attr('style', 'display: none');
                                    }
                                },
                                true,
                                true);
                            
                            if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm && Bloomerang.isDonationForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.isDonationForm && Bloomerang.reCaptchaV3SiteKey)) {
                                 Bloomerang.Util.load(`https://www.google.com/recaptcha/api.js?onload=captchaV3LoadCallback&render=${Bloomerang.reCaptchaV3SiteKey}`,
                                    function() { return Bloomerang.gRecaptchaV3Loaded; },
                                    function() {
                                        grecaptcha.ready(function() {
                                          grecaptcha.execute(Bloomerang.reCaptchaV3SiteKey, {action: 'submit'}).then(function(token) {
                                              async function success (result) {
                                                if(parseFloat(result.Score) < Bloomerang.gRecaptchaV3Threshold) {
                                                    Bloomerang.isForcePuzzle = true;
                                                    jQuery('.section.captcha').attr('style', 'display: block');
                                                } else {

                                                    const initialRecaptchaV3Token = await getRecaptchaV3Token();

                                                     // Append to the form data container to be included when submitting the form on bloomerangV2.js
                                                     Bloomerang.captchaResponseV3(initialRecaptchaV3Token);

                                                     setInterval(async ()=> {
                                                         const recaptchaV3Token = await getRecaptchaV3Token();

                                                        // Append to the form data container to be included when submitting the form on bloomerangV2.js
                                                        Bloomerang.captchaResponseV3(recaptchaV3Token);
                                                     }, 80000);
                                                }
                                               }
                                                
                                               function failure (result) {
                                                Bloomerang.isForcePuzzle = true;
                                                jQuery('.section.captcha').attr('style', 'display: block');
                                               }
                                                
                                               var data = {
                                                  token: token,
                                                  isSelfHosted: !Bloomerang.isBloomerangHostedForm
                                               };
                                                
                                                Bloomerang.Api._post('v1/ReCaptchaV3', data, success, failure);
                                          });
                                        });
                                    },
                                    true,
                                    true);
                               }
                           }
                            
                    
                    Bloomerang.transactionFee = 0.3; 
                    Bloomerang.transactionFeeRate = 0.032; 
                    Bloomerang.transactionFeeEft = 0; 
                    Bloomerang.transactionFeeRateEft = 0;
                    Bloomerang.transactionFeePayPal = 0.49; 
                    Bloomerang.transactionFeeRatePayPal = 0.032; 
                    
                    Bloomerang.useKey('pub_b5e75e9a-c3ba-11eb-ab84-0253c981a9f9');

        Bloomerang.isDonationForm=true;
        Bloomerang.Util.getDonationAmount = function() {
          return Number(accounting.unformat(jQuery(".donation-form .section.donation input[name='donation-level']:checked").val() || jQuery(".donation-form #donation-amount").val()));
        };

        // Register proper callbacks for various stages/outcomes of submission
        Bloomerang.Widget.Donation.OnSubmit = function (args) {
            jQuery(".btn-submit-donation").val("Donating...").prop("disabled", true).addClass("disabled");
            var val = function (selector) { return jQuery(selector).val(); };
            var country = val(".donation-form #country");
            var state = Bloomerang.Util.getCorrectState(country, val(".donation-form #state"), val(".donation-form #province"));
            var zipCode = Bloomerang.Util.getCorrectZipCode(country, val(".donation-form #zip-code"), val(".donation-form #postal-code"));
            Bloomerang.Account
                    .individual()
                    .firstName(val(".donation-form #first-name"))
                    .middleName(val(".donation-form #middle-name"))
                    .lastName(val(".donation-form #last-name"))
                    .homeAddress(val(".donation-form #street-address"),
                                 val(".donation-form #city"),
                                 state,
                                 zipCode,
                                 country)
                    .homeEmail(val(".donation-form #email-address"))
                    .homePhone(val(".donation-form #phone-number"))
                    .applyDonationCustomFields();

            if (jQuery(".donation-form #consent-all").prop("checked")) {
              Bloomerang.Account.optedInStatus(jQuery(".donation-form #consent-email").prop("checked"),
                                               jQuery(".donation-form #consent-mail").prop("checked"),
                                               jQuery(".donation-form #consent-phone").prop("checked"));
            }

            var amount = Bloomerang.Util.getDonationAmount() + Bloomerang.Util.getDonationTrueImpactAmount();
            if (jQuery(".donation-form #recurring").prop("checked")) {
                Bloomerang.RecurringDonation
                        .amount(amount)
                        .fundId(val(".donation-form #fund"))
                        .note(val(".donation-form #comment"))
                        .frequency(val(".donation-form #frequency") || "Monthly")
                        .startDate(val(".donation-form #start-date"))
                        .applyDonationCustomFields();

                // Need to do a null-check here because they might have a cached version of Bloomerang-v2.js
                if (Bloomerang.RecurringDonation.trueImpactEnabled && Bloomerang.RecurringDonation.trueImpactUsed) {
                  Bloomerang.RecurringDonation
                        .trueImpactEnabled(jQuery(".donation-form .true-impact .fee-amount").length > 0)
                        .trueImpactUsed(jQuery(".donation-form .true-impact input:checked").length > 0);
                }
            } else {
                Bloomerang.Donation
                        .amount(amount)
                        .fundId(val(".donation-form #fund"))
                        .note(val(".donation-form #comment"))
                        .applyDonationCustomFields();

                // Need to do a null-check here because they might have a cached version of Bloomerang-v2.js
                if (Bloomerang.Donation.trueImpactEnabled && Bloomerang.Donation.trueImpactUsed) {
                  Bloomerang.Donation
                        .trueImpactEnabled(jQuery(".donation-form .true-impact .fee-amount").length > 0)
                        .trueImpactUsed(jQuery(".donation-form .true-impact input:checked").length > 0);
                }
            }

            if (jQuery("#donation-form #Checking").is(":checked") ||
                jQuery("#donation-form #Savings").is(":checked")) {
              if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled === true) {
                Bloomerang.Eft
                  .accountNumber(val(".donation-form #accountNumber"))
                  .routingNumber(val(".donation-form #routingNumber"))
                  .type(jQuery("#donation-form .section.movedpayment input[type='radio']:checked").attr("id"));
              } else {
                Bloomerang.Eft
                  .accountNumber(val(".donation-form #accountNumber"))
                  .routingNumber(val(".donation-form #routingNumber"))
                  .type(jQuery("#donation-form .section.payment input[type='radio']:checked").attr("id"));
              }
            } else if (jQuery("#PayPalVenmo").is(":checked") || 
                jQuery("#PayPal").is(":checked") ||
                jQuery("#Venmo").is(":checked")) {
              if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled === true) {
                Bloomerang.Donation
                  .type(jQuery("#donation-form .section.movedpayment input[type='radio']:checked").attr("id"));
              } else {
                Bloomerang.Donation
                  .type(jQuery("#donation-form .section.payment input[type='radio']:checked").attr("id"));
              }
            }
            
        };
        Bloomerang.ValidateDonationFormCaptcha = function() {
           if (typeof(grecaptcha) !== "undefined" && (jQuery("#captcha" + Bloomerang.Data.WidgetIds.Donation).children().length || ((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)))) {
                var captchaResponse = grecaptcha.getResponse(jQuery(".donation-form").data("captcha-id"));
                if (captchaResponse) {
                    jQuery(".donation-form .noCaptchaResponseError").hide();
                    Bloomerang.captchaResponse(captchaResponse);
                    return true;
                } else {
                    jQuery(".donation-form .noCaptchaResponseError").show();
                    return false;
                }
            } else return true;
        };
        Bloomerang.scrollToElement = function(element) {
            var distance = 100;
            var offset = element.offset().top;
            var offsetTop = offset > distance ? offset - distance : offset;
		        jQuery('html, body').animate({ scrollTop : offsetTop}, 500);
        };
        Bloomerang.Api.OnSuccess = Bloomerang.Widget.Donation.OnSuccess = function (response) {
            jQuery("#donation-processing-container").hide();
            var formContainer = jQuery("#donation-form-container");
            formContainer.show();
            formContainer.html(successHtml63488);
            Bloomerang.scrollToElement(formContainer);
        };
        Bloomerang.Api.OnError = Bloomerang.Widget.Donation.OnError = function (response) {
            jQuery(".btn-submit-donation").prop("disabled", false).removeClass("disabled");
            Bloomerang.Util.updateDonateButtonText();
            jQuery("#donation-form-container .errors").removeClass("hidden").html(response.Message);
            jQuery("#donation-processing-container").hide();
            jQuery("#donation-form-container").show();
            Bloomerang.scrollToElement(jQuery("#donation-form-container .errors"));
            Bloomerang.cancelFinancialSubmission(jQuery("#donation-form"));
            if (typeof(SpreedlyExpress) !== 'undefined') { 
              SpreedlyExpress.unload();
              Bloomerang.initSpreedly();
            }
            if (typeof(grecaptcha) !== "undefined" && jQuery("#captcha" + Bloomerang.Data.WidgetIds.Donation).children().length) {
              grecaptcha.reset(jQuery(".donation-form").data("captcha-id"));
            }
        };
        
        Bloomerang.Util.applyDonationCustomFields = function (obj, type) {
        
            // Clear any fields from a previous failed submission
            obj.clearCustomFields();
        
            // Apply all <input> (not multiselect), <select> and <textarea> fields
            jQuery(".donation-form .section.custom-fields :input:not(a > input, select)[id*=" + type + "]").each(function() {
                if (jQuery(this).val().hasValue()) {
                    obj.customFreeformField(jQuery(this).attr("id").toUntypedValue(), jQuery(this).val());
                }
            });
            
            // Apply all <select> fields
            jQuery(".donation-form .section.custom-fields select[id*=" + type + "]").each(function() {
                if (jQuery(this).val().hasValue()) {
                    obj.customPickField(jQuery(this).attr("id").toUntypedValue(), jQuery(this).val());
                }
            });
                
            // Apply all multiselect fields
            jQuery(".donation-form .section.custom-fields .checkboxes[id*=" + type + "]").each(function() {
                obj.customPickField(jQuery(this).attr("id").toUntypedValue(),
                jQuery.map(jQuery(this).children(".checkbox.selected"), function(v) { return jQuery(v).attr("data-id"); }));
            });
        };
        
        String.prototype.hasValue = function() {
            return (this && jQuery.trim(this)); //IE8 doesn't have a native trim function
        };
        
        Bloomerang.Account.applyDonationCustomFields = function () {
            Bloomerang.Util.applyDonationCustomFields(this, "Account");
            return this;
        };
        
        Bloomerang.Donation.applyDonationCustomFields = function () {
            Bloomerang.Util.applyDonationCustomFields(this, "Transaction");
            return this;
        };
        
        Bloomerang.RecurringDonation.applyDonationCustomFields = function () {
            Bloomerang.Util.applyDonationCustomFields(this, "Transaction");
            return this;
        };
        
        String.prototype.toUntypedValue = function() {
            return this.substring(this.indexOf('_') + 1);
        };
        
        Date.prototype.toDateInputValue = function() {
            var local = new Date(this);
            local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
            return (local.getMonth() + 1) + // Add one to the month because it starts at 0
                    "/" + local.getDate() + "/" + local.getFullYear();
        };
        
        jQuery(document).ready(function() {
            jQuery(".donation-form .field.start-date input").val(new Date().toDateInputValue());
            
            jQuery(".donation-form .field.start-date input").blur(function() {
              if(Bloomerang.Data.PayPal.IsPayPalVenmoEnabled && Bloomerang.Data.PayPal.IsPayPalPaymentMethodVaultingEnabled) {
                Bloomerang.Data.PayPal.ButtonsObject.close();
                if(Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled && !jQuery(".donation-form .field.start-date input").valid())
                  return;
                // Future date
                if(new Date(jQuery(".donation-form .field.start-date input").val()) > new Date(new Date().toISOString().split('T')[0])) {
                  if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled) {
                    var source = jQuery("#donation-form #Venmo").is(":checked") ? paypal.FUNDING.VENMO : paypal.FUNDING.PAYPAL;
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsFutureRecurringOptionsWithSource(source));
                  } else {
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsFutureRecurringOptions());
                  }
                } else {
                  Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Data.PayPal.ButtonsOptions);
                }
                Bloomerang.Data.PayPal.ButtonsObject.render('#paypal-button-container');
              }
            });
            
            // Re-render paypal buttons in case where user was initially doing a future recurring donation
            // then decided to uncheck the recurring donation checkbox
            jQuery(".donation-form #recurring").change(function() {
              if(Bloomerang.Data.PayPal.IsPayPalVenmoEnabled && Bloomerang.Data.PayPal.IsPayPalPaymentMethodVaultingEnabled) {
                Bloomerang.Data.PayPal.ButtonsObject.close();
                // Start Date is set to later than today
                if(this.checked && jQuery(".donation-form .field.start-date input").val() && new Date(jQuery(".donation-form .field.start-date input").val()) > new Date(new Date().toISOString().split('T')[0])) {
                  if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled) {
                    var source = jQuery("#donation-form #Venmo").is(":checked") ? paypal.FUNDING.VENMO : paypal.FUNDING.PAYPAL;
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsFutureRecurringOptionsWithSource(source));
                  } else {
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsFutureRecurringOptions());
                  }
                } else {
                  if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled) {
                    var source = jQuery("#donation-form #Venmo").is(":checked") ? paypal.FUNDING.VENMO : paypal.FUNDING.PAYPAL;
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsVaultingOptionsWithSource(source));
                  } else {
                    Bloomerang.Data.PayPal.ButtonsObject = paypal.Buttons(Bloomerang.Util.paypalButtonsVaultingOptions());
                  }
                }
                Bloomerang.Data.PayPal.ButtonsObject.render('#paypal-button-container');
              }
            });
        });
        
        // Hide recurring donation options if recurring donation box is unchecked
        jQuery(".donation-form .field.recurring").change(function() { 
            jQuery(".donation-form .field.recurring").siblings().not(".recurring-paypal-venmo-label-info").each(function(i, e) { 
                jQuery(e).toggle();
            }); 
        })
        
        // The other-amount field is only equired when the "Other" donation-level is selected
        toggleOtherAmountRequired = function () { 
          jQuery(".donation-form #other-amount").toggleClass("required",
            jQuery(".donation-form #other-option").prop("checked"));
          Bloomerang.Util.calculateDonationTrueImpact();
        };
        jQuery(".donation-form .section.donation input[name='donation-level']").change(function() {
           toggleOtherAmountRequired();
           Bloomerang.Util.updateDonateButtonText();
         });

        if (jQuery(".donation-form .true-impact label").length) {
          jQuery(".donation-form .true-impact label")[0].innerHTML = jQuery(".donation-form .true-impact label")[0].innerHTML.replace("[amount]", "<span class='fee-amount'>$0</span>");
        }
        Bloomerang.Util.calculateDonationTrueImpact = function() {
          if (!jQuery(".donation-form .true-impact .fee-amount").length) { return; }
          // Note that we don't really care about JS floating point math. It's OK if the numbers are a couple cents off.
          var amount = Bloomerang.Util.getDonationAmount();
          var isEft = (jQuery("#donation-form #Checking").is(":checked") || jQuery("#donation-form #Savings").is(":checked"));
          var isPayPalVenmo = jQuery("#donation-form #PayPalVenmo").is(":checked");
          var isPayPal = jQuery("#donation-form #PayPal").is(":checked");
          var isVenmo = jQuery("#donation-form #Venmo").is(":checked");
          var isStripeExpress = Bloomerang.Api.ProcessorType === "StripeExpress";
          var feeRate = isEft ? Bloomerang.transactionFeeRateEft : Bloomerang.transactionFeeRate;
          var newTotal = (amount + (isEft ? Bloomerang.transactionFeeEft : Bloomerang.transactionFee)) / (1 - feeRate);
          
          var impactAmount; 
          if(isPayPalVenmo || isPayPal || isVenmo){
            var paypalFee = Bloomerang.transactionFeePayPal;
            var paypalPercentage = Bloomerang.transactionFeeRatePayPal;
            newTotal = (amount + paypalFee) / (1 - paypalPercentage);
            impactAmount = Number((Math.floor((newTotal - amount)*100)/100).toFixed(2));
          }
          else{
            impactAmount = isStripeExpress
              ? Number((Math.floor((newTotal - amount)*100)/100).toFixed(2))
              : Number((newTotal - amount).toFixed(2));
          }
          
          jQuery(".donation-form .true-impact .fee-amount").text(accounting.formatMoney(impactAmount));
          return impactAmount;
        };
        Bloomerang.Util.getDonationTrueImpactAmount = function() {
          if (jQuery(".donation-form .true-impact input:checked").length) {
            return Bloomerang.Util.calculateDonationTrueImpact();
          }
          return 0;
        };
        
        Bloomerang.Util.updateDonateButtonText = function() {
          if (jQuery("#donation-form #Checking").is(":checked") ||
              jQuery("#donation-form #Savings").is(":checked") ||
              Bloomerang.Util.isProcessorStripe()) {
            var amount = Bloomerang.Util.getDonationAmount();
            var impactAmount = Bloomerang.Util.getDonationTrueImpactAmount();
            jQuery(".btn-submit-donation").val("Donate " + accounting.formatMoney(amount + impactAmount));
          } else {
            jQuery(".btn-submit-donation").val("Enter Payment");
          }
        };

        // Changing the value of other-amount should change the value of other-option
        jQuery(".donation-form #other-amount").change(function () { 
          jQuery(".donation-form #other-option").val(jQuery(this).val());
          Bloomerang.Util.calculateDonationTrueImpact();
          Bloomerang.Util.updateDonateButtonText();
        });
        
        jQuery(".donation-form #donation-amount").change(function() {
          Bloomerang.Util.calculateDonationTrueImpact();
          Bloomerang.Util.updateDonateButtonText();
        });
        
        // Clicking into the other-amount field should select the other-option
        jQuery(".donation-form #other-amount").click(function() { 
          jQuery(".donation-form #other-option").prop('checked',true);
          toggleOtherAmountRequired();
          Bloomerang.Util.calculateDonationTrueImpact();
        });
        
        jQuery.validator.addMethod("startDateVenmo", function (value, element) {
            return this.optional(element) || !jQuery("#donation-form #Venmo").is(":checked") || value <= new Date().toISOString().split('T')[0];
        }, "Recurring donations processed through Venmo must have the same start date as the day of the transaction.");

        jQuery.validator.addMethod("phoneUS", function(phone_number, element) {
            var digits = phone_number.replace(/\D/g, "");
            return this.optional(element) || digits.length == 7 || digits.length == 10 || digits.length == 11;
        }, "Please specify a valid phone number or use '+' for international.");

        jQuery.validator.addMethod("phoneInternational", function (phone_number, element) {
            return this.optional(element) || /^\+[0-9\-\(\)\s.]+$/i.test(phone_number);
        }, "Please specify a valid phone number.");
        jQuery.validator.classRuleSettings.phoneInternational = { phoneInternational: true };
        
        jQuery.validator.addMethod("zipcodeUS", function (value, element) {
            return this.optional(element) || /\d{5}-\d{4}$|^\d{5}$/.test(value)
        }, "The specified US ZIP Code is invalid");

        jQuery.validator.addMethod("currency", function (value, element, options) {
            return !value ||
                value
                  .replace("$", "")
                  .replace(".", "")
                  .split(",").join("")
                  .match(/^\d+$/g);
        }, "Not a valid currency");

        jQuery.validator.classRuleSettings.currency = { currency: true };
        
        // Validate the other amount, but only if they selected it
        jQuery.validator.addMethod("otherAmount", function(value, element, param) {
          if (jQuery(".donation-form #other-option").prop("checked")) {
            return jQuery.validator.methods.min.bind(this)(value, element, 1) &&
              jQuery.validator.methods.currency(value, element);
          }
          return true;
        }, "Invalid amount");
        
        jQuery.validator.classRuleSettings.otherAmount = { otherAmount: true };

        jQuery.validator.addMethod("number", function (value, element, options) {
          return !value ||
              value
                .replace(".", "")
                .split(",").join("")
                .match(/^\d+$/g);
        }, "Not a valid number");

        jQuery.validator.classRuleSettings.number = { number: true };
        
        jQuery.validator.addMethod("validYear", function (value, element, options) {
            try {
                return (!value || value.match(/^[1-9]\d\d\d$/)) ? true : false;
            }
            catch (e) {
                return false;
            }
        }, function () { return "Must be a 4 digit year"; });

        jQuery.validator.classRuleSettings.validYear = { validYear: true };
        
        // Validate that the donation amount is at least $1
        jQuery.validator.methods.min = function( value, element, param ) {
          if (typeof (accounting) === "undefined") { // rip out $ and ,
              value = ((value + "") || "").replace(/[\$,]/g, "");
          }
          else { // Use accounting.parse, to handle $ and ,
              value = accounting.parse(value);
          }
          return this.optional( element ) || value >= param;
        };
        jQuery.validator.classRuleSettings.minimum1 = { min: 1 };
        jQuery.validator.messages.min = 'Please enter a value of at least {0}.'

        jQuery(".donation-form #country").change(function(event) {
          var element = jQuery(event.target || event.srcElement); // cross-browser event target selection
          var isInternational = (element.val() != "US" && element.val() != "CA" && element.val() != "BM");
          // TODO: Remove this when we have figured out the canada state/province issue
          Bloomerang.Util.addLog("Pre country change: Country=" + element.val() + ", State=" + jQuery(".donation-form #state").val() + ", Province=" + jQuery(".donation-form #province").val() + ", City=" + jQuery(".donation-form #city").val());
          jQuery(".donation-form #state, .donation-form #province").val(""); // clear the state when the country changes
          jQuery(".donation-form .field.city, .donation-form .field.state, .donation-form .field.province, .donation-form .field.zip-code, .donation-form .field.postal-code").toggle(!isInternational);
          jQuery(".donation-form #street-address").toggleClass("international", isInternational);
          if (element.val() == "BM") {
            jQuery(".donation-form .field.city .label").text(jQuery(".donation-form .field.city input").data("bm-label"));
          } else if (element.val() == "US" || element.val() == "CA") {
            jQuery(".donation-form .field.city .label").text(jQuery(".donation-form .field.city input").data("us-label"));
          }
          if (element.val() == "US") {
            jQuery(".donation-form .field.state, .donation-form .field.zip-code").show();
            jQuery(".donation-form .field.province, .donation-form .field.postal-code").hide();
          } else if (element.val() == "CA") {
            jQuery(".donation-form .field.state, .donation-form .field.zip-code").hide();
            jQuery(".donation-form .field.province, .donation-form .field.postal-code").show();
          } else if (element.val() == "BM") {
            jQuery(".donation-form .field.state, .donation-form .field.province, .donation-form .field.zip-code").hide();
            jQuery(".donation-form .field.postal-code").show();
          } else {
            jQuery(".donation-form #city, .donation-form #postal-code, .donation-form #zip-code").val("");
          }
          jQuery(".donation-form .section.consent").toggleClass("hidden", !Bloomerang.Util.isCountryInEurope(element.val()));
          // TODO: Remove this when we have figured out the canada state/province issue
          Bloomerang.Util.addLog("Post country change: Country=" + element.val() + ", State=" + jQuery(".donation-form #state").val() + ", Province=" + jQuery(".donation-form #province").val()+ ", City=" + jQuery(".donation-form #city").val());
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        // We use the focusin function to save the previous value so we can log out the previous and new values on change
        // https://stackoverflow.com/questions/29118178/input-jquery-get-old-value-before-onchange-and-get-value-after-on-change/29118530
        jQuery(".donation-form #state").focusin(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          element.data('val', element.val());
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        jQuery(".donation-form #state").change(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          var prev = element.data('val');
          var current = element.val();
          var provinceElement = jQuery(".donation-form #province");
          var prevProvince = provinceElement.data('val');
          var currentProvince = provinceElement.val();
          Bloomerang.Util.addLog("State Changed: Target=" + e.target.name + ", PreValue=" + prev + ", Value=" + current + ", Province PreValue =" + prevProvince + ", Province CurrentValue =" + currentProvince);
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        // We use the focusin function to save the previous value so we can log out the previous and new values on change
        // https://stackoverflow.com/questions/29118178/input-jquery-get-old-value-before-onchange-and-get-value-after-on-change/29118530
        jQuery(".donation-form #province").focusin(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          element.data('val', element.val());
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        jQuery(".donation-form #province").change(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          var prev = element.data('val');
          var current = element.val();
          var stateElement = jQuery(".donation-form #state");
          var prevState = stateElement.data('val');
          var currentState = stateElement.val();
          Bloomerang.Util.addLog("Province Changed: Target=" + e.target.name + ", PreValue=" + prev + ", Value=" + current + ", State PrevValue=" + prevState + ", State Current=" + currentState);
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        // We use the focusin function to save the previous value so we can log out the previous and new values on change
        // https://stackoverflow.com/questions/29118178/input-jquery-get-old-value-before-onchange-and-get-value-after-on-change/29118530
        jQuery(".donation-form #city").focusin(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          element.data('val', element.val());
        });

        // TODO: Remove this when we have figured out the canada state/province issue
        jQuery(".donation-form #city").change(function(e) {
          var element = jQuery(e.target || e.srcElement); // cross-browser event target selection
          var prev = element.data('val');
          var current = element.val();
          Bloomerang.Util.addLog("City Changed: Target=" + e.target.name + ", PreValue=" + prev + ", Value=" + current);
        });

        jQuery(".donation-form #phone-number").change(function () {
          var phoneField = jQuery(".donation-form #phone-number");
          var internationalNumber = phoneField.val().substring(0,1) === '+';
          phoneField.toggleClass("phoneUS", !internationalNumber);
          phoneField.toggleClass("phoneInternational", internationalNumber);
        })

        collectPayment = async function () {
          var form = jQuery("#donation-form");
          
          // donationFormReCaptchaV3Flag is always undefined on self hosted forms so it will use the V2 captcha for stripe
          if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)) {
            if (!Bloomerang.ValidateDonationFormCaptcha() && Bloomerang.isForcePuzzle) {
              return false;
            }
          } else {
            if (!Bloomerang.ValidateDonationFormCaptcha()) {
              return false;
            }
          }

          if (!form.valid()) {
            return false;
          }
          
          var isEft = jQuery("#donation-form #CreditCard").length > 0 && 
                      !jQuery("#donation-form #CreditCard").prop("checked") && 
                      !jQuery("#donation-form #PayPalVenmo").prop("checked") && 
                      !jQuery("#donation-form #PayPal").prop("checked") && 
                      !jQuery("#donation-form #Venmo").prop("checked");
          
          if (Bloomerang.Util.isProcessorStripe() && !isEft && !Bloomerang.Util.StripeCardIsValid) {
            document.getElementById('card-errors').textContent = "Valid card info is required";
            return false;
          }
          
          if(Bloomerang.UsePaymentElement && !isEft && !Bloomerang.Util.RecurringStripeCardIsValid) {
            document.getElementById('card-errors').textContent = "Valid card info is required";
            return false;
          }
  
          if (isEft) {
            submitDonation();
          }
          else {
            var val = function (selector) { return jQuery(selector).val(); };
            var amount = Bloomerang.Util.getDonationAmount() + Bloomerang.Util.getDonationTrueImpactAmount();
            var selectedDonationLevel = jQuery(".donation-form .section.donation input[name='donation-level']:checked").parent().text();
            selectedDonationLevel = (selectedDonationLevel.indexOf("-") == -1 ? "" : selectedDonationLevel.substr(selectedDonationLevel.indexOf("-") + 2) );

            var oldMeta = '';
            if (jQuery('meta[name="viewport"]').length) {
              oldMeta = jQuery('meta[name="viewport"]').attr('content');
            } else {
              jQuery('head').append('<meta name="viewport" content="" />');
            }
            jQuery('meta[name="viewport"]').attr('content', 'width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1');
            jQuery('meta[name="viewport"]').attr('content', oldMeta);

            if (Bloomerang.Util.isProcessorStripe()) {
              submitDonation();
            } else {
              SpreedlyExpress.setDisplayOptions({
                "amount": accounting.formatMoney(amount),
                "full_name": val(".donation-form #first-name") + " " + val(".donation-form #last-name"),
                "sidebar_bottom_description": selectedDonationLevel,
                "submit_label": "Donate"});
              SpreedlyExpress.setPaymentMethodParams({
                "email": val(".donation-form #email-address"),
                "phone_number": val(".donation-form #phone-number"),
                "address1": val(".donation-form #street-address"),
                "city": val(".donation-form #city"),
                "state": val(".donation-form #state") || val(".donation-form #province"),
                "zip": val(".donation-form #zip-code") || val(".donation-form #postal-code"),
                "country": val(".donation-form #country")});

              SpreedlyExpress.onPaymentMethod(function(token, paymentMethod) {
                Bloomerang.CreditCard.spreedlyToken(token);
	              submitDonation();
	            });

              SpreedlyExpress.openView();
            }
          }
        };

        submitDonation = function() {
          if (!Bloomerang.continueFinancialSubmission(jQuery("#donation-form"))) { return false; }
  
          Bloomerang.Api.OnSubmit = Bloomerang.Widget.Donation.OnSubmit;
          Bloomerang.Api.OnSuccess = Bloomerang.Widget.Donation.OnSuccess;
          Bloomerang.Api.OnError = Bloomerang.Widget.Donation.OnError;

          var processingMessage = jQuery("#donation-processing-container");
          processingMessage.show();
          jQuery("#donation-form-container").hide();
          Bloomerang.scrollToElement(processingMessage);
  
          var tmp = jQuery(".donation-form #recurring").prop("checked")
            ? Bloomerang.Api.recurringDonate()
            : Bloomerang.Api.donate();
        };
        
        if(jQuery("#donation-form #DebitCard").length === 0 || jQuery("#donation-form #DebitCard").hasClass("hidden")) {
          jQuery("#donation-form #CreditCard").prop("checked", true);
        }
        else {
          jQuery("#donation-form #DebitCard").prop("checked", true);
        }
        
        
        jQuery("#donation-form .section.payment input[type='radio']").click(function() {
          Bloomerang.Util.calculateDonationTrueImpact();
          Bloomerang.Util.updateDonateButtonText();
          
          if (jQuery(this).attr("id") == "DebitCard" || jQuery(this).attr("id") == "CreditCard") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#paypal-button-container").hide();
            
            jQuery("#stripe-cc-fields").show();
            if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)) {
              if(Bloomerang.isForcePuzzle) {
                jQuery(".donation-form .section.captcha").show();
              }
            } else {
              jQuery(".donation-form .section.captcha").show();
            }
            jQuery("#donation-form .btn-group").show();
          }
          else if(jQuery(this).attr("id") == "PayPalVenmo") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            
            jQuery("#paypal-button-container").show();
          }
          else if(jQuery(this).attr("id") == "PayPal") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            Bloomerang.Util.renderOnlyPayPal(false);
            jQuery("#paypal-button-container").show();
          }
          else if(jQuery(this).attr("id") == "Venmo") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            Bloomerang.Util.renderOnlyVenmo();
            jQuery("#paypal-button-container").show();
          }
          else {                   
            jQuery("#stripe-cc-fields").hide();
            jQuery("#paypal-button-container").hide();
            
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").show();
            if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)) {
              if(Bloomerang.isForcePuzzle) {
                jQuery(".donation-form .section.captcha").show();
              }
            } else {
              jQuery(".donation-form .section.captcha").show();
            }
            jQuery("#donation-form .btn-group").show();
                    
            if (jQuery("#donation-form .sample-check").length == 0) {                    
              var checkImage = new Image();
              checkImage.src = 'https://s3-us-west-2.amazonaws.com/bloomerang-public-cdn/public-gallery/SampleCheck.png';
              jQuery(checkImage).addClass("sample-check");
              jQuery("#donation-form .accountNumber").after(checkImage);
            }
          }
          
        });
      
        jQuery("#donation-form .section.movedpayment input[type='radio']").click(function() {
          Bloomerang.Util.calculateDonationTrueImpact();
          Bloomerang.Util.updateDonateButtonText();
          if (Bloomerang.Data.PayPal.IsVenmoSameDayDonationEnabled && jQuery(".donation-form .field.start-date input").is(":visible")) {
            jQuery(".donation-form .field.start-date input").valid();
          }
          
          if (jQuery(this).attr("id") == "DebitCard" || jQuery(this).attr("id") == "CreditCard") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#paypal-button-container").hide();
            
            jQuery("#stripe-cc-fields").show();
            if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)) {
              if(Bloomerang.isForcePuzzle) {
                jQuery(".donation-form .section.captcha").show();
              }
            } else {
                jQuery(".donation-form .section.captcha").show();
            }
            jQuery("#donation-form .btn-group").show();
          }
          else if(jQuery(this).attr("id") == "PayPalVenmo") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            
            jQuery("#paypal-button-container").show();
          }
          else if(jQuery(this).attr("id") == "PayPal") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            Bloomerang.Util.renderOnlyPayPal(jQuery(".donation-form .field.start-date input").val() > new Date().toISOString().split('T')[0]);
            jQuery("#paypal-button-container").show();
          }
          else if(jQuery(this).attr("id") == "Venmo") {
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").hide();
            jQuery("#stripe-cc-fields").hide();
            jQuery(".donation-form .section.captcha").hide();
            jQuery("#donation-form .btn-group").hide();
            Bloomerang.Util.renderOnlyVenmo();
            jQuery("#paypal-button-container").show();
          }
          else {                   
            jQuery("#stripe-cc-fields").hide();
            jQuery("#paypal-button-container").hide();
            
            jQuery("#donation-form .accountNumber, \
                    #donation-form .routingNumber, \
                    #donation-form .sample-check").show();
            if((Bloomerang.donationFormReCaptchaV3Flag && Bloomerang.isBloomerangHostedForm) || (Bloomerang.donationFormReCaptchaV3SelfHostedFlag && !Bloomerang.isBloomerangHostedForm && Bloomerang.reCaptchaV3SiteKey)) {
              if(Bloomerang.isForcePuzzle) {
                jQuery(".donation-form .section.captcha").show();
              }
            } else {
              jQuery(".donation-form .section.captcha").show();
            }
            jQuery("#donation-form .btn-group").show();
                    
            if (jQuery("#donation-form .sample-check").length == 0) {                    
              var checkImage = new Image();
              checkImage.src = 'https://s3-us-west-2.amazonaws.com/bloomerang-public-cdn/public-gallery/SampleCheck.png';
              jQuery(checkImage).addClass("sample-check");
              jQuery("#donation-form .accountNumber").after(checkImage);
            }
          }
          
        });
      
      jQuery("#donation-form #true-impact").change(function() {
        Bloomerang.Util.updateDonateButtonText();
      });

      // Show opt-in options based on the setting of the global opt-in
      jQuery(".donation-form .field.consent-all").change(function() { 
          jQuery(".donation-form .field.consent-all").siblings().each(function(i, e) { 
              jQuery(e).toggle();
          }); 
      });
        
})(jQuery);
    };
    
                var startBloomerangLoad = function() {
                    if (window.bloomerangLoadStarted == undefined) {
                        window.bloomerangLoadStarted = true;
                        var script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = 'https://crm.bloomerang.co/Content/Scripts/Api/Bloomerang-v2.js?nocache=1751049474';
                        document.getElementsByTagName('head')[0].appendChild(script);
                        waitForBloomerangLoad(function() { Bloomerang.Util.requireJQueryValidationAndStripe(function() { insertForm63488(); })});
                    }
                    else {
                        waitForBloomerangLoad(function() { Bloomerang.Util.requireJQueryValidationAndStripe(function() { insertForm63488(); })});
                    }
                };

                var waitForBloomerangLoad = function(callback) {
                    if (typeof(Bloomerang) === 'undefined' || !Bloomerang._isReady) {
                        setTimeout(function () { waitForBloomerangLoad(callback) }, 500);
                    }
                    else {
                        if (true) {
                            callback();
                        } else {
                            window.bloomerangLoadStarted = undefined;
                            Bloomerang = undefined; // The version of Blomerang.js is not what we want. So blow it away and reload.
                            startBloomerangLoad();
                        }
                    }
                };

                startBloomerangLoad();
			</script></pre>
		<?php

		return ob_get_clean();
	}
}
