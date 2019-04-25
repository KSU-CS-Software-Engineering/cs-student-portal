import * as dashboard from "../util/dashboard";

const DELETE_CONFIRMATION_MSG = "Are you sure? This will permanently remove all "
    + "requirements and repopulate them based on the selected degree program. "
    + "You cannot undo this action.";

export function init() {
    document.getElementById("save")
        .addEventListener("click", saveFlowchart);

    document.getElementById("delete")
        .addEventListener("click", deleteFlowchart);

    document.getElementById("repopulate")
        .addEventListener("click", resetFlowchart);
}

function saveFlowchart() {
    const data = {
        name: $("#name").val(),
        description: $("#description").val(),
        start_year: $("#start_year").val(),
        start_semester: $("#start_semester").val(),
        degreeprogram_id: $("#degreeprogram_id").val(),
    };
    const planId = $("#id").val();
    const studentId = $("#student_id").val();
    let url = `/flowcharts/edit/${planId}`;
    if (planId.length === 0) {
        url = `/flowcharts/new/${studentId}`;
    }
    dashboard.ajaxsave(data, url, planId);
}

function deleteFlowchart() {
    const studentId = $("#student_id").val();
    const url = "/flowcharts/delete";
    const retUrl = `/flowcharts/${studentId}`;
    const data = {
        id: $("#id").val(),
    };
    dashboard.ajaxdelete(data, url, retUrl, true);
}

function resetFlowchart() {
    const choice = confirm(DELETE_CONFIRMATION_MSG);
    if (choice === true) {
        const url = "/flowcharts/reset";
        const data = {
            id: $("#id").val(),
        };
        dashboard.ajaxsave(data, url, id);
    }
}
