;;;/* $Id: admin_menu.js,v 1.7.2.7.2.14 2009/07/22 20:40:22 sun Exp $ */

Drupal.admin = Drupal.admin || {};
Drupal.admin.behaviors = Drupal.admin.behaviors || {};
Drupal.admin.hashes = Drupal.admin.hashes || {};

/**
 * Core behavior for Administration menu.
 *
 * Test whether there is an administration menu is in the output and execute all
 * registered behaviors.
 */
Drupal.behaviors.adminMenu = function (context) {
  // Initialize settings.
  Drupal.settings.admin_menu = $.extend({
    suppress: false,
    margin_top: false,
    position_fixed: false,
    tweak_modules: false,
    tweak_tabs: false,
    destination: '',
    basePath: Drupal.settings.basePath,
    hash: 0,
    replacements: {}
  }, Drupal.settings.admin_menu || {});
  // Check whether administration menu should be suppressed.
  if (Drupal.settings.admin_menu.suppress) {
    return;
  }
  var $adminMenu = $('#admin-menu:not(.admin-menu-processed)', context);
  // Client-side caching; if administration menu is not in the output, it is
  // fetched from the server and cached in the browser.
  if (!$adminMenu.length && Drupal.settings.admin_menu.hash) {
    Drupal.admin.getCache(Drupal.settings.admin_menu.hash, function (response) {
      if (typeof response == 'string' && response.length > 0) {
        $('body', context).prepend(response);
      }
      var $adminMenu = $('#admin-menu:not(.admin-menu-processed)', context);
      // Apply our behaviors.
      Drupal.admin.attachBehaviors(context, Drupal.settings, $adminMenu);
    });
  }
  // If the menu is in the output already, this means there is a new version.
  else {
    // Apply our behaviors.
    Drupal.admin.attachBehaviors(context, Drupal.settings, $adminMenu);
  }
};

/**
 * Collapse fieldsets on Modules page.
 *
 * For why multiple selectors see #111719.
 */
Drupal.behaviors.adminMenuCollapseModules = function (context) {
  if (Drupal.settings.admin_menu.tweak_modules) {
    $('#system-modules fieldset:not(.collapsed), #system-modules-1 fieldset:not(.collapsed)', context).addClass('collapsed');
  }
};

/**
 * Apply margin to page.
 *
 * Note that directly applying marginTop does not work in IE. To prevent
 * flickering/jumping page content with client-side caching, this is a regular
 * Drupal behavior.
 */
Drupal.behaviors.adminMenuMarginTop = function (context) {
  if (!Drupal.settings.admin_menu.suppress && Drupal.settings.admin_menu.margin_top) {
    $('body:not(.admin-menu)', context).addClass('admin-menu');
  }
};

/**
 * Retrieve content from client-side cache.
 *
 * @param hash
 *   The md5 hash of the content to retrieve.
 * @param onSuccess
 *   A callback function invoked when the cache request was successful.
 */
Drupal.admin.getCache = function (hash, onSuccess) {
  if (Drupal.admin.hashes.hash !== undefined) {
    return Drupal.admin.hashes.hash;
  }
  $.ajax({
    cache: true,
    type: 'GET',
    dataType: 'text', // Prevent auto-evaluation of response.
    global: false, // Do not trigger global AJAX events.
    url: Drupal.settings.admin_menu.basePath.replace(/admin_menu/, 'js/admin_menu/cache/' + hash),
    success: onSuccess,
    complete: function (XMLHttpRequest, status) {
      Drupal.admin.hashes.hash = status;
    }
  });
}

/**
 * @defgroup admin_behaviors Administration behaviors.
 * @{
 */

/**
 * Attach administrative behaviors.
 */
Drupal.admin.attachBehaviors = function (context, settings, $adminMenu) {
  if ($adminMenu.length) {
    $adminMenu.addClass('admin-menu-processed');
    $.each(Drupal.admin.behaviors, function() {
      this(context, settings, $adminMenu);
    });
  }
};

/**
 * Apply 'position: fixed'.
 */
Drupal.admin.behaviors.positionFixed = function (context, settings, $adminMenu) {
  if (settings.admin_menu.position_fixed) {
    $adminMenu.css('position', 'fixed');
  }
};

/**
 * Move page tabs into administration menu.
 */
Drupal.admin.behaviors.pageTabs = function (context, settings, $adminMenu) {
  if (settings.admin_menu.tweak_tabs) {
    $('ul.tabs.primary li', context).addClass('admin-menu-tab').appendTo('#admin-menu-wrapper > ul');
    $('ul.tabs.secondary', context).appendTo('#admin-menu-wrapper > ul > li.admin-menu-tab.active');
    $('ul.tabs.primary', context).remove();
  }
};

/**
 * Perform dynamic replacements in cached menu.
 */
Drupal.admin.behaviors.replacements = function (context, settings, $adminMenu) {
  for (var item in settings.admin_menu.replacements) {
    $(item, $adminMenu).html(settings.admin_menu.replacements[item]);
  }
}

/**
 * Inject destination query strings for current page.
 */
Drupal.admin.behaviors.destination = function (context, settings, $adminMenu) {
  if (settings.admin_menu.destination) {
    $('a.admin-menu-destination', $adminMenu).each(function() {
      this.search += (!this.search.length ? '?' : '&') + Drupal.settings.admin_menu.destination;
    });
  }
}

