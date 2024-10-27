
//VARIABLES
var mieAdSauceAdSetupObject = {
	bearerToken: '',
	websiteFK: null,
	websiteLocationFK: null,
	user: {},
	website: {},
	websiteLocation: {},
	websites: [],
	websiteLocations: []
};

var mieAdSauceSettings = {
	websiteNameComboId: null,
	websiteLocationNameTypeComboId: null,
	websiteLocationIdHiddenId: null,
	adSizeTypeNameHiddenId: null,
	heightHiddenId: null,
	widthHiddenId: null,
	oldValueLoaded: false
};


//EVENTS
function websiteLocationChanged () {
	var websiteName = jQuery('#' + mieAdSauceSettings.websiteNameComboId).val();
	var websiteLocationNameType = jQuery('#' + mieAdSauceSettings.websiteLocationNameTypeComboId).val();

	for(var i = 0; i < mieAdSauceAdSetupObject.websites.length; i++) {
		if (websiteName == mieAdSauceAdSetupObject.websites[i].Name)
		{
			mieAdSauceAdSetupObject.website =  mieAdSauceAdSetupObject.websites[i];
			break;
		}
	}

	for(var i = 0; i < mieAdSauceAdSetupObject.websiteLocations.length; i++) {
		if (websiteLocationNameType == mieAdSauceAdSetupObject.websiteLocations[i].Name + ', ' + mieAdSauceAdSetupObject.websiteLocations[i].AdSizeTypeName)
		{
			mieAdSauceAdSetupObject.websiteLocation =  mieAdSauceAdSetupObject.websiteLocations[i];
			break;
		}
	}

	jQuery('#' + mieAdSauceSettings.websiteLocationIdHiddenId).val(mieAdSauceAdSetupObject.websiteLocation.Id);
	jQuery('#' + mieAdSauceSettings.adSizeTypeNameHiddenId).val(mieAdSauceAdSetupObject.websiteLocation.AdSizeType.Name);
	jQuery('#' + mieAdSauceSettings.heightHiddenId).val(mieAdSauceAdSetupObject.websiteLocation.AdSizeType.Height);
	jQuery('#' + mieAdSauceSettings.widthHiddenId).val(mieAdSauceAdSetupObject.websiteLocation.AdSizeType.Width);
}

function setData (username, password, websiteName, websiteLocationNameType, websiteLocationId, adSizeTypeName, height, width) {
	storeFormValues (websiteName, websiteLocationNameType, websiteLocationId, adSizeTypeName, height, width);

	getAdSauceBearerToken (username, password);
}


//REUSED FUNCTIONS
function blockUI (message) {
     var top = (jQuery(window).height() / 2) - 15;
     jQuery('body').append('<div id="blockUI" style="position: fixed; top: 0; left: 0; bottom: 0; right: 0; opacity: 0.6; background-color: black; z-index: 10000; cursor: wait;"><div style="margin: auto; text-align: center; width: 200px; height: 30px; margin-top: ' + top + 'px; background-color: black; opacity: 1;"><h1 style="color: white;">' + message + '</h1></div></div>');
}

function unblockUI (){
     jQuery('#blockUI').remove();
}

function setWebsitesComboBoxData() {
	populateDropDown(jQuery('#' + mieAdSauceSettings.websiteNameComboId), mieAdSauceAdSetupObject.websites, 'Name', 'Name')
}

function setWebsiteLocationsComboBoxData(){
	populateDropDown(jQuery('#' + mieAdSauceSettings.websiteLocationNameTypeComboId), mieAdSauceAdSetupObject.websiteLocations, ['Name', 'AdSizeTypeName'], ['Name', 'AdSizeTypeName'])
}

function updateWebsiteLocations() {
	var $webCombo = jQuery('#' + mieAdSauceSettings.websiteNameComboId);

	mieAdSauceAdSetupObject.websiteFK = getWebsitePK($webCombo.val());

	getWebsiteLocations();
}

function storeFormValues (websiteName, websiteLocationNameType, websiteLocationId, adSizeTypeName, height, width) {
	mieAdSauceSettings.websiteNameComboId = websiteName;
	mieAdSauceSettings.websiteLocationNameTypeComboId = websiteLocationNameType;
	mieAdSauceSettings.websiteLocationIdHiddenId = websiteLocationId;
	mieAdSauceSettings.adSizeTypeNameHiddenId = adSizeTypeName;
	mieAdSauceSettings.heightHiddenId = height;
	mieAdSauceSettings.widthHiddenId = width;
}

