String::capitalize = ->
  @substr(0, 1).toUpperCase() + @substr(1)

$ ->
  $('#input-category').change ->
    value = $(@).val()

    if value == ""
      changeSubcats [ ]
    else if value == '1'
      changeSubcats [{name: '', value: '' },
                     {name: 'cards', value: 1},
                     {name: 'autographs', value: 2},
                     {name: 'sports equipment', value: 3}]
    else if value == '2'
      changeSubcats [{name: '', value: ''},
                     {name: 'autographs', value: 4},
                     {name: 'records', value: 5 },
                     {name: 'toys', value: 6} ]
    else if value == '3'
      changeSubcats [{name: '', value: ''},
                     {name: 'U.S.', value: 7},
                     {name: 'international', value: 8} ]
    else if value == '4'
      changeSubcats [{name: '', value: ''},
                     {name: 'antique', value: 9},
                     {name: '1980s', value: 10},
                     {name: 'current day', value: 11},
                     {name: 'metal', value: 12} ]


changeSubcats = (subcats)->
  $('#input-subcategory').empty()
  for subcat in subcats
    option = "<option value='#{subcat['value']}'>#{subcat['name'].capitalize()}</option>"
    $('#input-subcategory').append option
