function ApdcQuickView(requirements) {
  this.requirements = requirements;
  this.checked = false;
}

ApdcQuickView.prototype.checkRequirements = function(callback, testNum) {
  if (!this.checked) {
    if (typeof(testNum) === 'undefined') {
      testNum = 0;
    }
    if (testNum < this.requirements.length) {
      var requirement = this.requirements[testNum];
      var self = this;
      if (!this.testIfLoaded(requirement)) {
        jQuery.getScript(
          requirement.url,
          function() {
            self.checkRequirements(callback, ++testNum);
          }
        );
      } else {
        this.checkRequirements(callback, ++testNum);
      }
    } else {
      this.checked = true;
      if (typeof(callback) === 'function') {
        callback();
      }
    }
  } else {
    if (typeof(callback) === 'function') {
      callback();
    }
  }
};

ApdcQuickView.prototype.testIfLoaded = function(requirement) {
  if (requirement.type === 'js') {
    return this.testScript(requirement.test);
  } else if (requirement.type === 'css') {
    this.testStylesheet(requirement);
  }
  return true;
};

/**
 * @parama array test 
 */
ApdcQuickView.prototype.testScript = function(test) {
  var currentTest = window;
  for (var i=0; i < test.length; ++i) {
    if (typeof(currentTest[test[i]]) === 'undefined') {
      return false;
    }
    currentTest = currentTest[test[i]];
  }
  return true;
};

ApdcQuickView.prototype.testStylesheet = function(requirement) {
  if (!jQuery('link[href="' + requirement.url + '"]').length) {
    jQuery('<link href="' + requirement.url + '" rel="stylesheet">').appendTo("head");
  }
  return true;
};
