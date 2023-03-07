import $ from 'jquery'

import 'jquery-mask-plugin'

import 'jquery-validation/dist/jquery.validate'
import 'jquery-validation/dist/additional-methods'
import 'jquery-validation/dist/localization/messages_uk'

import 'jquery-custom-select'

export default class Submit {
  constructor () {
     $.ajax({
      url: 'http://127.0.0.2/nominations/index',
      method: 'GET',
      type: 'json',
      success: function (response) {
        let html = `<option value=""> Назва номінації </option>`;
        response.map( temp => {
            html += `<option value="${temp.label}">${ temp.label }</option>`;
        });
        $('#nomination').html(html);
        $('#nomination').customSelect();
        $('#material_type').customSelect();
        $('#gender').customSelect();

        $('#nomination').change(function () {
          console.log($('#' + this.id + '_field').get(0))
          $('#' + this.id + '_field').
            attr('value', $(this).find('option:selected').val())
        })

        $('#material_type').change(function () {
          console.log($('#' + this.id + '_field').get(0))
          $('#' + this.id + '_field').
            attr('value', $(this).find('option:selected').val())
        })

        $('#gender').change(function () {
          console.log($('#' + this.id + '_field').get(0))
          $('#' + this.id + '_field').
            attr('value', $(this).find('option:selected').val())
        })
      },
      error: function () {
        alert('server error!');
      },
    })
    

    $('input.phone-mask').mask('+38 (000) 000-00-00')

    $.validator.setDefaults({
      debug: true,
      success: 'valid',
    })

    jQuery.validator.addMethod('phone', function (value, element) {
      // allow any non-whitespace characters as the host part
      return this.optional(element) ||
        /^\+38 \(\d{3}\) \d{3}-\d{2}-\d{2}$/.test(value)
    }, 'Використовуйте правильний формат телефону +38 (xxx) xxx-xx-xx')

    $('.step1_form').validate({
      onfocusout: false,
      onkeyup: false,
      onclick: false,
      focusInvalid: false,
      invalidHandler: this.error,
      normalizer: true,
      rules: {
        name: {
          required: true,
        },
        email: {
          required: true,
          email: true,
        },
        gender: {
          required: true,
        },
        age: {
          required: true,
          digits: true,
          min: 16,
          max: 99,
        },
        city: {
          required: true,
        },
        company_name: {
          required: true,
        },
        position: {
          required: true,
        },
        phone: {
          required: true,
          phone: true,
        },
        experience: {
          required: true,
          digits: true,
          min: 0,
          max: 99,
        },
      },
    })

    $('.step2_form').validate({
      onfocusout: false,
      onkeyup: false,
      onclick: false,
      focusInvalid: false,
      invalidHandler: this.error,
      rules: {
        other_name: {
          required: false,
        },
        material_label: {
          required: true,
        },
        material_type: {
          required: true,
        },
        program_label: {
          required: true,
        },
        program_published_date: {
          required: true,
        },
        program_link: {
          required: true,
          url: true,
        },
        nomination: {
          required: true,
        },
        argument: {
          required: true,
        },
        awards: {
          required: true,
        },
      },
    })

    this.step1()
    this.step2()
  }

  step1 () {

    $('.form_step1 .btn-next').click(function (e) {
      e.preventDefault()

      let form = $('.step1_form')

      let gender = form.find('#gender').val()

      if (form.valid()) {
        $('.form-group').removeClass('error')

        $('.form_step2').show()
        $('.form_step1').hide()
        $(document).scrollTop(400)
      }
    })
  }

  step2 () {
    let that = this

    $('.form_step2 .btn-submit').click(function (e) {
      e.preventDefault()

      let form = $('.step2_form')

      if (form.valid()) {
        $('.form-group').removeClass('error')

        that.submit()
      }
    })
  }

  submit () {
    let step1Data = $('.step1_form').serializeArray()
    let step2Data = $('.step2_form').serializeArray()

    let data = step1Data.concat(step2Data)

    let that = this

    $.ajax({
      url: 'http://127.0.0.2/submit-request',
      method: 'POST',
      type: 'json',
      data: data,
      success: function (response) {
        that.afterSend()
      },
      error: function () {
        alert('Server error')
      },
    })
  }

  error (event, validator) {
    let errors = validator.errorMap

    $('.form-group').removeClass('error')

    $.each(errors, function (element, error) {
      $('.form-group[data-field=' + element + ']').addClass('error')
    })
  }

  afterSend () {
    if ($.session.set('show-popup', true)) {
      location.reload()
    }
  }
}
