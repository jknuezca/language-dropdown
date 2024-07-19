jQuery(document).ready(function ($) {
   $('#switch-lang').on('click', function (e) {
      e.preventDefault();
      $('.ex-switch-lang-options').toggle();
   });

   $(document).on('click', function (e) {
      if (!$(e.target).closest('.ex-switch-lang').length) {
         $('.ex-switch-lang-options').hide();
      }
   });
});