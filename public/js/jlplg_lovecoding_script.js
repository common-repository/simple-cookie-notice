window.addEventListener('DOMContentLoaded', (event) => {
    function jlplgSetCookieConsent(name, value, options) {
        const opts = {
            path: "/",
            ...options
        }

        if (navigator.cookieEnabled) { // check if cookies are enabled
            const cookieName = encodeURIComponent(name);
            const cookieVal = encodeURIComponent(value);
            let cookieText = cookieName + "=" + cookieVal;

            if (opts.days && typeof opts.days === "number") {
                const data = new Date();
                data.setTime(data.getTime() + (opts.days * 24*60*60*1000));
                cookieText += "; expires=" + data.toUTCString();
            }

            if (opts.path) {
                cookieText += "; path=" + opts.path;
            }
            if (opts.domain) {
                cookieText += "; domain=" + opts.domain;
            }

            cookieText += "; Cache-Control=" + "no-cache";

            document.cookie = cookieText;
        }
    }

    function jlplgSetCookieConsentOnClick() {
        const button = document.querySelector('.jlplg-lovecoding-cookie-accept-button');

        if (button != null) {
            let expireTime = button.getAttribute('data-expire');
            if (typeof expireTime !== 'string') {
                expireTime = 30;
            }
            button.addEventListener('click', function(e) {
                e.preventDefault();
                jlplgSetCookieConsent("jlplg-cookies-accepted", "yes", { days: parseInt(expireTime), path: "/" });
                document.querySelector('.jlplg-lovecoding-cookie-info-container').classList.add('jlplg-hidden');
            });
        }
    }

    function jlplgGetCookie(name) {
        if (document.cookie !== "") {
            const cookies = document.cookie.split(/; */);

            for (let cookie of cookies) {
                const [ cookieName, cookieVal ] = cookie.split("=");
                if (cookieName === decodeURIComponent(name)) {
                    return decodeURIComponent(cookieVal);
                }
            }
        }

        return undefined;
    }

    function jlplgShowCookieConsent() {
        const jlplgCookie = jlplgGetCookie("jlplg-cookies-accepted");

        if (jlplgCookie != 'yes') {
            document.querySelector('.jlplg-lovecoding-cookie-info-container').classList.remove('jlplg-hidden');
        }
    }

    jlplgShowCookieConsent();
    jlplgSetCookieConsentOnClick();
});
