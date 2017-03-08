//Use AJAX for the main page application 
//https://www.youtube.com/watch?v=pKKIs0eyv8M

var session = {
    teacher : {
        id : teacherId
    },
    group : {
        id : undefined,
        name : ''
    },
    ressource : {
        id : undefined,
        name : ''
    },
    students : [],
    allStudents :[]
}

//---- Event to check the if only one check box is selected disable other ---
$(document).on('click', 'input[type="checkbox"]', function() {
    var checkboxidentifier = '.'+$(this).attr('class').split(' ')[0];
    $(checkboxidentifier).not(this).prop('checked', false);
})

// --- Click Event Manage The Dropdown  ---

$("#arrow").click(function(){
    $("ul.students").toggle();
});

$("#student").click(function(){
    $("ul.students").toggle();
});

//--- Manage if one field is open or not ---
$(document).click(function(e){
    var res = false
    if($(e.target).is('div #course-nav') ||$(e.target).is('div .menu-title')||$(e.target).parents().is('div .reports-content')||$(e.target).is('section')||$(e.target).is('div #btn-submit')) {
        res = false
        $("ul.students").hide();
    }
    $('.students-menu').on({
        'hide.bs.dropdown' : function (e) {
            return res;
        }
    });
});

// --- Function Change State Of The Field (Enable, Disable, Empty) ---
function changeFieldState(field){

    if(field == 'student'){

        if(session.students.length != 0){
            var last_student = session.students[session.students.length-1];
            $('#student').text(last_student.lastName+' '+last_student.firstName);
            $('.open-reports').removeClass('disabled');
        } else {
            $('#student').text(studentDefaultField);
            $('.open-reports'). addClass('disabled');
        }

    } else if (field == 'ressource'){

        $('.students-menu').removeClass('disabled');
        $('ul.students li').remove();

    } else if (field == 'group'){

        $('.students-menu').addClass('disabled');
        $('.ressources-menu').removeClass('disabled');
        $('ul.ressources li').remove();
        $('.open-reports').addClass('disabled');

    }
}

// --- Function to remove a value from the json array ---
function removeItem(array, property, value) {
    array.forEach(function(result, index) {
        if(result[property] == value) {
            //Remove from arra
            array.splice(index, 1);
        }
    });
}

// --- Function Fill Session With Datas Of The Student Selected ---
function getStudent(student){
    var studentjson = {
        'lastName' : $(student).data('lastname'),
        'firstName' : $(student).data('firstname'),
        'id': $(student).data('id')
    }
    if($(student).data('selected')){
        //$('.student_'+$(student).data('id')).addClass('listselected')
        $('.student_'+$(student).data('id')).css('background-color','#ffffff');
        $('.student_'+$(student).data('id')).css('color','#555555');
        $(student).data('selected',false)
        if(session.students.length == 1){
            session.students = [];
        } else {
            removeItem(session.students,'id',$(student).data('id'));
        }

    } else {
        //$('.student_'+$(student).data('id')).addClass('listunselected')
        $('.student_'+$(student).data('id')).css('background-color','#4c58a6');
        $('.student_'+$(student).data('id')).css('color','white');
        $(student).data('selected',true);

        session.students.push(studentjson);
    }

    changeFieldState('student');

};

// --- AJAX Filled The Field Student Based On RessourceId And GroupId ---
function getStudentByRessourceAndGroup(ressource){


    session.ressource.name = $(ressource).data('name');
    session.ressource.id = $(ressource).data('id');
    session.students = [];
    $('#checkall').show()

    $('#ressource').text(session.ressource.name);
    $('#student').text(studentDefaultField);

    var dataObject = JSON.stringify(session);

    $.ajax({
        type:'POST',
        url: routingStudentsByGroupAndRessource,
        data:{session:dataObject},
        beforeSend : function () {
            //console.log('chargement des étudiants');
        },
        error: function(xhr){
            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        },
        success: function(datas) {

            changeFieldState('ressource');

            var myList = $('ul.students')

            session.allStudents =[]

            $.each(datas, function(i)
            {
                var studentjson = {
                    'lastName' : datas[i].lastName,
                    'firstName' : datas[i].firstName,
                    'id': datas[i].id
                }
                session.allStudents.push(studentjson)
                var li = $('<li/>')
                    .addClass('student_'+datas[i].id)
                    .attr('onclick', 'getStudent(this)')
                    .attr('data-firstname',datas[i].firstName)
                    .attr('data-lastname',datas[i].lastName)
                    .attr('data-id',datas[i].id)
                    .attr('data-selected',false)
                    .appendTo(myList);

                var blockUrl = $('<a/>')
                    .addClass('student_'+datas[i].id)
                    .text(datas[i].lastName+' '+datas[i].firstName)
                    .appendTo(li);

            });
        }

    })
};

