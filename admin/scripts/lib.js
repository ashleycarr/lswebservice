
var fadeSpeed = 200;

function windowToggle(element)
{
    var makeVisible = false;
    var waitForFadeOut = false;

    if($(element).is(':hidden')) {
        makeVisible = true;
    }

    if(!$('#add').is(':hidden') || 
       !$('#edit').is(':hidden') ||
       !$('#search').is(':hidden')) {
        waitForFadeOut = true;

        $('#add').fadeOut(fadeSpeed);
        $('#edit').fadeOut(fadeSpeed);
        $('#search').fadeOut(fadeSpeed);
    }

    if(makeVisible == true) {
        if(waitForFadeOut) {
            $(element).delay(fadeSpeed).fadeIn(fadeSpeed);
        } else {
            $(element).fadeIn(fadeSpeed);
        }
        $(element).next('input').focus();
    }
}

function editProfessional(tableRow)
{
    var tableValues = [];

    $(tableRow).children('td').each(function() {
        tableValues.push($(this).text());
    });

    if($("#edit input[name='id']").val() == tableValues[0]) {
        $('#add').fadeOut(fadeSpeed);
        $('#search').fadeOut(fadeSpeed);
        if($('#edit').is(':hidden')) {
            $('#edit').fadeIn(fadeSpeed);
        } else {
            $('#edit').fadeOut(fadeSpeed);
        }
    } else {
        $("#edit input[name='id']").val(tableValues[0]);
        $("#edit input[name='name']").val(tableValues[1]);
        $("#edit input[name='addr1']").val(tableValues[2]);
        $("#edit input[name='addr2']").val(tableValues[3]);
        $('#edit select option[value=' + tableValues[4] + ']').attr('selected','selected');
        $("#edit input[name='pcode']").val(tableValues[5]);
        $("#edit input[name='phone']").val(tableValues[6]);
        $("#edit input[name='email']").val(tableValues[7]);

        if($('#edit').is(':hidden')) {
            windowToggle('#edit');
        }
    }
}