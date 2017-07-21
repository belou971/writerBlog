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
        })
        .on('success.form.bv', function(e) {
            var $form        = $(e.target);     // Form instance

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
                console.log(result);
            }, 'json');
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

//Configuration of Bootstrap validator on div "divNewCat" with the addiction of a callback to check
// whether the new category already exists
$(document).ready(function() {
    $('#divNewCat').bootstrapValidator({
        button: {
            selector: '#addCategoryBtn'
            /*disabled: 'disabled'*/
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

    /*$(this).find('.btn').toggleClass('active');

    if ($(this).find('.btn-success').length>0) {
        $(this).find('.btn').toggleClass('btn-success');
    }

    $(this).find('.btn').toggleClass('btn-default');*/
    set_active_class($(this));
});

/*$('.btn-toggle').load(function() {
    set_active_class($(this));
});*/

$('form').submit(function(){
    alert($(this["options"]).val());
    return false;
});

function set_active_class($element) {
    $element.find('.btn').toggleClass('active');

    if ($element.find('.btn-success').length>0) {
        $element.find('.btn').toggleClass('btn-success');
    }

    $element.find('.btn').toggleClass('btn-default');
}

