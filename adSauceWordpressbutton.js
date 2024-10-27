(function() {
     /* Register the buttons */
     tinymce.create('tinymce.plugins.AdSauceButtons', {
          init : function(ed, url) {
               /**
               * Inserts shortcode content
               */
               ed.addButton( 'adSauce_toolBar_button', {
                    title : 'Insert AdSauce Shortcode',
                    image : '../wp-includes/images/smilies/icon_eek.gif',
                    cmd: 'adSauce_plugin_command'//,
               });
               // Called when we click the Insert Gistpen button
               ed.addCommand( 'adSauce_plugin_command', function() {
                    // Calls the pop-up modal
                    ed.windowManager.open({
                         // Modal settings
                         title: 'Insert AdSauce Shortcode',
                         width: 400,
                         // minus head and foot of dialog box
                         height: 400,
                         inline: 1,
                         id: 'plugin-slug-insert-dialog',
                         buttons: [{
                              text: 'Insert',
                              id: 'plugin-slug-button-insert',
                              class: 'insert',
                              onclick: function( e ) {
                                   adSauceInsertShortCode(ed);

                                   jQuery('#plugin-slug-button-cancel').click();
                              },
                         },
                         {
                              text: 'Cancel',
                              id: 'plugin-slug-button-cancel',
                              onclick: 'close'
                         }],
                    });

                    appendInsertDialog();

               });
          },
          createControl : function(n, cm) {
               return null;
          },
     });
     /* Start the buttons */
     tinymce.PluginManager.add( 'adSauce_button_script', tinymce.plugins.AdSauceButtons );

     function appendInsertDialog () {
          var dialogBody = jQuery( '#plugin-slug-insert-dialog-body' );

          // Get the form template from WordPress
          jQuery.post( ajaxurl, {
               action: 'adSauce_plugin_slug_insert_dialog'
          }, function( response ) {
               template = response;

               dialogBody.children( '.loading' ).remove();
               dialogBody.html( template );
               
               jQuery( '.spinner' ).hide();
          });
     }

     function adSauceInsertShortCode (ed) {
          var shortcode = ''
          if(mieAdSauceAdSetupObject.websiteLocation.AdSizeTypeName == 'Business Directory') {
               shortcode = 'iframe src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/directory/' + mieAdSauceAdSetupObject.websiteLocation.Id + '"';
               shortcode += ' style="width: 100%; height: 1000px;"';
          }
          else if(mieAdSauceAdSetupObject.websiteLocation.AdSizeTypeName == 'Social Message Board') {
               shortcode = 'iframe src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/messagewall/' + mieAdSauceAdSetupObject.websiteLocation.Id + '"';
               shortcode += ' style="width: 100%; height: 987px; min-width: 315px; max-width: 1260px;"'
          }
          else {
               shortcode = 'iframe src="https://tad.adsauce.co/adindex.html#/servicewebsitelocation/displayad/' + mieAdSauceAdSetupObject.websiteLocation.Id + '"';
               shortcode += ' style="border: none; padding: 0; margin: 0; width: ' + mieAdSauceAdSetupObject.websiteLocation.AdSizeType.Width + 'px; height: ' + mieAdSauceAdSetupObject.websiteLocation.AdSizeType.Height + 'px;"';
          }

          shortcode = '[' + shortcode + ']';

          ed.selection.setContent(shortcode);
     }

})();

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
     websiteNameComboId: 'adSauce_WebsiteName',
     websiteLocationNameTypeComboId: 'adSauce_websiteLocationNameType',
     websiteLocationIdHiddenId: 'adSauce_WebsiteLocationId',
     adSizeTypeNameHiddenId: 'adSauce_AdSizeTypeName',
     heightHiddenId: 'adSauce_Ad_Width',
     widthHiddenId: 'adSauce_Ad_Height',
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

//REUSED FUNCTIONS
function blockUI (message) {
     var top = (jQuery(window).height() / 2) - 15;
     jQuery('#plugin-slug-insert-dialog-body').append('<div id="blockUI" style="position: fixed; top: 0; left: 0; bottom: 0; right: 0; opacity: 0.6; background-color: black; z-index: 10000; cursor: wait;"><div style="margin: auto; text-align: center; width: 200px; height: 30px; margin-top: ' + top + 'px; background-color: black; opacity: 1;"><h1 style="color: white;">' + message + '</h1></div></div>');
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
          if(value == oldValue) oldValueExist = true;
     }

     $dropDown.html(innerHTML);

     if(oldValueExist) {
          $dropDown.val(oldValue);

          updateWebsiteLocations();
     }
}

function setCookie (bearerToken, username){
     var hours = 4;
     var d = new Date();
     d.setTime(d.getTime() + (hours*60*60*1000));
     var expires = "expires="+d.toUTCString();
     document.cookie = "adSauceBearerToken=" + bearerToken + ";" + expires;   
     document.cookie = "username=" + username + ";" + expires;
}

function getCookie (){
     var name = "adSauceBearerToken=";
     var username = "username=";
     var returnValue = {};
     var ca = document.cookie.split(';');
     for(var i=0; i<ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0)==' ') c = c.substring(1);
          if (c.indexOf(name) == 0) returnValue.adSauceBearerToken = c.substring(name.length,c.length);
          if (c.indexOf(username) == 0) returnValue.username = c.substring(username.length,c.length);
     }
     return returnValue;
}

//API CALLS
function getAdSauceBearerToken () {
     var username = jQuery('#adSauce_username').val();
     var password = jQuery('#adSauce_password').val();

     blockUI('Logging In...');
     jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {"action": "getBearerToken", "username": username, "password": password},
          success: function(response) {
               unblockUI();
               
               if(response.indexOf('error') != 0) {
                    mieAdSauceAdSetupObject.bearerToken = response;

                    setCookie(response, username);

                    jQuery("#adSauce_loggedInAs").html("Logged in as: " + username);
                    jQuery("#adSauce_loggedInAs").css("visibility", "visible");

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
          url: ajaxurl,
          data: {"action": "getUserInfo", "bearerToken": mieAdSauceAdSetupObject.bearerToken},
          success: function(response) {
               unblockUI();

               if(response != "")
               {
                    if(response.indexOf('error') != 0 && response.indexOf('"Message"') != 0) {
                         var user = jQuery.parseJSON(response);

                         mieAdSauceAdSetupObject.user = user;

                         getWebsites();
                    } else {
                         alert(response);

                         jQuery("#adSauce_loggedInAs").html("");
                         jQuery("#adSauce_loggedInAs").css("visibility", "hidden");
                    }
               }
               else {
                    alert("User not found, please log in again!");

                    jQuery("#adSauce_loggedInAs").html("");
                    jQuery("#adSauce_loggedInAs").css("visibility", "hidden");
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
          url: ajaxurl,
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
          url: ajaxurl,
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