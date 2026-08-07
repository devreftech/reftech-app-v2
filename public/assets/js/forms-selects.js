/**
 * Selects & Tags
 */

'use strict';

$(function () {
  const selectPicker = $('.selectpicker'),
    select2 = $('.select2'),
    select2Icons = $('.select2-icons'),
    select2Badge = $('.select2-badge');

  // Bootstrap Select
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    selectPicker.selectpicker();
  }

  // Select2
  // --------------------------------------------------------------------

  // Default
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      select2Focus($this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }

  // Select2 Icons
  if (select2Icons.length) {
    // custom template to render icons
    function renderIcons(option) {
      if (!option.id) {
        return option.text;
      }
      var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;

      return $icon;
    }
    select2Focus(select2Icons);
    select2Icons.wrap('<div class="position-relative"></div>').select2({
      templateResult: renderIcons,
      templateSelection: renderIcons,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }

  // Select2 Badge
  if (select2Badge.length) {
    // custom template to render Badge
    function renderBadge(option) {
      if (!option.id) {
        return option.text;
      }
      var $badge = "<span class='" + $(option.element).data('badge') + " me-2'></span>";

      return $badge;
    }
    select2Focus(select2Badge);
    select2Badge.wrap('<div class="position-relative"></div>').select2({
      templateResult: renderBadge,
      templateSelection: renderBadge,
      escapeMarkup: function (es) {
        return es;
      }
    });
  }
});
