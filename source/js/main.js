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

    $('button.copy-to-clipboard').on('click', async function () {
      const copyElementId = $(this).data('copyElementId');
      const copyElement = document.getElementById(copyElementId);

      // Collect text from each child, separated by newlines
      let copyText = '';
      if (copyElement) {
        const children = Array.from(copyElement.childNodes);
        copyText = children.map(node => {
          if (node.nodeType === Node.TEXT_NODE) {
            return node.textContent;
          } else if (node.nodeType === Node.ELEMENT_NODE) {
            return node.textContent;
          }
          return '';
        }).filter(Boolean).join('\n');
      }

      await navigator.clipboard.writeText(copyText);
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