function getWebsitePK (websiteName) {
	for (var i = 0;i < mieAdSauceAdSetupObject.websites.length; i++) {
		if(mieAdSauceAdSetupObject.websites[i].Name == websiteName) 
			return mieAdSauceAdSetupObject.websites[i].Id;
	}

	return 0;
}

function populateDropDown ($dropDown, dataList, displayMember, valueMember) {
	var oldValue = $dropDown.val();
	var oldValueExist = false;

	$dropDown.html('');
	var innerHTML = '';

 	innerHTML += '<option value="null">(None Selected)</option>';

	for (var i = 0; i < dataList.length; i++) {
		var value = '';
		if(!(valueMember instanceof Array)){
			value = dataList[i][valueMember];
		} else {
			for (var j = 0; j < valueMember.length; j++) {
				if(j > 0)
					value += ', ';

				value += dataList[i][valueMember[j]];
			}
		}

		var display = '';
		if(!(displayMember instanceof Array)){
			display = dataList[i][displayMember];
		}
		else {
			for (var j = 0; j < displayMember.length; j++) {
				if(j > 0)
					display += ', ';

				display += dataList[i][displayMember[j]];
			}
		}

		innerHTML += '<option value="' + value + '">' + display + '</option>';
		if(value == oldValue && mieAdSauceSettings.oldValueLoaded == false) {
			oldValueExist = true;
			mieAdSauceSettings.oldValueLoaded = true;
		}

	}

	$dropDown.html(innerHTML);

	if(oldValueExist) {
		$dropDown.val(oldValue);

		updateWebsiteLocations();
	}
}

//API CALLS
function getAdSauceBearerToken ($username, $password) {
	var username = jQuery('#' + $username).val();
	var password = jQuery('#' + $password).val();

    blockUI('Logging In...');
	jQuery.ajax({
		type: 'POST',
		url: MyAjax.ajaxurl,
		data: {"action": "getBearerToken", "username": username, "password": password},
		success: function(response) {
			unblockUI();

			if(response.indexOf('error') != 0) {
				mieAdSauceAdSetupObject.bearerToken = response;

				getUserInfo();
			} else {
				alert(response);
			}
		},
		error: function(response){
			unblockUI();

			alert(response);
		}
	});
}

function getUserInfo() {
 	blockUI('Loading User Info...');
	jQuery.ajax({
		type: 'POST',
		url: MyAjax.ajaxurl,
		data: {"action": "getUserInfo", "bearerToken": mieAdSauceAdSetupObject.bearerToken},
		success: function(response) {
			unblockUI();

			if(response.indexOf('error') != 0) {
				var user = jQuery.parseJSON(response);

				mieAdSauceAdSetupObject.user = user;

				getWebsites();
			} else {
				alert(response);
			}
		},
		error: function(response){
			unblockUI();

			alert(response);
		}
	});	
}

function getWebsites() {
  	blockUI('Loading Websites...');
	jQuery.ajax({
		type: 'POST',
		url: MyAjax.ajaxurl,
		data: {"action": "getWebsites", "bearerToken": mieAdSauceAdSetupObject.bearerToken, "userPK": mieAdSauceAdSetupObject.user.Id},
		success: function(response) {
			unblockUI();

			if(response.indexOf('error') != 0) {
				var websites = jQuery.parseJSON(response);

				mieAdSauceAdSetupObject.websites = websites;

				setWebsitesComboBoxData();
			} else {
				alert(response);
			}
		},
		error: function(response){
			unblockUI();

			alert(response);
		}
	});	
}

function getWebsiteLocations() {
	blockUI('Loading Locations...');
	jQuery.ajax({
		type: 'POST',
		url: MyAjax.ajaxurl,
		data: {"action": "getWebsiteLocations", "bearerToken": mieAdSauceAdSetupObject.bearerToken, "websitePK": mieAdSauceAdSetupObject.websiteFK},
		success: function(response) {
			unblockUI();

			if(response.indexOf('error') != 0) {
				var websiteLocations = jQuery.parseJSON(response);

				mieAdSauceAdSetupObject.websiteLocations = websiteLocations;

				setWebsiteLocationsComboBoxData();
			} else {
				alert(response);
			}
		},
		error: function(response){
			unblockUI();

			alert(response);
		}
	});	
}
