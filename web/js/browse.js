/**
 * Created by belou on 29/06/2017.
 */

/* ************************************************************************* */
/* ***                             Post Form                             *** */
/*           Events on fields of form in aim to make form dynamic            */
/* ************************************************************************* */

$('.browse').on('click', function(){
    $('.file').trigger('click');
});

$('.file').on('change', function(){
    $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
});


$('.category').on('click', function(){
    var $newCat = $('#divNewCat');
    if ($newCat.css('display') === 'block') {
        $newCat.css('display', 'none');
    } else {
        $newCat.css('display', 'block');
    }
});


/* ************************************************************************* */
/* ***                        Post Form Validation                       *** */
/*                              Fields checking                              */
/* ************************************************************************* */

//Application of Bootstrap validator on title field of new post form
//checking that:
// 1. the title is not empty
// 2. The size of the title is greater than 5 characters
$(document).ready(function() {
    $('#newPostForm').bootstrapValidator({
            // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                titlePost: {
                    validators: {
                        stringLength: {
                            min: 5,
                            message: 'Veuillez entrer un titre de 5 caractères minimum'
                        },
                        notEmpty: {
                            message: 'Le titre est obligatoire'
                        }
                    }
                }
            }
        });
});

//Function to check if the label in parameter already exists in the list of category
function existCategory(label)
{
    var bexist = false,
        categorie_values = $("#categories").children("option");

    categorie_values.each(function(id, element) {
        bexist = ($(element).val() === label);
        if(bexist === true) {
            return !bexist;
        }
    });

    return !bexist;
}

function existCategoryInDB($label, $url)
{
    $element = $('.personal_validation');
    $.post($url, {'newCategory': $label})
        .done(function ($data) {
            if($data.cat_counter !== 0) {
                $element.append($data.message);
                $element.css('display', 'block');
            } else {
                $element.removeData().append($data.message);
                $element.css('display', 'none');
            }
        })
        .fail(function($data){
            $element.append($data.message);
            $element.css('display', 'block');
        });
}

//Configuration of Bootstrap validator on div "divNewCat" with the addiction of a callback to check
// whether the new category already exists
$(document).ready(function() {
    $('#divNewCat').bootstrapValidator({
        button: {
            selector: '#addCategoryBtn'
        },
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            inputCategory: {
                validators: {
                    notEmpty: {
                        message: 'Entrer un libellé'
                    },
                    callback: {
                        message: 'Impossible, ce libellé existe déjà',
                        callback: function (value, validator, $field) {
                            return existCategory(value);
                        }
                    }
                }
            }
        }
    })
});

//Change color state of status buttons on a click event associated to these buttons
$('.btn-toggle').click(function() {
    set_active_class($(this));
});

function set_active_class($element) {
    $element.find('.btn').toggleClass('active');

    if ($element.find('.btn-success').length>0) {
        $element.find('.btn').toggleClass('btn-success');
    }

    $element.find('.btn').toggleClass('btn-default');
}

//
$('#addCategoryBtn').click(function(event){
    event.preventDefault();

    //check whether the input of new category label is empty
    var $inputNewCategory = $('#inputCategory').val();
    if($inputNewCategory.length == 0) {
        $('.personal_validation').removeData()
            .append("Attention entrer un libellé")
            .css('display', 'block');
    }
    else {
        existCategoryInDB($inputNewCategory, "/admin/existCategory")
    }
        //existCategoryInDB($in$inputNewCategory);
    //}
});