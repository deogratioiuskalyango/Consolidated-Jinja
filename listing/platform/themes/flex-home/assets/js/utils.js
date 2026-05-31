const setCookie = (name, value, expiresDate) => {
    const date = new Date()
    let siteUrl = window.siteUrl

    if (!siteUrl.includes(window.location.protocol)) {
        siteUrl = window.location.protocol + siteUrl
    }

    let url = new URL(siteUrl)
    date.setTime(date.getTime() + expiresDate * 24 * 60 * 60 * 1000)
    const expires = 'expires=' + date.toUTCString()
    document.cookie = name + '=' + value + '; ' + expires + '; path=/' + '; domain=' + url.hostname
}

const getCookie = (name) => {
    const cookieName = name + '='
    const cookies = document.cookie.split(';')

    for (let i = 0; i < cookies.length; i++) {
        let c = cookies[i]
        while (c.charAt(0) === ' ') {
            c = c.substring(1)
        }
        if (c.indexOf(cookieName) === 0) {
            return c.substring(cookieName.length, c.length)
        }
    }
    return ''
}

const clearCookies = (name) => {
    let siteUrl = window.siteUrl

    if (!siteUrl.includes(window.location.protocol)) {
        siteUrl = window.location.protocol + siteUrl
    }

    let url = new URL(siteUrl)
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/' + '; domain=' + url.hostname
}

export { setCookie, getCookie, clearCookies }
