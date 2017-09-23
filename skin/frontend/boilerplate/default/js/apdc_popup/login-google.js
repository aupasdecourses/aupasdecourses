var GoogleAuth;
var SCOPE = 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';


function googleAjaxLogin(data)
{
  var ajaxUrl = jQuery('#connect_with_google').attr('href');
  apdcLoginPopup.showLoading();
  data.isAjax = 1;
  jQuery.ajax({
    url: ajaxUrl,
    data: data,
    type: 'POST'
  })
    .done(function(response) {
      if (typeof(response.redirect) !== 'undefined' && response.redirect !== '') {
        window.location.href = response.redirect;
      } else if (typeof(response.html) !== 'undefined' && response.html !== '') {  
        apdcLoginPopup.updateContent(response.html);
      }
      apdcLoginPopup.initPopupHeight();
    })
    .fail(function() {
      console.log('failed');
    });
  return false;
}

function googleConnectUser(user)
{
  var data = {};
  var isAuthorized = user.hasGrantedScopes(SCOPE);
  if (isAuthorized) {
    data.token = user.getAuthResponse();

    var basicProfile = user.getBasicProfile();
    data.fields = {
      id: basicProfile.getId(),
      email: basicProfile.getEmail(),
      family_name: basicProfile.getFamilyName(),
      given_name: basicProfile.getGivenName(),
      name: basicProfile.getName(),
      image_url: basicProfile.getImageUrl()
    };
  } else {
    data.error = 'not_authorized';
  }
  googleAjaxLogin(data);
}

function googleHandleAuthClick() {
  if (GoogleAuth.isSignedIn.get()) {
    var user = GoogleAuth.currentUser.get();
    googleConnectUser(user);
  } else {
    try {
      GoogleAuth.signIn().then(function(user) {
        googleConnectUser(user);
      });
    } catch(e) {
      console.log(e);
    }
  }
}


function initClient() {
  // Retrieve the discovery document for version 3 of Google Drive API.
  // In practice, your app can retrieve one or more discovery documents.
  var discoveryUrl = 'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest';

  // Initialize the gapi.client object, which app uses to make API requests.
  // Get API key and client ID from API Console.
  // 'scope' field specifies space-delimited list of access scopes.
  gapi.client.init({
    'apiKey': 'AIzaSyDBHp_RIREiifEZRugsvc49Y-939FPJHF4',
    'discoveryDocs': [discoveryUrl],
    'clientId': apdcGoogleLoginAppiId,
    'scope': SCOPE
  }).then(function () {
    GoogleAuth = gapi.auth2.getAuthInstance();

    // Handle initial sign-in state. (Determine if user is already signed in.)
    var user = GoogleAuth.currentUser.get();

    $(document).on('click', '#connect_with_google', function(e) {
      e.preventDefault();
      e.stopPropagation();
      googleHandleAuthClick();
      return false;
    }); 
  });
}

function googleHandleClientLoad() {
  // Load the API's client and auth2 modules.
  // Call the initClient function after the modules load.
  gapi.load('client:auth2', initClient);
}
