String.prototype.replaceAll = function(search, replacement) {
    var target = this;

    return target.split(search).join(replacement);
};

var Main = function() {
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById('back-to-top').style.display = "block";
        } else {
            document.getElementById('back-to-top').style.display = "none";
        }
    }

    window.onscroll = function() {
        scrollFunction();
    };

    $('#back-to-top').on('click', function() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });

    hljs.initHighlightingOnLoad();
};

