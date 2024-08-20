export default function () {
  function scrollFunction() {
    if (
      document.body.scrollTop > 20 ||
      document.documentElement.scrollTop > 20
    ) {
      document.getElementById('back-to-top').style.display = 'block';
    } else {
      document.getElementById('back-to-top').style.display = 'none';
    }
  }

  window.onscroll = function () {
    scrollFunction();
  };

  $('#back-to-top').on('click', function () {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
  });

  $(document).ready(function () {
    $('button[role="tab"]').on('click', function () {
        const tabId = $(this).attr('aria-controls');
        const tabContent = $('#' + tabId);

        $(this).closest('[role=tablist]').find('button[role="tab"]')
            .attr('data-active', null)
            .attr('aria-selected', 'false');

        $(this).attr('data-active', 'true').attr('aria-selected', 'true');

        $(this).closest('[role=tablist]').parent().find('div[role="tabpanel"]').hide();
        tabContent.show();
    });

    //
    // // Global
    // $('div.configuration-block li').each(function () {
    //   var str = $(':first', $(this)).html();
    //   $(':first ', $(this)).html('');
    //   $(':first ', $(this)).append('<a href="#">' + str + '</a>');
    //   $(':first', $(this)).bind('click', function () {
    //     $('[class^=highlight-]', $(this).parents('ul')).hide();
    //     $('li', $(this).parents('ul')).removeClass('selected');
    //     $(this).parent().addClass('selected');
    //
    //     var block = $('[class^=highlight-]', $(this).parent('li'));
    //     block.show();
    //     block.parents('ul').height(block.height() + 40);
    //     return false;
    //   });
    // });
    //
    // $('div.configuration-block').each(function () {
    //   $('li:first', $(this)).addClass('selected');
    // });

    $('button.copy-to-clipboard').on('click', function () {
      var copyElementId = $(this).data('copy-element-id');

      var copyText = $('#' + copyElementId + ' .code-line').text();

      var element = document.createElement('textarea');
      element.value = copyText;
      document.body.appendChild(element);
      element.select();
      document.execCommand('copy');
      document.body.removeChild(element);
    });

    if (window.ga && window.ga.create) {
      $('a').on('click', function () {
        var eventCategory = $(this).data('ga-category');
        var eventAction = $(this).data('ga-action');
        var eventLabel = $(this).data('ga-label');
        var eventValue = $(this).data('ga-value');
        var fieldsObject = $(this).data('ga-fields-object');

        if (eventCategory && eventAction) {
          ga(
            'send',
            'event',
            eventCategory,
            eventAction,
            eventLabel,
            eventValue,
            fieldsObject,
          );
        }
      });
    }
  });
}
