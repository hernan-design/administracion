const $ = (typeof window !== 'undefined' ? (window.jQuery || window.$) : null);
if ($ && !$.default) {
    try {
        $.default = $;
    } catch (e) {}
}

export const fn = $ ? $.fn : undefined;
export const extend = $ ? $.extend : undefined;
export const each = $ ? $.each : undefined;
export const inArray = $ ? $.inArray : undefined;
export const isArray = $ ? $.isArray : undefined;
export const trim = $ ? $.trim : undefined;
export const type = $ ? $.type : undefined;
export const Deferred = $ ? $.Deferred : undefined;

export default $;
