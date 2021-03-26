function toggleSignUp(e){
    e.preventDefault();
    $('#logreg-forms .form-signin').toggle(); // display:block or none
    $('#logreg-forms .form-signup').toggle(); // display:block or none
}

function toggleStatistics(e){
    console.log("toggle stat");
    e.preventDefault();
    $('#statisticsTable').toggle(); // display:block or none
}

function toggleInfo(e){
    console.log("toggle info");
    e.preventDefault();
    $('#infoTable').toggle(); // display:block or none
}

$(()=>{
    // Login Register Form
    $('#logreg-forms #btn-signup').click(toggleSignUp);
    $('#logreg-forms #cancel_signup').click(toggleSignUp);
    $('#statisticsButton').click(toggleStatistics);
    $('#infoButton').click(toggleInfo);
})
