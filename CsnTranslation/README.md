CsnTranslation
=======
Zend Framework 2 Module

To change local in view use
<a title="English" href="javascript:changeLocale('en_US')">English</a>
<script type="text/javascript">
    function changeLocale(value)
    { 
        var today = new Date();
        var oneYear = new Date(today.getTime() + 365 * 24 * 60 * 60);
        document.cookie = "locale=" + value + ";path=/;expires=" + oneYear.toGMTString();
        window.location.reload();
    }
</script>
----
If translations don't work, following must be set in .htaccess file
# For translations
<filesMatch "\.(php)$">
    FileETag None
    <ifModule mod_headers.c>
    Header unset ETag
    Header set Cache-Control "max-age=0, no-cache, no-store, must-revalidate"
    Header set Pragma "no-cache"
    Header set Expires "Wed, 11 Jan 1984 05:00:00 GMT"
    </ifModule>
</filesMatch>
----
TODO: create view helper for locales (Menu)
