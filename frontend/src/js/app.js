import $ from 'jquery'

import 'owl.carousel'

import './vendor/jquery.session'

import '@fancyapps/fancybox'

import Home from './Home'
import Submit from './Submit'

import '../css/main.scss'

var indexPage = require('../index.twig')

window.onload = function () {
  document.body.classList.add('loaded')

  $('li:has(a.active)').addClass('active')

  $(document).on('click', '.mobile_toggle', function (e) {
    e.preventDefault()

    $([this, '.block_header', '.header-nav-menu-mobile']).toggleClass('open')

    $('.header-nav-menu-mobile.open').height($(document).height())

    $('body').toggleClass('on-scroll')
  })

  if ($.session.get('show-popup')) {
    $.session.remove('show-popup')

    $.fancybox.open($('.popup').html(), {
      touch: false,
      keyboard: false,
      arrows: false,
      infobar: false,
      afterShow: function () {
        let popup = this
        setTimeout(function () {
          $.fancybox.close()
        }, 2000)
      },
    })
  }

  new Home()
  new Submit()
}