// --- AJAX Filled The Field Ressource Based On GroupId ---
function getRessourcesByGroup(group){

    session.group.name = $(group).data('name');
    session.group.id = $(group).data('id');
    session.students = [];
    $('#checkall').hide()

    $('#study-class').text(session.group.name);
    $('#ressource').text(ressourceDefaultField);
    $('#student').text(studentDefaultField);

    var dataObject = JSON.stringify(session);

    $.ajax({

        type:'POST',
        url: routingRessourcesByGroup,
        data:{session:dataObject},
        beforeSend : function () {
            //console.log('chargement des ressources');
        },
        error: function(xhr){
            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        },
        success: function(datas) {

            changeFieldState('group');

            var myList = $('ul.ressources')
            $.each(datas, function(i)
            {
                var li = $('<li/>')
                    .addClass('ressource_'+datas[i].courseCid)
                    .attr('onclick', 'getStudentByRessourceAndGroup(this)')
                    .attr('data-name',datas[i].courseTitle)
                    .attr('data-id',datas[i].courseCid)
                    .appendTo(myList);

                var blockUrl = $('<a/>')
                    .text(datas[i].courseTitle)
                    .appendTo(li);
            });
        }

    })
};


// --- AJAX Filled The Field Group Based On TeacherId ---
$("document").ready(function(){

    var dataObject = JSON.stringify(session);
    console.log(dataObject);
    $('#checkall').hide()

        $.ajax({
        type: "POST",
        url: routingGroupsByTeacher,
        data:{session:dataObject},
        xhrFields: {
                withCredentials: true
        },
        crossDomain: true,

        beforeSend : function (xhr, data) {
            xhr.withCredentials = true;
        },
        error: function(xhr){
            alert('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
        },
        success: function(datas) {

            //see if there is some groups
            //if there is no groups display error message
            //else display list of groups
            if(datas.length == 0 && role == 'TEACHER'){
                $('#message').text("Vous n'avez aucun rapport à éditer");
                $('.classes-menu').addClass('disabled');

            } else {
                var myList = $('ul.groups')
                $.each(datas, function (i) {
                    var li = $('<li/>')
                        .addClass('group_' + datas[i].id)
                        .attr('onclick', 'getRessourcesByGroup(this)')
                        .attr('data-name', datas[i].name)
                        .attr('data-id', datas[i].id)
                        .appendTo(myList);
                    var blockUrl = $('<a/>')
                        .text(datas[i].name)
                        .appendTo(li);
                });
            }
        }
    })
});

// --- AJAX Send Session To The Controller And Generate Table ---
function getTable(){

    $('.table').css('visibility','visible');

    $('.myCheckboxAll').prop('checked', false);

    if(!$('#btn-submit').is('.disabled')) {
        //remove old elment of the table and hide default content
        $('tbody tr').remove();
        $('#content-panel').addClass('hidden');

        var students = session.students;
        var myList = $('tbody')

        //Construction of the Table
        $.each(students, function (i) {

            var tr = $('<tr/>')
                .addClass('name_' + students[i].id)
                .appendTo(myList);

            var tableRow = $('tr.name_' + students[i].id);

            $.each(tabletitle, function (j) {

                if (tabletitle[j] == 'Prénom/Nom') {
                    var td = $('<td/>')
                        .addClass('fullname_' + students[i].id)
                        .addClass('text-center')
                        .attr('data-fullnameid', students[i].id)
                        .text(students[i].lastName + ' ' + students[i].firstName)
                        .appendTo(tableRow);

                } else if (tabletitle[j] == 'Préalable') {
                    var td = $('<td/>')
                        .addClass('checkbox1_' + students[i].id)
                        .addClass('text-center')
                        .attr('data-priorid', students[i].id)
                        .appendTo(tableRow);

                    var checkbox = $('td.checkbox1_' + students[i].id);

                    var input = $('<input/>')
                        .addClass('prior_' + students[i].id)
                        .addClass('text-center')
                        .attr('title', help)
                        .attr('type', 'checkbox')
                        .attr('id', 'checkbox1_' + students[i].id)
                        .attr('data-priorid', students[i].id)
                        .appendTo(checkbox);

                } else if (tabletitle[j] == 'Finale') {
                    var td = $('<td/>')
                        .addClass('checkbox2_' + students[i].id)
                        .addClass('text-center')
                        .attr('data-priorid', students[i].id)
                        .appendTo(tableRow);

                    var checkbox = $('td.checkbox2_' + students[i].id);

                    var input = $('<input/>')
                        .addClass('prior_' + students[i].id)
                        .addClass('text-center')
                        .attr('title', help)
                        .attr('type', 'checkbox')
                        .attr('id', 'checkbox2_' + students[i].id)
                        .attr('data-priorid', students[i].id)
                        .appendTo(checkbox);

                } else {
                    if (tabletitle[j] == 'Tout') {
                        var td = $('<td/>')
                            .addClass('data' + j + '_' + students[i].id)
                            .attr('data-result', students[i].id)
                            .appendTo(tableRow);
                    } else {
                        var td = $('<td/>')
                            .addClass('data' + j + '_' + students[i].id)
                            .attr('data-result', students[i].id)
                            .appendTo(tableRow);
                    }

                    var tabledata = $('td.data' + j + '_' + students[i].id);

                    var res = linkpdf.replace("/-1", "");

                    var link = $('<a/>')
                        .addClass('link' + j + '_' + students[i].id)
                        .attr('onclick', 'changeCheckboxValues(this)')
                        .attr('href', res + (j - 2) + '/' + students[i].id)
                        .appendTo(tabledata);

                    var blockUrl = $('a.link' + j + '_' + students[i].id);

                    var link = $('<img/>')
                        .addClass('img' + j + '_' + students[i].id)
                        .addClass('center-block')
                        .attr('src', srcpdfpicture)
                        .attr('alt', 'pdf-picture' + j)
                        .attr('style', 'vertical-align: middle;')
                        .appendTo(blockUrl);

                }

            });

        });

        //reset the list
        //remove ol li selected
        session.students = [];
        $("ul.students li").each(function(i){
            $("."+$(this).attr('class')).attr('data-selected',false);
            $("."+$(this).attr('class')).css('background-color','#ffffff');
            $("."+$(this).attr('class')).css('color','#555555');
            $($(this)).data('selected',false);
        });
        $('#student').text(studentDefaultField);
        $('.open-reports'). addClass('disabled');

    }
}

//--- Function add in the url if the checkbox is checked or not ---
//Change the value of the link before is submit
function changeCheckboxValues(checkbox) {

    var url = $(checkbox).attr("href");
    url = url.split('/');
    var linkpdfLength = linkpdf.split('/').length;
    if(url.length<=linkpdfLength){
        var id = url[url.length-1];
        $(checkbox).attr("href", $(checkbox).attr("href")+'/'+$('#checkbox1_'+id).is(":checked")+'/'+$('#checkbox2_'+id).is(":checked")+'/'+session.ressource.id)
    } else {
        var url = $(checkbox).attr("href");
        url = url.split('/');
        var newUrl ='';

        //we get all element of the old url exept the  two checkbox values (2)
        for(i=1;i<url.length-3;i++){
            newUrl+= '/'+url[i];
        };

        //we get the id of the actual url
        var id = url[url.length-4];

        newUrl+= '/'+$('#checkbox1_'+id).is(":checked")+'/'+$('#checkbox2_'+id).is(":checked")+'/'+session.ressource.id;
        $(checkbox).attr("href", newUrl)
    }
}

//--- Button check if the checkbox all is selected or not
$('.myCheckboxAll').click(function() {
    if ($(this).is(':checked'))
    {
        session.allStudents.forEach(function (element) {
            $('.student_'+element.id).css('background-color','#4c58a6');
            $('.student_'+element.id).css('color','white');
            $('li.student_'+element.id).data('selected',true);
        });
        session.students = session.allStudents;
        changeFieldState('student');
    } else {
        session.allStudents.forEach(function (element) {
            $('.student_'+element.id).css('background-color','#ffffff');
            $('.student_'+element.id).css('color','#555555');
            $('li.student_'+element.id).data('selected',false);
        });
        session.students = [];
        changeFieldState('student');
    }
})