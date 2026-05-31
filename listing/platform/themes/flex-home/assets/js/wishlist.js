;(function ($) {
    'use strict'
    let __ = function (key) {
        window.trans = window.trans || {}

        return window.trans[key] !== 'undefined' && window.trans[key] ? window.trans[key] : key
    }

    $(document).ready(function () {
        setWishListCount()

        $(document).on('click', '.add-to-wishlist', function (e) {
            e.preventDefault()

            const currentTarget = $(e.currentTarget)

            const cookieName = currentTarget.data('type') === 'property' ? 'wishlist' : 'project_wishlist'
            const id = currentTarget.data('id')
            const wishCookies = decodeURIComponent(getCookie(cookieName))
            let arrWList = []

            if (id != null && id !== 0 && id !== undefined) {
                // Case 1: Wishlist cookies are undefined
                if (wishCookies === undefined || wishCookies == null || wishCookies === '') {
                    arrWList.push({ id: id })
                    currentTarget.find('i').removeClass('far fa-heart').addClass('fas fa-heart')

                    Theme.showSuccess(__('Added to wishlist successfully!'))
                    setCookie(cookieName, JSON.stringify(arrWList), 60)
                } else {
                    const item = { id: id }
                    arrWList = JSON.parse(wishCookies)
                    const index = arrWList
                        .map((e) => e.id)
                        .indexOf(item.id)

                    if (index === -1) {
                        arrWList.push(item)
                        clearCookies(cookieName)
                        setCookie(cookieName, JSON.stringify(arrWList), 60)

                        currentTarget.find('i')
                            .removeClass('far fa-heart')
                            .addClass('fas fa-heart')

                        Theme.showSuccess(__('Added to wishlist successfully!'))
                    } else {
                        arrWList.splice(index, 1)
                        clearCookies(cookieName)
                        setCookie(cookieName, JSON.stringify(arrWList), 60)
                        currentTarget.find('i')
                            .removeClass('fas fa-heart')
                            .addClass('far fa-heart')

                        Theme.showSuccess(__('Removed from wishlist successfully!'))
                    }
                }
            }

            let countWishlist = JSON.parse(getCookie(cookieName)).length

            $('.wishlist-count').text(countWishlist)
            setWishListCount()
        })

        function setWishListCount() {
            const propertyCookieName = 'wishlist'
            const projectCookieName = 'project_wishlist'
            const propertyWishListCookies = JSON.parse(decodeURIComponent(getCookie(propertyCookieName)) || '[]')
            const projectWishListCookies = JSON.parse(decodeURIComponent(getCookie(projectCookieName)) || '[]')

            $('.wishlist-count').text(propertyWishListCookies.length + projectWishListCookies.length)

            if (!!propertyWishListCookies) {
                propertyWishListCookies.map((item) => {
                    $(`.add-to-wishlist[data-type="property"][data-id="${item.id}"]`).find('i').removeClass('far fa-heart').addClass('fas fa-heart')
                })
            }

            if (!!projectWishListCookies) {
                projectWishListCookies.map((item) => {
                    $(`.add-to-wishlist[data-type="project"][data-id="${item.id}"]`).find('i').removeClass('far fa-heart').addClass('fas fa-heart')
                })
            }
        }

        function setCookie(cname, cvalue, exdays) {
            let d = new Date()
            let siteUrl = window.siteUrl

            if (!siteUrl.includes(window.location.protocol)) {
                siteUrl = window.location.protocol + siteUrl
            }

            let url = new URL(siteUrl)
            d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000)
            let expires = 'expires=' + d.toUTCString()
            document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/' + '; domain=' + url.hostname
        }

        function getCookie(cname) {
            let name = cname + '='
            let ca = document.cookie.split(';')
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i]
                while (c.charAt(0) == ' ') {
                    c = c.substring(1)
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length)
                }
            }
            return ''
        }

        function clearCookies(name) {
            let siteUrl = window.siteUrl

            if (!siteUrl.includes(window.location.protocol)) {
                siteUrl = window.location.protocol + siteUrl
            }

            let url = new URL(siteUrl)
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/' + '; domain=' + url.hostname
        }
    })
})(jQuery)
