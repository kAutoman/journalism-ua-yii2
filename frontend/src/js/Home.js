import $ from 'jquery'

import 'owl.carousel'

export default class Home {
  constructor () {
    this.nominationSlider()
    if (document.documentElement.clientWidth <= 768) {
      this.councilItems()
    }
    this.partnerSlider()
  }

  nominationSlider () {
    let nominationCarusel = $('.block_nomination .owl-carousel')
    let nominationCaruselPrev = $('.block_nomination .slider_prev')
    let nominationCaruselNext = $('.block_nomination .slider_next')

    if (nominationCarusel.length) {
      nominationCarusel.owlCarousel({
        loop: true,
        autoWidth: false,
        responsive: {
          0: {
            items: 1,
          },
          768: {
            items: 2,
          },
          1024: {
            items: 3,
          },
        },
      })

      nominationCaruselPrev.click(function (e) {
        e.preventDefault()
        nominationCarusel.trigger('prev.owl.carousel', [300])
      })

      nominationCaruselNext.click(function (e) {
        e.preventDefault()
        nominationCarusel.trigger('next.owl.carousel', [300])
      })
    }
  }

  councilItems () {
    let councilBody = $('.block_council .body')
    let councilBodyRow = $('.block_council .body .row')
    let councilBodyBtn = $('.block_council .body .btn')
    let currentIndex = 0
    let maxIndex = 0

    if (councilBody.length && councilBodyRow.length > 1) {
      let rows = councilBodyRow.toArray()

      rows.shift()

      maxIndex = rows.length

      $.each(rows, function (key, item) {
        $(item).hide()
      })

      councilBodyBtn.click(function (e) {
        e.preventDefault()

        let item = $(rows[currentIndex])

        if (item.length) {
          item.show()

          currentIndex++
        }

        if (currentIndex >= maxIndex) {
          $(this).hide()
        }
      })
    }
  }

  partnerSlider () {
    let partnerCarusel = $('.block_partner .owl-carousel')
    let partnerCaruselItem = $('.block_partner .owl-item')
    let partnerCaruselPrev = $('.block_partner .slider_prev')
    let partnerCaruselNext = $('.block_partner .slider_next')

    if (partnerCarusel.length) {
      partnerCarusel.owlCarousel({
        loop: true,
        autoWidth: false,
        responsive: {
          0: {
            items: 1,
          },
          768: {
            items: 2,
          },
          1024: {
            items: 4,
          },
        }
      })

      partnerCaruselPrev.click(function (e) {
        e.preventDefault()
        partnerCarusel.trigger('prev.owl.carousel', [300])
      })

      partnerCaruselNext.click(function (e) {
        e.preventDefault()
        partnerCarusel.trigger('next.owl.carousel', [300])
      })
    }
  }

  draggedSlider (event) {
    let partnerCaruselItem = $('.block_partner .owl-item')
    console.log(partnerCaruselItem)

    partnerCaruselItem.hover(function () {
      console.log('hover')
      $(this).find('.carusel_item').addClass('hover')
    }, function () {
      console.log('end hover')
      $(this).find('.carusel_item').removeClass('hover')
    })
  }
}