/**
 * Apply JavaScript-based hovering behaviors.
 *
 * @todo This has to run last.  If another script registers additional behaviors
 *   it will not run last.
 */
Drupal.admin.behaviors.hover = function (context, settings, $adminMenu) {
  // Hover emulation for IE 6.
  if ($.browser.msie && parseInt(jQuery.browser.version) == 6) {
    $('li', $adminMenu).hover(function() {
      $(this).addClass('iehover');
    }, function() {
      $(this).removeClass('iehover');
    });
  }

  // Delayed mouseout.
  $('li', $adminMenu).hover(function() {
    // Stop the timer.
    clearTimeout(this.sfTimer);
    // Display child lists.
    $('> ul', this).css({left: 'auto', display: 'block'})
      // Immediately hide nephew lists.
      .parent().siblings('li').children('ul').css({left: '-999em', display: 'none'});
  }, function() {
    // Start the timer.
    var uls = $('> ul', this);
    this.sfTimer = setTimeout(function() {
      uls.css({left: '-999em', display: 'none'});
    }, 400);
  });
};

/**
 * @} End of "defgroup admin_behaviors".
 */

;;// $Id: block.js,v 1.2 2007/12/16 10:36:53 goba Exp $

/**
 * Move a block in the blocks table from one region to another via select list.
 *
 * This behavior is dependent on the tableDrag behavior, since it uses the
 * objects initialized in that behavior to update the row.
 */
Drupal.behaviors.blockDrag = function(context) {
  var table = $('table#blocks');
  var tableDrag = Drupal.tableDrag.blocks; // Get the blocks tableDrag object.

  // Add a handler for when a row is swapped, update empty regions.
  tableDrag.row.prototype.onSwap = function(swappedRow) {
    checkEmptyRegions(table, this);
  };

  // A custom message for the blocks page specifically.
  Drupal.theme.tableDragChangedWarning = function () {
    return '<div class="warning">' + Drupal.theme('tableDragChangedMarker') + ' ' + Drupal.t("The changes to these blocks will not be saved until the <em>Save blocks</em> button is clicked.") + '</div>';
  };

  // Add a handler so when a row is dropped, update fields dropped into new regions.
  tableDrag.onDrop = function() {
    dragObject = this;
    if ($(dragObject.rowObject.element).prev('tr').is('.region-message')) {
      var regionRow = $(dragObject.rowObject.element).prev('tr').get(0);
      var regionName = regionRow.className.replace(/([^ ]+[ ]+)*region-([^ ]+)-message([ ]+[^ ]+)*/, '$2');
      var regionField = $('select.block-region-select', dragObject.rowObject.element);
      var weightField = $('select.block-weight', dragObject.rowObject.element);
      var oldRegionName = weightField[0].className.replace(/([^ ]+[ ]+)*block-weight-([^ ]+)([ ]+[^ ]+)*/, '$2');

      if (!regionField.is('.block-region-'+ regionName)) {
        regionField.removeClass('block-region-' + oldRegionName).addClass('block-region-' + regionName);
        weightField.removeClass('block-weight-' + oldRegionName).addClass('block-weight-' + regionName);
        regionField.val(regionName);
      }
    }
  };

  // Add the behavior to each region select list.
  $('select.block-region-select:not(.blockregionselect-processed)', context).each(function() {
    $(this).change(function(event) {
      // Make our new row and select field.
      var row = $(this).parents('tr:first');
      var select = $(this);
      tableDrag.rowObject = new tableDrag.row(row);

      // Find the correct region and insert the row as the first in the region.
      $('tr.region-message', table).each(function() {
        if ($(this).is('.region-' + select[0].value + '-message')) {
          // Add the new row and remove the old one.
          $(this).after(row);
          // Manually update weights and restripe.
          tableDrag.updateFields(row.get(0));
          tableDrag.rowObject.changed = true;
          if (tableDrag.oldRowElement) {
            $(tableDrag.oldRowElement).removeClass('drag-previous');
          }
          tableDrag.oldRowElement = row.get(0);
          tableDrag.restripeTable();
          tableDrag.rowObject.markChanged();
          tableDrag.oldRowElement = row;
          $(row).addClass('drag-previous');
        }
      });

      // Modify empty regions with added or removed fields.
      checkEmptyRegions(table, row);
      // Remove focus from selectbox.
      select.get(0).blur();
    });
    $(this).addClass('blockregionselect-processed');
  });

  var checkEmptyRegions = function(table, rowObject) {
    $('tr.region-message', table).each(function() {
      // If the dragged row is in this region, but above the message row, swap it down one space.
      if ($(this).prev('tr').get(0) == rowObject.element) {
        // Prevent a recursion problem when using the keyboard to move rows up.
        if ((rowObject.method != 'keyboard' || rowObject.direction == 'down')) {
          rowObject.swap('after', this);
        }
      }
      // This region has become empty
      if ($(this).next('tr').is(':not(.draggable)') || $(this).next('tr').size() == 0) {
        $(this).removeClass('region-populated').addClass('region-empty');
      }
      // This region has become populated.
      else if ($(this).is('.region-empty')) {
        $(this).removeClass('region-empty').addClass('region-populated');
      }
    });
  };
};
;